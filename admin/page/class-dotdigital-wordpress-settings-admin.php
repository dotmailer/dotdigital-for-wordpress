<?php
/**
 * The file that defines the Dotdigital_WordPress_Settings_Admin page class
 *
 * @package    Dotdigital_WordPress
 */

namespace Dotdigital_WordPress\Admin\Page;

class Dotdigital_WordPress_Settings_Admin {

	/**
	 * The URL slug for the settings page.
	 *
	 * @var string
	 */
	public const URL_SLUG = 'dotdigital-for-wordpress-settings';

	/**
	 * Render
	 *
	 * @return void
	 */
	public function render() {
		$this->legacy_redirect();
		require_once DOTDIGITAL_WORDPRESS_PLUGIN_PATH . 'admin/view/dotdigital-wordpress-admin-settings.php';
	}

	/**
	 * Redirect to the settings page.
	 *
	 * @return void
	 */
	public function legacy_redirect() {
		$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : '';

		if ( '' === $active_tab ) {
			wp_redirect(
				admin_url( 'admin.php?page=' . $this->get_slug() . '&tab=about' )
			);
			exit();
		}

		$valid_tab_names = array( 'about', 'lists', 'api_credentials', 'data_fields', 'messages', 'redirections' );
		if ( in_array( $active_tab, $valid_tab_names ) ) {
			return;
		}

		switch ( $active_tab ) {
			case 'my_address_books':
				$tab = 'lists';
				break;
			case 'api_credentials':
				$tab = 'api_credentials';
				break;
			case 'my_data_fields':
				$tab = 'data_fields';
				break;
			case 'my_form_msg':
				$tab = 'messages';
				break;
			case 'my_redirections':
				$tab = 'redirections';
				break;
			default:
				$tab = 'about';
				break;
		}

		wp_redirect(
			admin_url( 'admin.php?page=' . $this->get_slug() . ( $tab ? '&tab=' . $tab : '' ) )
		);
		exit();
	}

	/**
	 * Get the URL slug for the settings page.
	 *
	 * @return string
	 */
	public function get_slug(): string {
		return self::URL_SLUG;
	}
}
