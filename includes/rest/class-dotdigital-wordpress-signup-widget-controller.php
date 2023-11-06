<?php
/**
 * Restful controller for the widget content
 *
 * @package    Dotdigital_WordPress
 */

namespace Dotdigital_WordPress\Includes\Rest;

use Dotdigital\V3\Models\Contact;
use Dotdigital_WordPress\Includes\Client\Dotdigital_WordPress_Contact;
use Dotdigital_WordPress\Includes\Setting\Dotdigital_WordPress_Config;
use Dotdigital_WordPress\Includes\Widget\Dotdigital_WordPress_Sign_Up_Widget;

class Dotdigital_WordPress_Signup_Widget_Controller {

	/**
	 * @var Dotdigital_WordPress_Contact
	 */
	private $dotdigital_contact;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->dotdigital_contact = new Dotdigital_WordPress_Contact();
	}

	/**
	 * Register the routes for the objects of the controller.
	 */
	public function register() {
		register_rest_route(
			'dotdigital/v1',
			'/signup-widget',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get' ),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			'dotdigital/v1',
			'/signup-widget',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'post' ),
				'args'                => array(
					'email'       => array(
						'required' => true,
					),
					'redirection' => array(
						'required' => true,
					),
				),
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * Return content
	 *
	 * @param \WP_REST_Request $request
	 * @return false|string
	 */
	public function get( \WP_REST_Request $request ) {
		ob_start();
		the_widget(
			\DM_Widget::class,
			array(),
			array(
				'showtitle'   => $request['showtitle'] ?? false,
				'showdesc'    => $request['showdesc'] ?? false,
				'redirection' => $request['redirecturl'] ?? '',
			)
		);
		return ob_get_clean();
	}

	/**
	 * Save widget from data content
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return bool
	 */
	public function post( \WP_REST_Request $request ) {
		$data = $request->get_params();

		if ( $this->has_invalid_email( $data['email'] ) ) {
			return wp_redirect(
				add_query_arg(
					array(
						'success' => false,
						'message' => Dotdigital_WordPress_Sign_Up_Widget::get_invalid_email_message(),
						'widget_id' => $data['widget_id'],
					),
					$data['origin']
				)
			);
		}

		if ( $this->has_no_lists_but_should_have( $data ) ) {
			return wp_redirect(
				add_query_arg(
					array(
						'success' => false,
						'message' => Dotdigital_WordPress_Sign_Up_Widget::get_nobook_message(),
						'widget_id' => $data['widget_id'],
					),
					$data['origin']
				)
			);
		}

		if ( $this->has_missing_required_data_fields( $data ) ) {
			return wp_redirect(
				add_query_arg(
					array(
						'success' => false,
						'message' => Dotdigital_WordPress_Sign_Up_Widget::get_fill_required_message(),
						'widget_id' => $data['widget_id'],
					),
					$data['origin']
				)
			);
		}

		try {
			$contact = new Contact();
			$contact->setIdentifiers( array( 'email' => $data['email'] ) );
			$contact->setLists( array_values( $data['lists'] ?? array() ) );
			$contact->setDataFields( $this->prepare_data_fields( $data['datafields'] ?? array() ) );
			$this->dotdigital_contact->create_or_update( $contact );
		} catch ( \Exception $e ) {
			error_log( $e->getMessage() );
			return wp_redirect(
				add_query_arg(
					array(
						'success' => false,
						'message' => Dotdigital_WordPress_Sign_Up_Widget::get_failure_message(),
						'widget_id' => $data['widget_id'],
					),
					$data['origin']
				)
			);
		}

		return wp_redirect(
			add_query_arg(
				array(
					'success' => true,
					'message' => Dotdigital_WordPress_Sign_Up_Widget::get_success_message(),
					'widget_id' => $data['widget_id'],
				),
				$data['redirection']
			)
		);
	}

	/**
	 * @param string $email
	 *
	 * @return mixed
	 */
	private function has_invalid_email( string $email ) {
		return ! filter_var( $email, FILTER_VALIDATE_EMAIL );
	}

	/**
	 * Check if payload has lists.
	 *
	 * If any visible lists are configured, the payload must contain at least one list. Otherwise we don't mind.
	 *
	 * @param array $data
	 *
	 * @return bool
	 */
	private function has_no_lists_but_should_have( array $data ) {
		$has_visible_lists = count(
			array_filter(
				get_option( Dotdigital_WordPress_Config::SETTING_LISTS_PATH ),
				function ( $list ) {
					return $list['isVisible'];
				}
			)
		) > 0;

		return $has_visible_lists && empty( $data['lists'] );
	}

	/**
	 * @param array $data
	 *
	 * @return bool
	 */
	private function has_missing_required_data_fields( array $data ) {
		foreach ( $data['datafields'] ?? array() as $datafield ) {
			if ( $datafield['required'] && empty( $datafield['value'] ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @param array $datafields
	 *
	 * @return array
	 */
	private function prepare_data_fields( array $datafields ) {
		$prepared = array();
		foreach ( $datafields as $name => $value ) {
			$prepared[ $name ] = $value['value'];
		}
		return $prepared;
	}
}
