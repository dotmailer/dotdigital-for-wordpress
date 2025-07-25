/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { useState, useEffect, Element as WPElement } from '@wordpress/element';
import { ToggleControl, RangeControl, SelectControl, Panel, PanelBody, PanelRow } from '@wordpress/components';

/**
 * Editor styles.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';

/**
 * Api fetch.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-api-fetch/
 */
import apiFetch from '@wordpress/api-fetch';
import { addQueryArgs } from '@wordpress/url';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @param {Object}   args
 * @param {Object}   args.attributes
 * @param {Function} args.setAttributes
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */
const Edit = ( { attributes, setAttributes } ) => {
	const [ options, setOptions ] = useState( [] );

	const restAPIPath = '/dotdigital/v1/surveys';

	useEffect( () => {
		fetchData(); // Fetch data when the component mounts
		previewBlockContent( attributes );
	}, [ attributes ] );

	const fetchData = async () => {
		const queryParams = {};
		try {
			apiFetch( { path: addQueryArgs( restAPIPath, queryParams ) } ).then( ( data ) => {
				setOptions( data );
			} );
		} catch ( error ) {
			// eslint-disable-next-line no-console
			console.error( 'Error fetching data:', error );
		}
	};

	const previewBlockContent = ( blockAttributes ) => {
		// Update the preview content based on block attributes
		const previewContainer = document.getElementById( blockAttributes.selectedOption );

		if ( previewContainer ) {
			if ( blockAttributes.selectedOption ) {
				previewContainer.innerHTML = `
				<div style="border: 1px solid #ccc; padding: 10px;">
					<iframe class="dd-preview" src="${ blockAttributes.selectedOption }"></iframe>
				</div>
      		`;
			} else {
				previewContainer.innerHTML = `
				<div></div> `;
			}
		}
	};

	return (
		<>
			<InspectorControls>
				<Panel>
					<PanelBody title="Configuration" initialOpen={ true }>
						<PanelRow>
							<SelectControl
								label="Style"
								value={ attributes.formStyle }
								options={ [
									{ label: 'Embedded', value: 'embedded' },
									{ label: 'Popover', value: 'popover' },
								] }
								onChange={ ( newValue ) => setAttributes( { formStyle: newValue } ) }
							/>
						</PanelRow>
						<div className={ attributes.formStyle === 'popover' ? `dd-popover` : `dd-embedded` } >
							<PanelRow>
								<RangeControl
									label="Show after"
									value={ typeof attributes.showAfter === 'undefined' ? 0 : attributes.showAfter }
									onChange={ ( newValue ) => setAttributes( { showAfter: newValue } ) }
									min={ 0 }
									max={ 10 }
								/>
							</PanelRow>
							<PanelRow>
								<SelectControl
									label="Stop Displaying"
									value={ attributes.stopDisplaying }
									options={ [
										{ label: 'When the form is closed', value: 'fc' },
										{ label: 'Only when the form is completed', value: 'uc' },
									] }
									onChange={ ( newValue ) => setAttributes( { stopDisplaying: newValue } ) }
								/>
							</PanelRow>
							<PanelRow>
								<ToggleControl
									label="Show on mobile devices (not recommended)"
									checked={ attributes.showMobile }
									onChange={ ( newValue ) => setAttributes( { showMobile: newValue } ) }
								/>
							</PanelRow>
							<PanelRow>
								<ToggleControl
									label="Enable use of 'esc' to dismiss pop-over"
									checked={ attributes.useEsc }
									onChange={ ( newValue ) => setAttributes( { useEsc: newValue } ) }
								/>
							</PanelRow>
							<PanelRow>
								<RangeControl
									label="Dialog width"
									value={ typeof attributes.dialogWidth === 'undefined' ? 600 : attributes.dialogWidth }
									onChange={ ( newValue ) => setAttributes( { dialogWidth: newValue } ) }
									min={ 0 }
									max={ 2000 }
								/>
							</PanelRow>
						</div>
					</PanelBody>
				</Panel>
			</InspectorControls>
			<div { ...useBlockProps() }>
				<SelectControl
					label="Select a form"
					value={ attributes.selectedOption }
					options={ options }
					onChange={ ( newValue ) => setAttributes( { selectedOption: newValue } ) }
				/>
				<div id={ attributes.selectedOption } className={ 'block-preview' }></div>
			</div>
		</>
	);
};

export default Edit;
