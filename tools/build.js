/* eslint-disable no-console */
/**
 * Block build script.
 *
 * Replaces `wp-scripts build`. For every directory in `blocks/` that contains a
 * `block.json`, it will:
 *
 *   - bundle `index.js` (JSX + ESM) with esbuild, treating `@wordpress/*` and
 *     `react/jsx-runtime` as WordPress-provided globals,
 *   - compile any `.scss` files imported from JS (`editor.scss` -> `index.css`,
 *     `style.scss` -> `style-index.css`) and generate the `-rtl` variants,
 *   - generate `index.asset.php` with the detected script dependencies,
 *   - copy `block.json` and `render.php` to the output directory.
 *
 * Usage: node tools/build.js [--watch]
 */

const fs = require( 'fs' );
const path = require( 'path' );
const crypto = require( 'crypto' );
const esbuild = require( 'esbuild' );
const sass = require( 'sass' );
const rtlcss = require( 'rtlcss' );

const ROOT = path.resolve( __dirname, '..' );
const SRC_DIR = path.join( ROOT, 'blocks' );
const OUT_DIR = path.join( ROOT, 'build' );

const WATCH = process.argv.includes( '--watch' );

/**
 * Packages that ship inside the bundle rather than being provided by WordPress.
 */
const BUNDLED_PACKAGES = [
	'@wordpress/icons',
	'@wordpress/interface',
	'@wordpress/style-engine',
	'@wordpress/undo-manager',
];

/**
 * Non-@wordpress externals provided by WordPress.
 */
const EXTERNAL_MAP = {
	react: { global: [ 'React' ], handle: 'react' },
	'react-dom': { global: [ 'ReactDOM' ], handle: 'react-dom' },
	'react/jsx-runtime': { global: [ 'ReactJSXRuntime' ], handle: 'react-jsx-runtime' },
	jquery: { global: [ 'jQuery' ], handle: 'jquery' },
	lodash: { global: [ 'lodash' ], handle: 'lodash' },
	moment: { global: [ 'moment' ], handle: 'moment' },
};

/**
 * Convert a kebab-case string to camelCase.
 *
 * @param {string} value Kebab-cased string.
 * @return {string} camelCased string.
 */
function camelCase( value ) {
	return value.replace( /-([a-z])/g, ( _match, letter ) => letter.toUpperCase() );
}

/**
 * Resolve an import path to a WordPress global + script handle, if applicable.
 *
 * @param {string} request Import path.
 * @return {?{global: string[], handle: string}} External descriptor, or null.
 */
function getExternal( request ) {
	if ( EXTERNAL_MAP[ request ] ) {
		return EXTERNAL_MAP[ request ];
	}

	if ( ! request.startsWith( '@wordpress/' ) || BUNDLED_PACKAGES.includes( request ) ) {
		return null;
	}

	const slug = request.slice( '@wordpress/'.length );

	// Sub-path imports (e.g. @wordpress/foo/bar) are not provided as globals.
	if ( slug.includes( '/' ) ) {
		return null;
	}

	return {
		global: [ 'wp', camelCase( slug ) ],
		handle: `wp-${ slug }`,
	};
}

/**
 * esbuild plugin mapping WordPress packages to runtime globals and collecting
 * the resulting script handles.
 *
 * @param {Set<string>} handles Set collecting discovered script handles.
 * @return {Object} esbuild plugin.
 */
function wordPressExternalsPlugin( handles ) {
	return {
		name: 'wordpress-externals',
		setup( build ) {
			build.onResolve( { filter: /.*/ }, ( args ) => {
				const external = getExternal( args.path );

				if ( ! external ) {
					return null;
				}

				handles.add( external.handle );

				return { path: args.path, namespace: 'wp-global' };
			} );

			build.onLoad( { filter: /.*/, namespace: 'wp-global' }, ( args ) => {
				const { global: globalPath } = getExternal( args.path );
				const accessor = globalPath
					.map( ( segment ) => `[ ${ JSON.stringify( segment ) } ]` )
					.join( '' );

				return {
					contents: `module.exports = window${ accessor };`,
					loader: 'js',
				};
			} );
		},
	};
}

/**
 * esbuild plugin that strips `.scss` imports from JS and records them so they
 * can be compiled separately.
 *
 * @param {string[]} stylesheets Array collecting absolute stylesheet paths.
 * @return {Object} esbuild plugin.
 */
function styleCollectorPlugin( stylesheets ) {
	return {
		name: 'style-collector',
		setup( build ) {
			build.onResolve( { filter: /\.(scss|sass|css)$/ }, ( args ) => ( {
				path: path.resolve( args.resolveDir, args.path ),
				namespace: 'stylesheet',
			} ) );

			build.onLoad( { filter: /.*/, namespace: 'stylesheet' }, ( args ) => {
				if ( ! stylesheets.includes( args.path ) ) {
					stylesheets.push( args.path );
				}

				return { contents: '', loader: 'js' };
			} );
		},
	};
}

/**
 * Compile a list of stylesheets into a single CSS file plus its RTL variant.
 *
 * @param {string[]} sources  Absolute paths of stylesheets to compile.
 * @param {string}   outFile  Absolute path of the CSS file to write.
 */
function buildStylesheet( sources, outFile ) {
	if ( ! sources.length ) {
		return;
	}

	const css = sources
		.map( ( source ) => sass.compile( source, { style: 'compressed' } ).css )
		.join( '\n' );

	fs.writeFileSync( outFile, css );

	const rtl = rtlcss.process( css );
	fs.writeFileSync( outFile.replace( /\.css$/, '-rtl.css' ), rtl );
}

/**
 * Build a single block directory.
 *
 * @param {string} blockName Directory name inside `blocks/`.
 */
async function buildBlock( blockName ) {
	const srcDir = path.join( SRC_DIR, blockName );
	const outDir = path.join( OUT_DIR, blockName );
	const entry = path.join( srcDir, 'index.js' );

	if ( ! fs.existsSync( entry ) ) {
		throw new Error( `Missing entry point: ${ entry }` );
	}

	fs.mkdirSync( outDir, { recursive: true } );

	const handles = new Set();
	const stylesheets = [];

	const result = await esbuild.build( {
		entryPoints: [ entry ],
		outfile: path.join( outDir, 'index.js' ),
		bundle: true,
		minify: true,
		format: 'iife',
		target: [ 'es2020' ],
		jsx: 'automatic',
		platform: 'browser',
		logLevel: 'warning',
		write: false,
		plugins: [
			wordPressExternalsPlugin( handles ),
			styleCollectorPlugin( stylesheets ),
		],
		loader: {
			'.js': 'jsx',
			'.json': 'json',
			'.svg': 'dataurl',
			'.png': 'dataurl',
		},
	} );

	const [ output ] = result.outputFiles;
	fs.writeFileSync( path.join( outDir, 'index.js' ), output.contents );

	// Editor styles (anything that isn't `style.*`) -> index.css
	buildStylesheet(
		stylesheets.filter( ( file ) => ! /(^|\/)style\.(scss|sass|css)$/.test( file ) ),
		path.join( outDir, 'index.css' )
	);

	// Shared styles (`style.*`) -> style-index.css
	buildStylesheet(
		stylesheets.filter( ( file ) => /(^|\/)style\.(scss|sass|css)$/.test( file ) ),
		path.join( outDir, 'style-index.css' )
	);

	// index.asset.php
	const version = crypto
		.createHash( 'md5' )
		.update( output.contents )
		.digest( 'hex' )
		.slice( 0, 20 );
	const dependencies = [ ...handles ]
		.sort()
		.map( ( handle ) => `'${ handle }'` )
		.join( ', ' );

	fs.writeFileSync(
		path.join( outDir, 'index.asset.php' ),
		`<?php return array('dependencies' => array(${ dependencies }), 'version' => '${ version }');\n`
	);

	// Static files consumed by register_block_type().
	for ( const file of [ 'block.json', 'render.php' ] ) {
		const from = path.join( srcDir, file );
		if ( fs.existsSync( from ) ) {
			fs.copyFileSync( from, path.join( outDir, file ) );
		}
	}

	console.log( `✓ built block "${ blockName }"` );
}

/**
 * Build every block found in `blocks/`.
 */
async function buildAll() {
	const blocks = fs
		.readdirSync( SRC_DIR, { withFileTypes: true } )
		.filter(
			( entry ) =>
				entry.isDirectory() &&
				fs.existsSync( path.join( SRC_DIR, entry.name, 'block.json' ) )
		)
		.map( ( entry ) => entry.name );

	if ( ! blocks.length ) {
		throw new Error( `No blocks found in ${ SRC_DIR }` );
	}

	for ( const block of blocks ) {
		await buildBlock( block );
	}
}

( async () => {
	try {
		await buildAll();

		if ( WATCH ) {
			console.log( 'Watching for changes…' );
			let timer = null;
			fs.watch( SRC_DIR, { recursive: true }, () => {
				clearTimeout( timer );
				timer = setTimeout( () => {
					buildAll().catch( ( error ) => console.error( error ) );
				}, 100 );
			} );
		}
	} catch ( error ) {
		console.error( error );
		process.exit( 1 );
	}
} )();

