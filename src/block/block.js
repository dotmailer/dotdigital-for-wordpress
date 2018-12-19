/**
 * BLOCK: dd-block
 *
 * Registering a basic block with Gutenberg.
 * Simple block, renders and saves the same content without any interactivity.
 */

//  Import CSS.
import './style.scss';
import './editor.scss';

const { __ } = wp.i18n; // Import __() from wp.i18n
const { registerBlockType } = wp.blocks; // Import registerBlockType() from wp.blocks
const el = wp.element.createElement;
const Components = wp.components;
var $ = require('jQuery');

import { SelectControl } from '@wordpress/components';
import { withState } from '@wordpress/compose';

/**
 * Register: aa Gutenberg Block.
 *
 * Registers a new block provided a unique name and an object defining its
 * behavior. Once registered, the block is made editor as an option to any
 * editor interface where blocks are implemented.
 *
 * @link https://wordpress.org/gutenberg/handbook/block-api/
 * @param  {string}   name     Block name.
 * @param  {Object}   settings Block settings.
 * @return {?WPBlock}          The block, if it has been successfully
 *                             registered; otherwise `undefined`.
 */
registerBlockType( 'cgb/block-dd-block', {
	// Block name. Block names must be string that contains a namespace prefix. Example: my-plugin/my-custom-block.
	title: __( 'dd-block - Dotdigital Block' ), // Block title.
	icon: 'shield', // Block icon from Dashicons → https://developer.wordpress.org/resource/dashicons/.
	category: 'common', // Block category — Group blocks together based on common traits E.g. common, formatting, layout widgets, embed.
	keywords: [
		__( 'dd-block — Dotdigital Block' ),
		__( 'CGB Example' ),
		__( 'create-guten-block' ),
	],
	attributes: {
		id: {
			type: 'string',
		}
	},

	/**
	 * The edit function describes the structure of your block in the context of the editor.
	 * This represents what the editor will render when the block is used.
	 *
	 * The "edit" property must be a valid function.
	 *
	 * @link https://wordpress.org/gutenberg/handbook/block-api/block-edit-save/
	 */
    edit: function( props ) {
        var id = props.attributes.id || '',
			focus = props.focus;
        var retval = [];

        	const MySelectControl = () => (
        		<SelectControl
					label={ __( 'Select a survey:' ) }
					value={ id } // e.g: value = [ 'a', 'c' ]
					onChange={ (survey) =>props.setAttributes({
						id: survey
					})}
					options={ [
							{ value: "https://r1.dotmailer-surveys.com/204mlv7a-9e3jrsda", label: 'Survey 1' },
							{ value: "https://r1.dotmailer-surveys.com/204mlv7a-8a3jrt2e", label: 'Survey 2' },
							{ value: "https://r1.dotmailer-surveys.com/204mlv7a-9e3jrsda", label: 'Survey 3' },
					] }
					/>
			);
            retval.push(
                 el( MySelectControl )
			);
			if(props.attributes.id) {
				$('#inspector-select-control-1').closest('.editor-block-list__block-edit').append('<iframe src="'+props.attributes.id+'"></iframe>');
			}
        return retval;
	},

	save: function( props ) {
		return (
			<div>
				<p>— Hello from the frontend.</p>
				<p>
		Dotdigital block: <code>dd-block</code> is a new Gutenberg block.
				</p>
				<p>
					It was created via{ ' ' }
					<code>
						<a href="https://github.com/ahmadawais/create-guten-block">
							create-guten-block
						</a>
					</code>.
				</p>
			</div>
		);
	},
} );
