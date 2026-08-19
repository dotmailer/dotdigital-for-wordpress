<?php
/**
 * Class Dotdigital_WordPress_Recaptcha_Admin
 *
 * Handles the reCAPTCHA admin settings tab.
 *
 * @package Dotdigital_WordPress\Admin\Page\Tab
 */

namespace Dotdigital_WordPress\Admin\Page\Tab;

use Dotdigital_WordPress\Admin\Page\Dotdigital_WordPress_Page_Tab_Interface;
use Dotdigital_WordPress\Includes\Setting\Dotdigital_WordPress_Config;
use Dotdigital_WordPress\Includes\Setting\Form\Dotdigital_WordPress_Setting_Form;
use Dotdigital_WordPress\Includes\Setting\Form\Fields\Dotdigital_WordPress_Setting_Form_Text_Input;
use Dotdigital_WordPress\Includes\Setting\Form\Fields\Dotdigital_WordPress_Setting_Form_Checkbox_Input;

/**
 * Class Dotdigital_WordPress_Recaptcha_Admin
 *
 * Implements the reCAPTCHA settings tab.
 */
class Dotdigital_WordPress_Recaptcha_Admin implements Dotdigital_WordPress_Page_Tab_Interface {

	/**
	 * URL slug for the reCAPTCHA settings tab.
	 *
	 * @var string
	 */
	private const URL_SLUG = 'recaptcha';

	/**
	 * Form instance for the reCAPTCHA settings.
	 *
	 * @var Dotdigital_WordPress_Setting_Form
	 */
	private $form;

	/**
	 * Temporary form values for validation errors.
	 *
	 * @var array
	 */
	private $temp_values = array();

	/**
	 * Initialize the reCAPTCHA settings tab.
	 *
	 * @return void
	 */
	public function initialise() {
		$this->temp_values = get_transient( 'dm_recaptcha_temp_values_' . get_current_user_id() );
		if ( false === $this->temp_values ) {
			$this->temp_values = array();
		}

		$this->form = new Dotdigital_WordPress_Setting_Form(
			$this->get_slug(),
			'reCAPTCHA settings',
			$this->get_slug()
		);

		$current_values   = get_option( 'dm_recaptcha', array() );
		$site_key_value   = $this->temp_values['dm_recaptcha_site_key'] ?? $current_values['dm_recaptcha_site_key'] ?? '';
		$secret_key_value = $this->temp_values['dm_recaptcha_secret_key'] ?? $current_values['dm_recaptcha_secret_key'] ?? '';
		$threshold_value  = $this->temp_values['dm_recaptcha_threshold'] ?? $current_values['dm_recaptcha_threshold'] ?? '0.5';
		$validation_message_value  = $this->temp_values['dm_recaptcha_validation_message'] ?? $current_values['dm_recaptcha_validation_message'] ?? '';
		$hide_badge_value  = $this->temp_values['dm_recaptcha_hide_badge'] ?? $current_values['dm_recaptcha_hide_badge'] ?? false;

		$site_key_input = new Dotdigital_WordPress_Setting_Form_Text_Input(
			Dotdigital_WordPress_Config::SETTING_RECAPTCHA_PATH . '[dm_recaptcha_site_key]',
			'Site Key',
			$this->get_slug()
		);
		$this->form->add_input( $site_key_input );

		$secret_key_input = new Dotdigital_WordPress_Setting_Form_Text_Input(
			Dotdigital_WordPress_Config::SETTING_RECAPTCHA_PATH . '[dm_recaptcha_secret_key]',
			'Secret Key',
			$this->get_slug()
		);
		$this->form->add_input( $secret_key_input );

		$threshold_input = new Dotdigital_WordPress_Setting_Form_Text_Input(
			Dotdigital_WordPress_Config::SETTING_RECAPTCHA_PATH . '[dm_recaptcha_threshold]',
			'Score Threshold',
			$this->get_slug(),
			'',
			'Scores range from 0.0 (likely a bot) to 1.0 (likely a human). Adjust the default value of 0.5 to customise bot filtering for your site\'s context.'
		);
		$this->form->add_input( $threshold_input );

		$validation_message_input = new Dotdigital_WordPress_Setting_Form_Text_Input(
			Dotdigital_WordPress_Config::SETTING_RECAPTCHA_PATH . '[dm_recaptcha_validation_message]',
			'Validation Failure Message',
			$this->get_slug(),
			'',
			'Leave blank to not display any message when reCAPTCHA validation fails.'
		);
		$this->form->add_input( $validation_message_input );

		$hide_badge_input = new Dotdigital_WordPress_Setting_Form_Checkbox_Input(
			Dotdigital_WordPress_Config::SETTING_RECAPTCHA_PATH . '[dm_recaptcha_hide_badge]',
			'Hide the reCAPTCHA badge',
			$this->get_slug(),
			'',
			'By hiding the badge you confirm that you added the required reCAPTCHA branding and legal notices to your site. Read more <a href="https://developers.google.com/recaptcha/docs/faq#id-like-to-hide-the-recaptcha-badge.-what-is-allowed" target="_blank">here.</a>'
		);
		$this->form->add_input( $hide_badge_input );

		$this->form->initialise();
		add_filter( "{$this->get_slug()}/save", array( $this, 'save' ), 10, 1 );

		if ( ! empty( $this->temp_values ) ) {
			delete_transient( 'dm_recaptcha_temp_values_' . get_current_user_id() );
		}

		add_filter(
			"{$this->get_slug()}/dm_recaptcha[dm_recaptcha_site_key]/value",
			function () use ( $site_key_value ) {
				return $site_key_value;
			}
		);

		add_filter(
			"{$this->get_slug()}/dm_recaptcha[dm_recaptcha_secret_key]/value",
			function () use ( $secret_key_value ) {
				return $secret_key_value;
			}
		);

		add_filter(
			"{$this->get_slug()}/dm_recaptcha[dm_recaptcha_threshold]/value",
			function () use ( $threshold_value ) {
				return $threshold_value;
			}
		);

		add_filter(
			"{$this->get_slug()}/dm_recaptcha[dm_recaptcha_validation_message]/value",
			function () use ( $validation_message_value ) {
				return $validation_message_value;
			}
		);

		add_filter(
			"{$this->get_slug()}/dm_recaptcha[dm_recaptcha_hide_badge]/value",
			function () {
				return 'on';
			}
		);

		add_filter(
			"{$this->get_slug()}/dm_recaptcha[dm_recaptcha_hide_badge]/checked",
			function () use ( $hide_badge_value ) {
				return filter_var( $hide_badge_value, FILTER_VALIDATE_BOOLEAN );
			}
		);
	}

	/**
	 * Render the reCAPTCHA settings tab.
	 *
	 * @return void
	 */
	public function render() {
		$view = $this;
		$form = $view->form;
		require_once DOTDIGITAL_WORDPRESS_PLUGIN_PATH . 'admin/view/tabs/dotdigital-wordpress-recaptcha-admin.php';
	}

	/**
	 * Validates the reCAPTCHA settings and adds response notices.
	 *
	 * @param array $options The options to save.
	 *
	 * @return array The options to save.
	 */
	public function save( $options = array() ) {
		$site_key   = $options['dm_recaptcha_site_key'] ?? '';
		$secret_key = $options['dm_recaptcha_secret_key'] ?? '';
		$threshold  = $options['dm_recaptcha_threshold'] ?? '';

		$any_filled = ! empty( $site_key ) || ! empty( $secret_key ) || ! empty( $threshold );

		if ( $any_filled ) {
			if ( empty( $site_key ) || empty( $secret_key ) || empty( $threshold ) ) {
				set_transient( 'dm_recaptcha_temp_values_' . get_current_user_id(), $options, 120 );
				do_action( DOTDIGITAL_WORDPRESS_PLUGIN_NAME . '_settings_notice', 'All reCAPTCHA fields are required when configuring reCAPTCHA.', 'error' );
				return array();
			}

			// Validate threshold range.
			$threshold_value = floatval( $threshold );
			if ( $threshold_value < 0.0 || $threshold_value > 1.0 ) {
				set_transient( 'dm_recaptcha_temp_values_' . get_current_user_id(), $options, 120 );
				do_action( DOTDIGITAL_WORDPRESS_PLUGIN_NAME . '_settings_notice', 'reCAPTCHA threshold must be between 0.0 and 1.0.', 'error' );
				return array();
			}
		}

		delete_transient( 'dm_recaptcha_temp_values_' . get_current_user_id() );

		do_action( DOTDIGITAL_WORDPRESS_PLUGIN_NAME . '_settings_notice', 'reCAPTCHA settings saved', 'success' );
		return $options;
	}

	/**
	 * Get the slug for the reCAPTCHA settings.
	 *
	 * @return string The slug.
	 */
	public function get_slug(): string {
		return Dotdigital_WordPress_Config::SETTING_RECAPTCHA_PATH;
	}

	/**
	 * Get the URL slug for the reCAPTCHA settings tab.
	 *
	 * @return string The URL slug.
	 */
	public function get_url_slug(): string {
		return self::URL_SLUG;
	}

	/**
	 * Get the title for the reCAPTCHA settings tab.
	 *
	 * @return string The title.
	 */
	public function get_title() {
		return __( 'reCAPTCHA', 'dotdigital-for-wordpress' );
	}
}
