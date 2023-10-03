<?php

function shutdown() {
	$error = error_get_last();
	if ( isset( $error ) && $error['type'] === E_ERROR ) {
		echo 'There was a problem: ' . $error['message'];
	}
}

/**
 * @param array $posts
 *
 * @return array
 */
function returnRequiredFields( $posts = array() ) {
	$required = array();
	foreach ( $posts as $field_id => $field ) {
		if ( $field[2] == 'required' ) {
			$required[ $field_id ] = $field;
		}
	}
	return $required;
}

register_shutdown_function( 'shutdown' );

/**
 * Sort descending.
 *
 * @param object $a Object A.
 * @param object $b Object B.
 * @return int
 */
function dotdigital_item_sort_desc( object $a, object $b ) {
	if ( ! method_exists( $a, 'getName' ) || ! method_exists( $b, 'getName' ) ) {
		return 0;
	}

	$a_name = strtolower( $a->getName() );
	$b_name = strtolower( $b->getName() );

	if ( $a_name === $b_name ) {
		return 0;
	}

	return $a_name > $b_name ? -1 : 1;
}

/**
 * Sort ascending.
 *
 * @param object $a Object A.
 * @param object $b Object B.
 * @return int
 */
function dotdigital_item_sort_asc( object $a, object $b ) {
	if ( ! method_exists( $a, 'getName' ) || ! method_exists( $b, 'getName' ) ) {
		return 0;
	}

	$a_name = strtolower( $a->getName() );
	$b_name = strtolower( $b->getName() );

	if ( $a_name === $b_name ) {
		return 0;
	}

	return $a_name > $b_name ? 1 : -1;
}

function clean( $data ) {
	$trimmed = trim( $data );
	$stripped = strip_tags( $trimmed );
	$clean = htmlspecialchars( $stripped );
	return $clean;
}

function writeFormLine( $fieldType, $fieldName, $fieldWording, $required ) {
	if ( $required == 'true' ) {
		$required = 'required';
		$asterisk = '*';
	} else {
		$required = 'optional';
		$asterisk = '';
	}

	switch ( $fieldType ) {
		case 'date':
			echo "<label style='display:block;' for='{$fieldName}'>$fieldWording$asterisk</label>";
			echo "<input style='display:block;' class='$fieldType' type='date' id='$fieldName' name='datafields[{$fieldName}][]'/>";
			echo "<input type='hidden' id='$fieldName' name='datafields[{$fieldName}][]' value='{$fieldType}'/>";
			echo "<input type='hidden' id='$required' name='datafields[{$fieldName}][]' value='{$required}'/>";
			break;
		case 'boolean':
			echo "<span style='display:block;'>$fieldWording$asterisk</span>";
			echo "<input class='radio $required' type='radio' id='yes' name='datafields[{$fieldName}][]' value='TRUE' checked/>";
			echo "<label for=''>Yes</label><br>";
			echo "<input class='radio' type='radio' id='no' name='datafields[{$fieldName}][]' value='FALSE'/>";
			echo "<label for=''>No</label><br>";
			echo "<input type='hidden' id='$fieldName' name='datafields[{$fieldName}][]' value='radio'/>";
			echo "<input type='hidden' id='$required' name='datafields[{$fieldName}][]' value='{$required}'/>";
			break;
		case 'string':
			echo "<label style='display:block;' for='{$fieldName}'>$fieldWording$asterisk</label>";
			echo "<input style='display:block;' class='$fieldType' type='text' id='$fieldName' name='datafields[{$fieldName}][]'/>";
			echo "<input type='hidden' id='$fieldName' name='datafields[{$fieldName}][]' value='$fieldType'/>";
			echo "<input type='hidden' id='$required' name='datafields[{$fieldName}][]' value='{$required}'/>";
			break;
		case 'numeric':
			echo "<label style='display:block;' for='{$fieldName}'>$fieldWording$asterisk</label>";
			echo "<input style='display:block;' class='$fieldType' type='number' id='$fieldName' name='datafields[{$fieldName}][]'/>";
			echo "<input type='hidden' id='$fieldName' name='datafields[{$fieldName}][]' value='$fieldType'/>";
			echo "<input type='hidden' id='$required' name='datafields[{$fieldName}][]' value='{$required}'/>";
			break;
	}
}

function writeFormBooks( $bookId, $bookWording, $isVisible ) {
	if ( $isVisible == 'true' ) {
		echo ( "<input class='addressBooks' style='margin:0px 5px 5px 0; ' name='books[]' value='$bookId' type='checkbox'/>{$bookWording}<br>" );
	} else {
		echo ( "<input class='addressBooks' name='books[]' value='$bookId' type='hidden'/>" );
	}
}

/**
 * @param $requiredFields
 *
 * @return array
 */
function validateRequiredFields( $requiredFields ) {
	$messages_option = get_option( 'dm_API_messages' );
	$field_missing_error = '';
	$formErrors = array();

	if ( isset( $messages_option ) ) {
		$field_missing_error = $messages_option['dm_API_fill_required'];
	}

	foreach ( $requiredFields as $field_id => $requiredField ) {
		if ( trim( $requiredField[0] ) == '' ) {
			$formErrors[ $field_id ] = $field_missing_error;
		}
	}
	return $formErrors;
}

function checkBooksVisibility( $booksArray ) {

	$invisible_books = array();

	foreach ( $booksArray as $book => $details ) {

		if ( $details['isVisible'] == 'false' ) {

			$invisible_books[] = $book;
		}
	}

	if ( count( $invisible_books ) == count( $booksArray ) ) {
		return true;
	} else {
		return false;
	}
}


// *********************
// *  Install Blocks   *
// *********************
function create_dotdigital_signup_form_block() {
	register_block_type(
		__DIR__ . '/build/signup-form'
	);
}
add_action( 'init', 'create_dotdigital_signup_form_block' );

/**
 * Return Widget content
 */
function dotdigital_widget_content( $request )
{
	ob_start();
	the_widget(
		'DM_Widget',
		array(),
		array(
			'showtitle' => $request['showtitle'] ?? false,
			'showdesc' => $request['showdesc'] ?? false,
			'redirection' => $request['redirecturl'] ?? '',
		)
	);
	return ob_get_clean();
}

add_action( 'rest_api_init', function () {
	register_rest_route(
		'dotdigital/v1',
		'/signup-widget',
		[
			'methods'  => 'GET',
			'callback' => 'dotdigital_widget_content',
			'permission_callback' => '__return_true'
		]
	);
} );


