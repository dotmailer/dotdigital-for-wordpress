( function( $ ) {
	'use strict';

	const toggle_inputs = ( row ) => {
		const element = $( row );
		const checkbox = element.find( 'input[toggle-row-inputs]' );
		const checked = checkbox.is( ':checked' );

		$( row ).find( 'input' ).each( function() {
			const input = $( this );
			if ( input.is( checkbox ) ) {
				return;
			}
			if ( checked ) {
				input.removeAttr( 'disabled' );
			}
			if ( ! checked ) {
				input.attr( 'disabled', 'disabled' );
			}
		} );
	};

	$( '.multiselector' ).change( function() {
		const element = $( this );
		const table_body = element.closest( 'table' ).find( 'tbody' );

		$( table_body ).find( '.toggle-inputs' ).each( function() {
			const row = $( this );
			const row_checkbox = $( row ).find( 'input[toggle-row-inputs]' );

			if ( element.is( ':checked' ) ) {
				row_checkbox.prop( 'checked', true );
			} else {
				row_checkbox.prop( 'checked', false );
			}

			toggle_inputs( row );
		} );
	} );

	$( `input[toggle-row-inputs]` ).on(
		'change',
		( event ) => toggle_inputs( event.target.closest( 'tr' ) )
	);

	$( '.form-group-radio' ).change( function() {
		const element = $( this );
		const group = element.closest( '.radio-selection-group' );
		const form = element.closest( 'form' );
		$( group ).attr( 'data-selected', element.val() );
		$( form ).find( '.radio-selection-group' ).each( function() {
			const radio_group = $( this );

			if ( radio_group.is( group ) ) {
				radio_group.find( 'input,select' ).each( function() {
					const input = $( this );
					input.removeAttr( 'disabled' );
				} );
				return;
			}

			radio_group.removeAttr( 'data-selected' );
			radio_group.find( 'input,select' ).each( function() {
				if ( $( this ).is( 'input[type="radio"]' ) ) {
					return;
				}
				const input = $( this );
				input.attr( 'disabled', 'disabled' );
			} );
		} );
	} );

	document.getElementById( 'filterInput' )?.addEventListener( 'input', function() {
		const filter = this.value.toLowerCase();
		const records = document.querySelectorAll( '.filter-list > tr > .list-column ' );

		records.forEach( function( cell ) {
			if ( cell.textContent.toLowerCase().includes( filter ) ) {
				cell.closest( 'tr' ).classList.remove( 'hidden' );
			} else {
				cell.closest( 'tr' ).classList.add( 'hidden' );
			}
		} );
	} );
}( jQuery ) );
