const wordpress = require( '@wordpress/eslint-plugin' );
const wordpressConfigs = wordpress.configs;
const globals = require( 'globals' );

module.exports = [
	// Replaces .eslintignore
	{
		ignores: [
			'**/*.min.js',
			'**/*.build.js',
			'**/node_modules/**',
			'**/vendor/**',
			'build/**',
			'coverage/**',
			'cypress/**',
			'tools/**',
		],
	},
	// WordPress recommended flat config
	...wordpressConfigs[ 'recommended-with-formatting' ],
	// Project-specific overrides
	{
		languageOptions: {
			globals: {
				...globals.browser,
				jQuery: 'readonly',
				tinymce: 'readonly',
				tinyMCE: 'readonly',
				tinyMCEPreInit: 'readonly',
				wpApiSettings: 'readonly',
			},
		},
		rules: {
			'linebreak-style': 0,
			camelcase: 'off',
			// @wordpress/* packages are provided by WordPress at runtime, not npm
			'import/no-unresolved': [
				'error',
				{ ignore: [ '^@wordpress/' ] },
			],
			'import/no-extraneous-dependencies': [
				'error',
				{
					peerDependencies: true,
					packageDir: __dirname,
				},
			],
		},
		settings: {
			'import/ignore': [ '^@wordpress/' ],
		},
	},
];

