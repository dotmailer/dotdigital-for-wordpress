<?php
/**
 * The file that defines the Dotdigital_WordPress_Sign_Up_Widget class
 *
 * @package    Dotdigital_WordPress
 */

namespace Dotdigital_WordPress\Includes\Widget;

use Dotdigital_WordPress\Includes\Client\Dotdigital_WordPress_Account_Info;
use Dotdigital_WordPress\Includes\Traits\Dotdigital_WordPress_Interacts_With_Messages_Trait;
use Dotdigital_WordPress\Includes\Traits\Dotdigital_WordPress_Interacts_With_Redirection_Trait;
use WP_Widget;

/**
 * Class Dotdigital_WordPress_Sign_Up_Widget
 */
class Dotdigital_WordPress_Sign_Up_Widget extends WP_Widget {

	use Dotdigital_WordPress_Interacts_With_Messages_Trait;
	use Dotdigital_WordPress_Interacts_With_Redirection_Trait;

	/**
	 * @var string $widget_id
	 */
	public $widget_id = DOTDIGITAL_WORDPRESS_PLUGIN_NAME . '_widget_signup_form';

	/**
	 * @var string $widget_name
	 */
	public $widget_name = 'Dotdigital Signup Form';

	/**
	 * @var string $widget_description
	 */
	public $widget_description = 'Deprecated - please use the new Dotdigital WordPress Signup Form block.';

	/**
	 * @var array $widget_options
	 */
	public $widget_options = array();

	/**
	 * @var array $control_options
	 */
	public $control_options = array();

	/**
	 * Dotdigital account info.
	 *
	 * @var Dotdigital_WordPress_Account_Info $dotdigital_account_info
	 */
	private $dotdigital_account_info;

	/**
	 * @var int
	 */
	private $widget_instance_id = 1;

	/**
	 * Construct.
	 */
	public function __construct() {
		$this->dotdigital_account_info = new Dotdigital_WordPress_Account_Info();
		parent::__construct(
			$this->widget_id,
			$this->widget_name,
			array_merge(
				array(
					'classname'   => $this->widget_id,
					'description' => __(
						'Deprecated - please use the new Dotdigital Signup Form block.',
						'dotdigital-for-wordpress'
					),
				),
				$this->widget_options ?? array()
			),
			array_merge(
				array(
					'id_base' => $this->widget_id,
				),
				$this->control_options ?? array()
			)
		);
	}

	/**
	 * Widget.
	 *
	 * @param array $args The arguments.
	 * @param array $instance The instance.
	 *
	 * @return void
	 */
	public function widget( $args, $instance ) {
		if ( ! $this->connected() ) {
			return;
		}
		wp_parse_args(
			$args,
			array(
				'showtitle' => 1,
				'showdesc'  => 1,
				'is_ajax'   => 0,
			)
		);

		$domain               = strval( DOTDIGITAL_WORDPRESS_PLUGIN_NAME );
		$showtitle            = $args['showtitle'] ?? 1;
		$showdesc             = $args['showdesc'] ?? 1;
		$redirection          = ! empty( $args['redirection'] ) ? $args['redirection'] : $this->get_redirection();
		$is_ajax              = $args['is_ajax'] ?? 0;
		$widget               = $this;
		$dd_widget_id         = $widget->id . '-' . $this->widget_instance_id++;
		$recaptcha            = get_option( 'dm_recaptcha', array() );
		$recaptcha_site_key   = $recaptcha['dm_recaptcha_site_key'] ?? '';
		$recaptcha_secret_key = $recaptcha['dm_recaptcha_secret_key'] ?? '';
		$recaptcha_hide_badge = $recaptcha['dm_recaptcha_hide_badge'] ?? false;

		// Enqueue scripts if reCAPTCHA is configured.
		if ( $recaptcha_site_key && $recaptcha_secret_key ) {
			$this->enqueue_recaptcha_scripts();
			$this->add_widget_data( $dd_widget_id, $recaptcha_site_key );
		}

		require DOTDIGITAL_WORDPRESS_PLUGIN_PATH . 'public/view/widget/dotdigital-wordpress-widget-sign-up.php';
		require DOTDIGITAL_WORDPRESS_PLUGIN_PATH . 'public/view/widget/dotdigital-wordpress-widget-sign-up-messages.php';
	}

	/**
	 * Add widget data to the localized script.
	 *
	 * @param string $widget_id
	 * @param string $site_key
	 */
	private function add_widget_data( $widget_id, $site_key ) {
		// Store widget data in a static array to accumulate all widgets.
		static $widget_data = array();

		$widget_data[] = array(
			'id'      => $widget_id,
			'siteKey' => $site_key,
		);

		wp_localize_script(
			'dotdigital-widget-recaptcha',
			'dmRecaptchaData',
			array( 'widgets' => $widget_data )
		);
	}
	/**
	 * Enqueue reCAPTCHA scripts.
	 */
	private function enqueue_recaptcha_scripts() {
		$recaptcha            = get_option( 'dm_recaptcha' );
		$recaptcha_site_key   = $recaptcha['dm_recaptcha_site_key'] ?? '';
		$recaptcha_secret_key = $recaptcha['dm_recaptcha_secret_key'] ?? '';

		if ( $recaptcha_site_key && $recaptcha_secret_key ) {
			wp_enqueue_script(
				'dotdigital-recaptcha',
				'https://www.google.com/recaptcha/api.js?render=' . esc_attr( $recaptcha_site_key ),
				array(),
				null,
				true
			);
		}

		if ( ! wp_script_is( 'dotdigital-widget-recaptcha', 'enqueued' ) ) {
			wp_enqueue_script(
				'dotdigital-widget-recaptcha',
				DOTDIGITAL_WORDPRESS_PLUGIN_URL . 'public/js/dotdigital-recaptcha.js',
				array(),
				DOTDIGITAL_WORDPRESS_VERSION,
				true
			);
		}
	}

	/**
	 * @param string $widget_id
	 *
	 * @return string
	 */
	public function get_message( $widget_id ) {
		if ( ! isset( $_GET['widget_id'] ) || $_GET['widget_id'] !== $widget_id ) {
			return '';
		}
		return isset( $_GET['message'] ) ? sanitize_text_field( wp_unslash( $_GET['message'] ) ) : '';
	}

	/**
	 * @param string $widget_id
	 *
	 * @return string
	 */
	public function get_message_class( $widget_id ) {
		switch ( $this->get_success( $widget_id ) ) {
			case 1:
				return 'dd-wordpress-success-msg';
			case 0:
				return 'dd-wordpress-error-msg';
			default:
				return 'dd-wordpress-info-msg';
		}
	}

	/**
	 * @param string $widget_id
	 *
	 * @return string|int
	 */
	public function get_success( $widget_id ) {
		if ( ! isset( $_GET['widget_id'] ) || $_GET['widget_id'] !== $widget_id ) {
			return '';
		}
		return isset( $_GET['success'] ) ? sanitize_text_field( wp_unslash( $_GET['success'] ) ) : '';
	}

	/**
	 * Is connected.
	 *
	 * @return bool
	 */
	private function connected(): bool {
		try {
			return $this->dotdigital_account_info->is_connected();
		} catch ( \Exception $exception ) {
			require DOTDIGITAL_WORDPRESS_PLUGIN_PATH . 'public/view/widget/dotdigital-wordpress-widget-sign-up-error.php';
			return false;
		}
	}
}
