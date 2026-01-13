/* global dmRecaptchaData, grecaptcha */
window.dmRecaptchaWidgets = window.dmRecaptchaWidgets || [];

// Add widgets from localized data
if ( typeof dmRecaptchaData !== 'undefined' && dmRecaptchaData.widgets ) {
	window.dmRecaptchaWidgets = window.dmRecaptchaWidgets.concat( dmRecaptchaData.widgets );
}

function dmRenderRecaptcha( widgetId, siteKey ) {
	if ( typeof grecaptcha === 'undefined' ) {
		return;
	}

	grecaptcha.ready( function() {
		grecaptcha.execute( siteKey, { action: 'signup_form' } ).then( function( token ) {
			const inputId = 'recaptcha_response_' + widgetId;
			const input = document.getElementById( inputId );

			if ( input ) {
				input.value = token;
			}
		} );
	} );
}

function processDmRecaptchaWidgets() {
	window.dmRecaptchaWidgets.forEach( function( widget ) {
		dmRenderRecaptcha( widget.id, widget.siteKey );
	} );
}

document.addEventListener( 'DOMContentLoaded', function() {
	processDmRecaptchaWidgets();
} );

if ( document.readyState === 'complete' || document.readyState === 'interactive' ) {
	processDmRecaptchaWidgets();
}
