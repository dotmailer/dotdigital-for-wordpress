<?php
/*
  Plugin Name: Dotdigital Signup Form
  Plugin URI: https://integrations.dotdigital.com/technology-partners/wordpress
  Description: Add a "Subscribe to Newsletter" widget to your WordPress powered website that will insert your contact in one of your Dotdigital address books.
  Version: 6.0.1
  Author: dotdigital
  Author URI: https://www.dotdigital.com/
 */


/*
  Copyright 2014-2022 Dotdigital (email : support@dotdigital.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 2 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

use Dotdigital\Exception\ResponseValidationException;

require_once( plugin_dir_path( __FILE__ ) . 'functions.php' );
require_once( plugin_dir_path( __FILE__ ) . 'dm_widget.php' );
require_once( plugin_dir_path( __FILE__ ) . 'dm_shortcode.php' );
require_once( plugin_dir_path( __FILE__ ) . 'DotdigitalConnect.php' );
register_uninstall_hook( __FILE__, 'dotMailer_widget_uninstall' );
register_activation_hook( __FILE__, 'dotMailer_widget_activate' );

function dotMailer_widget_activate() {
	dotMailer_set_initial_messages();
}

function dotMailer_widget_install() {
	add_option( 'dm_API_credentials', '', '' );
	add_option( 'dm_API_messages', '', '' );
	add_option( 'dm_API_address_books', '', '' );
	add_option( 'dm_API_data_fields', '', '' );
	add_option( 'dm_redirections', '' );
}

function dotMailer_widget_uninstall() {
	delete_option( 'dm_API_credentials' );
	delete_option( 'dm_API_messages' );
	delete_option( 'dm_API_address_books' );
	delete_option( 'dm_API_data_fields' );
	delete_option( 'dm_redirections' );
	delete_option( 'dm_api_endpoint' );
}

/*PG FIX*/

add_action( 'admin_enqueue_scripts', 'settings_head_scripts' );
add_action( 'wp_enqueue_scripts', 'widget_head', 9999 );
add_action( 'widgets_init', 'register_my_widget' );

function register_my_widget() {
	register_widget( 'DM_Widget' );
}

function widget_head() {
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'widgetUI' );
	wp_enqueue_script( 'jquery-ui-datepicker', '', array( 'jquery', 'jquery-ui-core' ) );
	wp_enqueue_script( 'widgetjs', plugins_url( '/js/widget.js', ( __FILE__ ) ) );
	wp_register_style( 'main', plugins_url( '/css/dotmailer.css', ( __FILE__ ) ) );
	wp_enqueue_style( 'main' );
}

function settings_head_scripts() {
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'adminheadjs', plugins_url( '/js/adminheadjs.js', ( __FILE__ ) ) );
	wp_enqueue_script( 'jquery-ui-sortable', '', array( 'jquery', 'jquery-ui-core' ) );
	wp_register_style( 'main', plugins_url( '/css/dotmailer.css', ( __FILE__ ) ) );
	wp_register_style( 'adminCss', plugins_url( '/css/admin.css', ( __FILE__ ) ) );
	wp_enqueue_style( 'main' );
	wp_enqueue_style( 'adminCss' );
}

function dm_create_menu_page() {

	$icon_svg      = 'PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAzMiAzMiI+PHBhdGggZD0iTTE2LDIuNzhBMTMuMjIsMTMuMjIsMCwxLDEsMi43OCwxNiwxMy4yMywxMy4yMywwLDAsMSwxNiwyLjc4TTE2LDBBMTYsMTYsMCwxLDAsMzIsMTYsMTYsMTYsMCwwLDAsMTYsMFoiIGZpbGw9IiNmZmYiLz48cGF0aCBkPSJNMTYsOC4yOUE3Ljc0LDcuNzQsMCwxLDEsOC4yNiwxNiw3Ljc1LDcuNzUsMCwwLDEsMTYsOC4yOW0wLTIuNzhBMTAuNTIsMTAuNTIsMCwxLDAsMjYuNTIsMTYsMTAuNTIsMTAuNTIsMCwwLDAsMTYsNS41MVoiIGZpbGw9IiNmZmYiLz48cGF0aCBkPSJNMTYsMTMuNzdBMi4yNiwyLjI2LDAsMSwxLDEzLjc1LDE2LDIuMjYsMi4yNiwwLDAsMSwxNiwxMy43N00xNiwxMWE1LDUsMCwxLDAsNSw1LDUsNSwwLDAsMC01LTVaIiBmaWxsPSIjZmZmIi8+PC9zdmc+';

	add_menu_page(
		'Dotdigital Signup Form Options', // The title to be displayed on the corresponding page for this menu
		'Dotdigital Signup Form', // The text to be displayed for this actual menu item
		'manage_options', // Which type of users can see this menu
		'dm_form_settings', // The unique ID - that is, the slug - for this menu item
		'dm_settings_menu_display', // The name of the function to call when rendering the menu for this page
		'data:image/svg+xml;base64,' . $icon_svg
	);
}

// end dm_create_menu_page


add_action( 'admin_menu', 'dm_create_menu_page' );
add_action( 'admin_init', 'plugin_admin_init' );

function plugin_admin_init() {
	if ( get_option( 'dm_API_messages' ) === false ) {
		dotMailer_set_initial_messages();
	}
	register_setting( 'dm_API_credentials', 'dm_API_credentials', 'dm_API_credentials_validate' );
	register_setting( 'dm_API_messages', 'dm_API_messages', 'dm_API_messages_validate' );
	register_setting( 'dm_API_address_books', 'dm_API_address_books', 'dm_API_books_validate' );
	register_setting( 'dm_API_data_fields', 'dm_API_data_fields', 'dm_API_fields_validate' );
	register_setting( 'dm_redirections', 'dm_redirections', 'dm_redirections_validate' );
	add_settings_section( 'credentials_section', 'Main settings', 'api_credentials_section', 'credentials_section' );
	add_settings_section( 'messages_section', 'Message settings', 'api_messages_section', 'messages_section' );
	add_settings_section( 'address_books_section', 'Address book settings', 'api_address_books_section', 'address_books_section' );
	add_settings_section( 'data_fields_section', 'Contact data field settings', 'api_data_fields_section', 'data_fields_section' );
	add_settings_section( 'redirections_section', 'Redirection settings', 'api_redirections_section', 'redirections_section' );
	add_settings_field( 'dm_API_username', 'Your API username', 'dm_API_username_input', 'credentials_section', 'credentials_section' );
	add_settings_field( 'dm_API_password', 'Your API password', 'dm_API_password_input', 'credentials_section', 'credentials_section' );
	add_settings_field( 'dm_API_form_title', 'Form header', 'dm_API_form_title_input', 'messages_section', 'messages_section' );
	add_settings_field( 'dm_API_invalid_email', 'Invalid email error message', 'dm_API_invalid_email_input', 'messages_section', 'messages_section' );
	add_settings_field( 'dm_API_fill_required', 'Required field missing error message', 'dm_API_fill_required_input', 'messages_section', 'messages_section' );
	add_settings_field( 'dm_API_success_message', 'Submission success message', 'dm_API_success_message_input', 'messages_section', 'messages_section' );
	add_settings_field( 'dm_API_failure_message', 'Submission failure message', 'dm_API_failure_message_input', 'messages_section', 'messages_section' );
	add_settings_field( 'dm_API_nobook_message', 'No newsletter selected message', 'dm_API_nobook_message_input', 'messages_section', 'messages_section' );
	add_settings_field( 'dm_API_subs_button', 'Form subscribe button', 'dm_API_subs_button_input', 'messages_section', 'messages_section' );
	add_settings_field( 'dm_API_address_books', '', 'dm_API_address_books_input', 'address_books_section', 'address_books_section' );
	add_settings_field( 'dm_API_data_fields', '', 'dm_API_data_fields_input', 'data_fields_section', 'data_fields_section' );
	add_settings_field( 'dm_redirections', 'Where do you want to redirect the user after successful submission?', 'dm_redirections_input', 'redirections_section', 'redirections_section' );
}

function api_credentials_section() {
	echo "<div class='inside'><h4>Change your API credentials:</h4>";
}

function api_messages_section() {
	echo "<div class='inside'><h4>Customise form messages:</h4>";
}

function api_address_books_section() {
	echo "<div class='inside'>";
}

function api_data_fields_section() {
	echo "<div class='inside'>";
}

function api_redirections_section() {
	echo "<div class='inside'>";
}

function dotMailer_set_initial_messages() {

	$messages = array(
		'dm_API_form_title' => 'Subscribe to our newsletter',
		'dm_API_invalid_email' => 'Please use a valid email address',
		'dm_API_fill_required' => 'Please fill all the required fields',
		'dm_API_nobook_message' => 'Please select one newsletter',
		'dm_API_success_message' => 'You have now subscribed to our newsletter',
		'dm_API_failure_message' => 'There was a problem signing you up.',
		'dm_API_subs_button' => 'Subscribe',
	);

	if ( ! get_option( 'dm_API_messages' ) ) {
		add_option( 'dm_API_messages', $messages );
	}

}

function dm_redirections_input() {

	if ( get_option( 'dm_redirections' ) != '' ) {
		$option = get_option( 'dm_redirections' );
	} else {
		$option = array();
	}

	$selected = 0;
	if ( array_key_exists( 'page', $option ) ) {
		$selected = 1;
	}
	if ( array_key_exists( 'url', $option ) ) {
		$selected = 2;
	}

	echo '<table class="wp-list-table widefat fixed radiolist" cellspacing="0">
<tr>
	<th><input type="radio" name="dm_redirections" class="radioselector" id="noredir" value="0"';

	if ( $selected == 0 ) {
		echo ' checked="checked"';
	}

	echo '></th>
	<td class="radiotitle"><label for="noredir">No redirection</label></td>
	<td class="radiovalue"></td>
</tr>
<tr>
	<th><input type="radio" name="dm_redirections" class="radioselector" id="pageredir" value="1"';

	if ( $selected == 1 ) {
		echo ' checked="checked"';
	}

	echo '></th>
	<td class="radiotitle"><label for="pageredir">Local page</label></td>
	<td class="radiovalue"><select';

	if ( $selected != 1 ) {
		echo ' disabled="disabled"';
	}

	echo ' name="dm_redirections[page]">';

	// The Query
	$the_query = new WP_Query( 'post_type=page&orderby=name&order=ASC&nopaging=true' );

	// The Loop
	if ( $the_query->have_posts() ) {
		while ( $the_query->have_posts() ) {
			$the_query->the_post();
			echo '<option value="' . get_the_ID() . '"';
			if ( $selected == 1 && ( $option['page'] == get_the_ID() ) ) {
				echo ' selected="selected"';
			}
			echo '>' . get_the_title() . '</option>';
		}
	}

	echo '</select></td>
</tr>
<tr>
	<th><input type="radio" name="dm_redirections" class="radioselector" id="urlredir" value="2"';

	if ( $selected == 2 ) {
		echo ' checked="checked"';
	}

	echo '></th>
	<td class="radiotitle"><label for="urlredir">Custom URL</label></td>
	<td class="radiovalue"><input size="50" type="text"';

	if ( $selected != 2 ) {
		echo ' disabled="disabled"';
	}

	echo ' name="dm_redirections[url]" value ="';

	if ( $selected == 2 ) {
		echo $option['url'];
	}

	echo '" /></td>
</tr>
</table>';

}

function dm_API_form_title_input() {

	$options = get_option( 'dm_API_messages' );
	echo "<input id='dm_form_title' name='dm_API_messages[dm_API_form_title]' size='40' type='text' value='{$options['dm_API_form_title']}' />";

}

function dm_API_invalid_email_input() {

	$options = get_option( 'dm_API_messages' );
	echo "<input id='dm_invalid_email' name='dm_API_messages[dm_API_invalid_email]' size='40' type='text' value='{$options['dm_API_invalid_email']}' />";
}

function dm_API_fill_required_input() {

	$options = get_option( 'dm_API_messages' );
	echo "<input id='dm_fill_required' name='dm_API_messages[dm_API_fill_required]' size='40' type='text' value='{$options['dm_API_fill_required']}' />";
}

function dm_API_nobook_message_input() {

	$options = get_option( 'dm_API_messages' );
	echo "<input id='dm_nobook_message' name='dm_API_messages[dm_API_nobook_message]' size='40' type='text' value='{$options['dm_API_nobook_message']}' />";
}

function dm_API_success_message_input() {

	$options = get_option( 'dm_API_messages' );
	echo "<input id='dm_success_message' name='dm_API_messages[dm_API_success_message]' size='40' type='text' value='{$options['dm_API_success_message']}' />";
}

function dm_API_failure_message_input() {

	$options = get_option( 'dm_API_messages' );
	echo "<input id='dm_failure_message' name='dm_API_messages[dm_API_failure_message]' size='40' type='text' value='{$options['dm_API_failure_message']}' />";
}

function dm_API_subs_button_input() {

	$options = get_option( 'dm_API_messages' );
	echo "<input  id='dm_subs_button' name='dm_API_messages[dm_API_subs_button]' size='40' type='text' value='{$options['dm_API_subs_button']}' />";
}

function dm_API_username_input() {
	$options = get_option( 'dm_API_credentials' );

	if ( isset( $options['dm_API_username'] )
	) {
		echo "<input id='dm_username' name='dm_API_credentials[dm_API_username]' size='40' type='text' value='{$options['dm_API_username']}' />";
	} else {
		echo "<input id='dm_username' name='dm_API_credentials[dm_API_username]' size='40' type='text'  />";
	}
}

function dm_API_password_input() {
	$options = get_option( 'dm_API_credentials' );
	if ( isset( $options['dm_API_password'] )
	) {
		echo "<input id='dm_password' name='dm_API_credentials[dm_API_password]' size='40' type='password' value='{$options['dm_API_password']}' />";
	} else {
		echo "<input id='dm_password' name='dm_API_credentials[dm_API_password]' size='40' type='password'  />";
	}
}

function dm_API_address_books_input() {
	if ( isset( $_SESSION['connection'] ) && $_SESSION['connection'] !== false ) {
		$dm_account_books = (array) unserialize( $_SESSION['dm_account_books'] );
	}

	if ( isset( $_GET['order'] ) ) {
		if ( $_GET['order'] == 'asc' ) {
			uasort( $dm_account_books, 'bookSortAsc' );
			$neworder = '&order=desc';
		} elseif ( $_GET['order'] == 'desc' ) {
			uasort( $dm_account_books, 'bookSortDesc' );
			$neworder = '&order=asc';
		}
	} else {

		$neworder = '&order=desc';
	}
	?>
	<table class="wp-list-table widefat fixed " cellspacing="0">
		<thead>
			<tr>
				<th scope="col"  class="manage-column column-cb check-column " style=""><input class="multiselector" type="checkbox"/></th>
				<th scope="col" id="addressbook" class="manage-column column-addressbook sortable desc" style=""><a href="?page=dm_form_settings&tab=my_address_books
				<?php
				if ( isset( $neworder ) ) {
					echo $neworder;}
				?>
					"><span>Address Books</span><span class="sorting-indicator"></span></a></th>
				<th scope="col" id="changelabel" class="manage-column column-changelabel" style="">Change label</th>
				<th scope="col" id="visible" class="manage-column column-visible" style="text-align: center;">Visible?</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th scope="col" id="cb" class="manage-column column-cb check-column" style=""><input class="multiselector" type="checkbox"/></th>
				<th scope="col" id="addressbook" class="manage-column column-addressbook sortable desc" style=""><a href="?page=dm_form_settings&tab=my_address_books
				<?php
				if ( isset( $neworder ) ) {
					echo $neworder;}
				?>
					"><span>Address Books</span><span class="sorting-indicator"></span></a></th>
				<th scope="col" id="changelabel" class="manage-column column-changelabel" style="">Change label</th>
				<th scope="col" id="visible" class="manage-column column-visible" style="text-align: center;">Visible?</th>
			</tr>
		</tfoot>
		<tbody id="the-list" class="sort_books">
			<?php
			$selected_books = get_option( 'dm_API_address_books' );

			$indexes_to_replace = array();
			$elements_to_swap = array();
			// re-sort
			if ( ! empty( $selected_books ) ) {
				$swapped_array = array();
				foreach ( $dm_account_books as $account_book ) {

					if ( in_array( $account_book->getName(), array_keys( $selected_books ) ) ) {
						$indexes_to_replace[] = array_search(
							$account_book,
							$dm_account_books
						);
						$elements_to_swap[] = $account_book;
					}
				}

				foreach ( $selected_books as $book_name => $book_details ) {
					foreach ( $elements_to_swap as $index => $element ) {

						if ( $book_name == $element->getName() ) {
							$swapped_array[] = $element;
						}
					}
				}

				if ( ! empty( $indexes_to_replace ) ) {
					$new_order = array_combine( $indexes_to_replace, $swapped_array );
					foreach ( $new_order as $new_key => $element ) {
						$old_index = array_search( $element, $dm_account_books );
						$temp = $dm_account_books[ $new_key ];
						$dm_account_books[ $new_key ] = $element;
						$dm_account_books[ $old_index ] = $temp;
					}
				}
			}

			foreach ( $dm_account_books as $account_book ) {
				$selected = '';
				$label = '';
				$visible = '';

				if ( $account_book->getName() == 'Test' ) {
					continue;
				}
				if ( ! empty( $selected_books ) ) {

					if ( in_array( $account_book->getName(), array_keys( $selected_books ) ) ) {

						$selected = " checked='checked'";
						$book_values = $selected_books[ $account_book->getName() ];
						$label = $book_values['label'];

						if ( $book_values['isVisible'] == 'true' ) {
							$visible = " checked='checked'";
						}
					}
				}
				?>

				<tr id="<?php echo $account_book->getId(); ?>" class="dragger">
					<th scope="row"><span class="handle" ><img src="<?php echo plugins_url( 'images/large.png', __FILE__ ); ?>" class="drag_image" /></span><input class="bookselector" type="checkbox" value="<?php echo $account_book->getId(); ?>" name="dm_API_address_books[<?php echo $account_book->getName(); ?>][id]" <?php echo $selected; ?>/></th>
					<td class="addressbook column-addressbook"><strong><?php echo $account_book->getName(); ?></strong></td>
					<td><input type="text" disabled="disabled" name="dm_API_address_books[<?php echo $account_book->getName(); ?>][label]" value="<?php echo !empty($label) ? $label : $account_book->getName(); ?>"/></td>
					<td style="text-align: center;" class=""><input disabled="disabled" value="false" type="hidden" name="dm_API_address_books[<?php echo $account_book->getName(); ?>][isVisible]" />
						<input value="true" type="checkbox" name="dm_API_address_books[<?php echo $account_book->getName(); ?>][isVisible]" disabled="disabled" <?php echo $visible; ?>/></td>


				</tr>

				<?php
			}
			?>
		</tbody>
	</table>


	<?php
}

function dm_API_data_fields_input() {
	if ( isset( $_SESSION['connection'] ) && $_SESSION['connection'] !== false ) {
		$dm_API_data_fields = (array) unserialize( $_SESSION['dm_data_fields'] );
	}

	if ( isset( $_GET['order'] ) ) {
		if ( $_GET['order'] == 'asc' ) {
			uasort( $dm_API_data_fields, 'bookSortAsc' );
			$neworder = '&order=desc';
		} elseif ( $_GET['order'] == 'desc' ) {
			uasort( $dm_API_data_fields, 'bookSortDesc' );
			$neworder = '&order=asc';
		}
	} else {
		$neworder = '&order=desc';
	}
	?>
		<table class="wp-list-table widefat fixed ">
			<thead>
				<th scope="col" id="cb" class="manage-column column-cb check-column " style=""><input class="multiselector" type="checkbox"/></th>
				<th scope="col" id="addressbook" class="manage-column column-addressbook sortable desc" style=""><a href="?page=dm_form_settings&tab=my_data_fields
				<?php
				if ( isset( $neworder ) ) {
					echo $neworder;}
				?>
				"><span>Contact data fields</span><span class="sorting-indicator"></span></a></th>
				<th scope="col" id="changelabel" class="manage-column column-changelabel" style="">Change label</th>
				<th scope="col" id="visible" class="manage-column column-visible" style="text-align: center;">Required?</th>
			</thead>
			<tfoot>
				<tr>
					<th scope="col" id="cb" class="manage-column column-cb check-column " style=""><input class="multiselector" type="checkbox"/></th>
					<th scope="col" id="addressbook" class="manage-column column-addressbook sortable desc" style=""><a href="?page=dm_form_settings&tab=my_data_fields
					<?php
					if ( isset( $neworder ) ) {
						echo $neworder;}
					?>
					"><span>Contact data fields</span><span class="sorting-indicator"></span></a></th>
					<th scope="col" id="changelabel" class="manage-column column-changelabel" style="">Change label</th>
					<th scope="col" id="visible" class="manage-column column-visible" style="text-align: center;">Required?</th>
				</tr>
			</tfoot>
			<tbody id="the-list" class="sort_fields">

	<?php
	$selected_fields = get_option( 'dm_API_data_fields' );

	// sorting data fields
	$indexes_to_replace = array();
	$elements_to_swap = array();
	// re-sort
	if ( ! empty( $selected_fields ) ) {
		$swapped_array = array();
		foreach ( $dm_API_data_fields as $data_field ) {
			if ( in_array( $data_field->getName(), array_keys( $selected_fields ) ) ) {
				$indexes_to_replace[] = array_search( $data_field, $dm_API_data_fields );
				$elements_to_swap[] = $data_field;
			}
		}

		foreach ( $selected_fields as $field_name => $field_details ) {
			foreach ( $elements_to_swap as $element ) {
				if ( $field_name == $element->getName() ) {
					$swapped_array[] = $element;
				}
			}
		}

		if ( ! empty( $indexes_to_replace ) ) {
			$new_order = array_combine( $indexes_to_replace, $swapped_array );

			foreach ( $new_order as $new_key => $element ) {
				$old_index = array_search( $element, $dm_API_data_fields );
				$temp = $dm_API_data_fields[ $new_key ];
				$dm_API_data_fields[ $new_key ] = $element;
				$dm_API_data_fields[ $old_index ] = $temp;
			}
		}
	}

	// end of sorting
	foreach ( $dm_API_data_fields as $dm_API_data_field ) {

		$selected = '';
		$label = '';
		$required = '';

		if ( ! empty( $selected_fields ) ) {
			if ( in_array( $dm_API_data_field->getName(), array_keys( $selected_fields ) ) ) {
				$selected = " checked='checked'";

				$fields_values = $selected_fields[ $dm_API_data_field->getName() ];

				$label = $fields_values['label'];
				if ( $fields_values['isRequired'] == 'true' ) {
					$required = " checked='checked'";
				}
			}
		}
		?>
				<tr id="<?php echo $dm_API_data_field->getName(); ?>" class="dragger">
					<th  scope="row" ><span class="handle"><img src="<?php echo plugins_url( 'images/large.png', __FILE__ ); ?>" class="drag_image" /></span><input class="bookselector" type="checkbox" value="<?php echo $dm_API_data_field->getName(); ?>" name="dm_API_data_fields[<?php echo $dm_API_data_field->getName(); ?>][name]" <?php echo $selected; ?>/> </th>
					<td><strong><?php echo $dm_API_data_field->getName(); ?></strong></td>
					<td><input  size="50" type="text" disabled="disabled" name="dm_API_data_fields[<?php echo $dm_API_data_field->getName(); ?>][label]" value ="<?php echo !empty($label) ? $label : ucwords( strtolower( $dm_API_data_field->getName() ) )?>" /></td>
					<td class="" style="text-align: center;"><input  disabled="disabled" value="false" type="hidden" name="dm_API_data_fields[<?php echo $dm_API_data_field->getName(); ?>][isRequired]"/>
						<input value="true" type="checkbox" name="dm_API_data_fields[<?php echo $dm_API_data_field->getName(); ?>][isRequired]"  disabled="disabled" <?php echo $required; ?>/>
						<input disabled="disabled" value="<?php echo $dm_API_data_field->getName(); ?>" type="hidden" name="dm_API_data_fields[<?php echo $dm_API_data_field->getName(); ?>][type]" /></td>
				</tr>
				<?php
	}
	?>
			</tbody>
		</table>
	<?php
}

function dm_API_credentials_validate( $input ) {
	$options = get_option( 'dm_API_credentials' );
	$submitted_API_username = trim( $input['dm_API_username'] );
	$submitted_API_password = trim( $input['dm_API_password'] );
	if ( $submitted_API_username == '' || $submitted_API_password == '' ) {
		$options = array();
		add_settings_error( 'dm_API_credentials', 'dm_API_credentials_error', 'Your API credentials cannot be empty.' );
		return $options;
	}

	$connection = new DotdigitalConnect(
		array(
			'username' => $submitted_API_username,
			'password' => $submitted_API_password,
		)
	);

	$account_info = $connection->getAccountInfo();

	if ( $account_info === false ) {
		$options = array();
		add_settings_error( 'dm_API_credentials', 'dm_API_credentials_error', 'Your API credentials seem to be invalid.' );
		return $options;
	}

	foreach ( $account_info->getProperties() as $info ) {
		switch ( $info['Name'] ) {
			case 'ApiEndpoint':
				$acc_dets['ApiEndpoint'] = $info['Value'];
				$api_endpoint = get_option( 'dm_api_endpoint' );
				if ( ( $api_endpoint == false ) || ( $api_endpoint != $acc_dets['ApiEndpoint'] ) ) {
					update_option( 'dm_api_endpoint', $acc_dets['ApiEndpoint'] );
				}
				break;
		}
	}
	$options['dm_API_username'] = trim( $input['dm_API_username'] );
	$options['dm_API_password'] = trim( $input['dm_API_password'] );
	add_settings_error( 'dm_API_credentials', 'dm_API_credentials_error', 'Settings saved.', 'updated' );

	return $options;
}


function dm_API_books_validate( $input ) {
	if ( empty( $input ) ) {
		return array();
	} else {
		return $input;
	}
}

function dm_API_fields_validate( $input ) {

	if ( empty( $input ) ) {
		return array();
	} else {
		return $input;
	}
}

function dm_redirections_validate( $input ) {

	if ( empty( $input ) ) {
		return array();
	} else {
		return $input;
	}
}

function dm_API_messages_validate( $input ) {
	$options = get_option( 'dm_API_messages' );
	foreach ( $input as $input_field ) {
		if ( empty( $input_field ) ) {
			add_settings_error( 'dm_API_messages', 'dm_API_messages_error', 'Please fill all the fields' );
			return $options;
		}
	}
	if ( empty( $input ) ) {
		return array();
	} else {
		return $input;
	}
}

// ***************************************
// This will render the settings page    *
// ***************************************
function dm_settings_menu_display() {

	$options = get_option( 'dm_API_credentials' );

	if ( isset( $options['dm_API_username'] ) && isset( $options['dm_API_password'] ) ) {
		$connection = new DotdigitalConnect(
			array(
				'username' => $options['dm_API_username'],
				'password' => $options['dm_API_password'],
			)
		);
		try {
			$dm_account_books = $connection->listAddressBooks();
			$dm_data_fields = $connection->listDataFields();
			$_SESSION['connection'] = 1;
			$_SESSION['dm_account_books'] = serialize( $dm_account_books );
			$_SESSION['dm_data_fields'] = serialize( $dm_data_fields );
		} catch (ResponseValidationException $responseValidationException) {
			add_settings_error( 'dm_API_credentials', 'dm_API_credentials_error', 'Your API credentials seem to be invalid.' );
		}
	}
	?>
		<style>
			#namediv input.bookselector[type="checkbox"] {width: auto !important;}
		</style>

		<div class="wrap">
			<div id="icon-dotdigital" class="icon32"></div>
			<h2 style="padding:9px 15px 4px 0;">Dotdigital Signup Form</h2>
		<?php settings_errors(); ?>
		<?php
		$active_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'about_dm';
		?>

			<h2 class='nav-tab-wrapper'>
				<a href='?page=dm_form_settings&tab=about_dm' class="nav-tab <?php echo $active_tab == 'about_dm' ? 'nav-tab-active' : ''; ?>">About</a>
				<a href='?page=dm_form_settings&tab=api_credentials' class="nav-tab <?php echo $active_tab == 'api_credentials' ? 'nav-tab-active' : ''; ?>">API credentials</a>
				<a href='?page=dm_form_settings&tab=my_address_books' class="nav-tab <?php echo $active_tab == 'my_address_books' ? 'nav-tab-active' : ''; ?>">My address books</a>
				<a href='?page=dm_form_settings&tab=my_data_fields' class="nav-tab <?php echo $active_tab == 'my_data_fields' ? 'nav-tab-active' : ''; ?>">My contact data fields</a>
				<a href='?page=dm_form_settings&tab=my_form_msg' class="nav-tab <?php echo $active_tab == 'my_form_msg' ? 'nav-tab-active' : ''; ?>">Messages</a>
				<a href='?page=dm_form_settings&tab=my_redirections' class="nav-tab <?php echo $active_tab == 'my_redirections' ? 'nav-tab-active' : ''; ?>">Redirections</a>
			</h2>
		<?php
		if ( $active_tab == 'my_address_books' ) {
			if ( isset( $dm_account_books ) ) {
				?>

					<div class="metabox-holder columns-2 newdmstyle" id="post-body">
						<div id="post-body-content">
							<div id="namediv" class="postbox">
								<form action="options.php" method="post">
					<?php settings_fields( 'dm_API_address_books' ); ?>
					<?php do_settings_sections( 'address_books_section' ); ?>
									<p><a href="https://support.dotdigital.com/hc/en-gb/articles/212216058-Using-the-dotmailer-WordPress-sign-up-form-plugin-v2#My%20address%20books" target="_blank">Find out more...</a></p>



							</div>
						</div>
					</div>
				</div>
				<input name="Submit" type="submit" value="Save Changes" class="button-primary action">
				</form>

					<?php
			} else {
				?>
				<div class="metabox-holder columns-2 newdmstyle" id="post-body">
					<table width="100%" cellspacing="0" cellpadding="0">
						<tbody>
							<tr valign="top">
								<td>
									<div id="post-body-content">
										<div id="namediv" class="postbox">
											<h3>You're not up and running yet...</h3>
											<div class="inside">
												<p>Before you can use this tab, you need to enter your API credentials. See our <a href="https://support.dotdigital.com/hc/en-gb/articles/212216058-Using-the-dotmailer-WordPress-sign-up-form-plugin-v2" target="_blank">user guide</a> on how to get started</p>
											</div>
										</div>
									</div>
								</td>
							</tr>
						</tbody>
					</table>
				</div>

				<?php
			}
		}

		if ( $active_tab == 'my_data_fields' ) {
			if ( isset( $dm_data_fields ) ) {
				?>
				<div class="metabox-holder columns-2 newdmstyle" id="post-body">
					<div id="post-body-content">
						<div id="namediv" class="postbox">
							<form action="options.php" method="post">
				<?php settings_fields( 'dm_API_data_fields' ); ?>
				<?php do_settings_sections( 'data_fields_section' ); ?>
								<p><a href="https://support.dotdigital.com/hc/en-gb/articles/212216058-Using-the-dotmailer-WordPress-sign-up-form-plugin-v2#My%20contact%20data%20fields" target="_blank">Find out more...</a></p>
						</div>
					</div>
				</div>
			</div>
			<input name="Submit" type="submit" value="Save Changes" class="button-primary action">
			</form>
				<?php
			} else {
				?>
			<div class="metabox-holder columns-2 newdmstyle" id="post-body">
				<table width="100%" cellspacing="0" cellpadding="0">
					<tbody>
						<tr valign="top">
							<td>
								<div id="post-body-content">
									<div id="namediv" class="postbox">
										<h3>You're not up and running yet...</h3>
										<div class="inside">


											<p>Before you can use this tab, you need to enter your API credentials. See our <a href="https://support.dotdigital.com/hc/en-gb/articles/212216058-Using-the-dotmailer-WordPress-sign-up-form-plugin-v2" target="_blank">user guide</a> on how to get started</p>
										</div>
									</div>
								</div>
							</td>
						</tr>
					</tbody>
				</table>
			</div>

				<?php
			}
		}

		if ( $active_tab == 'about_dm' ) {
			?>

		<div class="metabox-holder columns-2 newdmstyle" id="post-body">
			<table width="100%" cellspacing="0" cellpadding="0">
				<tbody>
					<tr valign="top">
						<td>
							<div id="post-body-content">
								<div id="namediv" class="postbox">
									<h3>What it does</h3>
									<div class="inside">
										<p>Add the Dotdigital signup form plugin to your site and allow your visitors to sign up to your dotdigital-powered newsletter and email marketing campaigns. The email addresses of new subscribers can be added to multiple Dotdigital address books, and you can capture contact data fields too.</p>

										<p>If you're not a Dotdigital user already you can find out more about us at <a href="https://www.dotdigital.com">dotdigital.com</a>.</p>

										<p></p><a href="https://wordpress.org/plugins/dotmailer-sign-up-widget/#developers" target="_blank">See the full changelog here...</a></div>

									</div>
								</div>
							</div>

							<div id="post-body-content">
								<div id="namediv" class="postbox">
									<h3>Setup advice</h3>
									<div class="inside">
										<p>To get you up and running, we have full setup
											instructions on the <a href="https://support.dotdigital.com/hc/en-gb/articles/212216058-Using-the-dotmailer-WordPress-sign-up-form-plugin-v2" target="_blank">Dotdigital knowledge base</a>.</p>
									</div>
								</div>
							</div>


						</td>
						<td width="10"></td>
						<td width="350">
							<div class="postbox">
								<h3 style="cursor: default;">Dotdigital</h3>
								<div class="inside">
									<img src="<?php echo plugins_url( '/images/dotdigital-logo.png', ( __FILE__ ) ); ?>" alt="dotdigital" /> <p>Powerful email marketing made easy - with the most intuitive, easy to use email marketing platform you will find. Grab yourself a free 30-day trial now from our website.&nbsp;Visit <a href="http://dotdigital.com" target="_blank" >dotdigital.com &gt;&gt;</a></p>
								</div>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>

			<?php
		}

		if ( $active_tab == 'api_credentials' ) {
			?>

		<div class="metabox-holder columns-2 newdmstyle" id="post-body">
			<div id="post-body-content">
				<div id="namediv" class="postbox">


					<form action="options.php" method="post">

			<?php settings_fields( 'dm_API_credentials' ); ?>

			<?php do_settings_sections( 'credentials_section' ); ?>
						<p><a href="https://support.dotdigital.com/hc/en-gb/articles/212216058-Using-the-dotmailer-WordPress-sign-up-form-plugin-v2#API%20credentials" target="_blank">Find out more...</a></p>
				</div>
			</div>

		</div>

		</div>
		<input name="Submit" type="submit" value="Save Changes" class="button-secondary action">
		</form>

			<?php
			if ( isset( $connection ) ) {
				$account_info = $connection->getAccountInfo();
				if ( $account_info && $account_info->getProperties() ) {
					?>
			<div class="metabox-holder columns-2 newdmstyle" id="post-body">
				<table width="100%" cellspacing="0" cellpadding="0">
					<tbody>
						<tr valign="top">
							<td>
								<div id="post-body-content">
									<div id="namediv" class="postbox">
										<h3>Account details</h3>
										<div class="inside">
							<?php
							$acc_dets = array();
							foreach ( $account_info->getProperties() as $info ) {
								switch ( $info['name'] ) {
									case 'Name':
										$acc_dets['Name'] = $info['value'];
										break;
									case 'MainEmail':
										$acc_dets['MainEmail'] = $info['value'];
										break;
									case 'ApiCallsInLastHour':
										$acc_dets['ApiCallsInLastHour'] = $info['value'];
										break;
									case 'ApiCallsRemaining':
										$acc_dets['ApiCallsRemaining'] = $info['value'];
										break;
									case 'ApiEndpoint':
										$acc_dets['ApiEndpoint'] = $info['value'];
										$api_endpoint = get_option( 'dm_api_endpoint' );
										if ( ( $api_endpoint == false ) || ( $api_endpoint != $acc_dets['ApiEndpoint'] ) ) {
											update_option( 'dm_api_endpoint', $acc_dets['ApiEndpoint'] );
										}
										break;
								}
							}

							echo "<p style='font-weight:bold;'>Account holder name:</p> {$acc_dets['Name']}";
							echo "<p style='font-weight:bold;'>Main account email address:</p> {$acc_dets['MainEmail']}";
							echo "<p style='font-weight:bold;'>API calls in last hour:</p> {$acc_dets['ApiCallsInLastHour']}";
							echo "<p style='font-weight:bold;'>API calls remaining:</p> {$acc_dets['ApiCallsRemaining']}";
							echo "<p style='font-weight:bold;'>API endpoint:</p> {$acc_dets['ApiEndpoint']}";
							?>
										</div>
									</div>
								</div>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
					<?php
				} // if ($account_info->getProperties())
			} // end if ($active_tab == 'api_credentials')
		} // end if isset($connection)

		if ( $active_tab == 'my_form_msg' ) {
			?>

		<div class="metabox-holder columns-2 newdmstyle" id="post-body">
			<div id="post-body-content">
				<div id="namediv" class="postbox" style="padding-bottom:10px;">
					<form action="options.php" method="post">
										<?php settings_fields( 'dm_API_messages' ); ?>
										<?php do_settings_sections( 'messages_section' ); ?>
						<p><a href="https://support.dotdigital.com/hc/en-gb/articles/212216058-Using-the-dotmailer-WordPress-sign-up-form-plugin-v2#Messages" target="_blank">Find out more...</a></p>
				</div>
			</div>
		</div>
		</div>
		<input name="Submit" type="submit" value="Save Changes" class="button-primary action">
		</form>

			<?php
		}

		if ( $active_tab == 'my_redirections' ) {
			?>

		<div class="metabox-holder columns-2 newdmstyle" id="post-body">
			<div id="post-body-content">
				<div id="namediv" class="postbox" style="padding-bottom:10px;">
					<form action="options.php" method="post">
								<?php settings_fields( 'dm_redirections' ); ?>
								<?php do_settings_sections( 'redirections_section' ); ?>
				</div>
			</div>
		</div>
		</div>
		<input name="Submit" type="submit" value="Save Changes" class="button-primary action">
		</form>
			<?php
		}
		?>
	</div>
	<?php
}
?>
