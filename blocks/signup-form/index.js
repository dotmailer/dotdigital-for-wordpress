/**
 * Block 1.
 *
 * @see https://developer.wordpress.org/block-editor/how-to-guides/block-tutorial/writing-your-first-block-type/
 */

import { registerBlockType } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import Edit from './edit';

import metadata from './block.json';

/**
 * Every block starts by registering a new block type definition.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
registerBlockType( metadata,
	{
		/**
		 * @see ./edit.js
		 */
		edit: Edit,
		icon: {
			src: <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
				<path
					d="M16,2.78A13.22,13.22,0,1,1,2.78,16,13.23,13.23,0,0,1,16,2.78M16,0A16,16,0,1,0,32,16,16,16,0,0,0,16,0Z"
					fill="#000"
				/>
				<path
					d="M16,8.29A7.74,7.74,0,1,1,8.26,16,7.75,7.75,0,0,1,16,8.29m0-2.78A10.52,10.52,0,1,0,26.52,16,10.52,10.52,0,0,0,16,5.51Z"
					fill="#000"
				/>
				<path
					d="M16,13.77A2.26,2.26,0,1,1,13.75,16,2.26,2.26,0,0,1,16,13.77M16,11a5,5,0,1,0,5,5,5,5,0,0,0-5-5Z"
					fill="#000"
				/>
			</svg>,
		},
	}
);
