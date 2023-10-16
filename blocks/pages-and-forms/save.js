/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps } from '@wordpress/block-editor';

/**
 * The save function defines the way in which the different attributes should
 * be combined into the final markup, which is then serialized by the block
 * editor into `post_content`.
 *
 * @param {Object} args
 * @param {Object} args.attributes
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#save
 *
 * @return {WPElement|void} Element to render.
 */
export default function save( { attributes } ) {
	const parseUrl = ( form_url ) => {
		const url = document.createElement( 'a' );
		url.href = form_url;
		return url;
	};

	const getFormId = ( form_id ) => {
		return form_id.replace( '/p/', '' );
	};

	const { selectedOption, formStyle } = attributes;

	if ( ! selectedOption ) {
		return;
	}
	const form_url = parseUrl( selectedOption );
	const domain = form_url.hostname;
	const scriptPath = formStyle === 'popover' ? '/resources/sharing/popoverv2.js?' : '/resources/sharing/embed.js?';

	const form_data = formStyle === 'popover' ? {
		sharing: 'lp-popover',
		domain: form_url.hostname,
		id: getFormId( form_url.pathname ),
		delay: attributes.showAfter ?? 0,
		mobile: attributes.showMobile ?? '',
		keydismiss: attributes.useEsc ?? '',
		width: attributes.dialogWidth ?? 600,
		appearance: attributes.stopDisplaying === 'uc' ? 'uc' : '',
	} : {
		sharing: 'lp-embedded',
		domain: form_url.hostname,
		id: getFormId( form_url.pathname ),
	};

	const script_vars = new URLSearchParams( form_data );
	return (
		<script { ...useBlockProps.save() } src={ '//' + domain + scriptPath + script_vars }></script>
	);
}
