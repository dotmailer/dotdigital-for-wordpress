<?php
/**
 * Class Dotdigital_WordPress_Patch_7_3_0
 *
 * This patch is for updating the datafields and lists order in the settings.
 *
 * @since 7.3.0
 * @package Dotdigital_WordPress\Includes\Patches
 * @see Dotdigital_WordPress_Patch_Interface
 */

namespace Dotdigital_WordPress\Includes\Patches;

use Dotdigital_WordPress\Includes\Setting\Dotdigital_WordPress_Config;

class Dotdigital_WordPress_Patch_7_3_0 implements Dotdigital_WordPress_Patch_Interface {

	/**
	 * @inheritDoc
	 */
	public static function apply_patch() {

		$configs = array(
			Dotdigital_WordPress_Config::SETTING_DATAFIELDS_PATH => get_option(
				Dotdigital_WordPress_Config::SETTING_DATAFIELDS_PATH,
				array()
			),
			Dotdigital_WordPress_Config::SETTING_LISTS_PATH => get_option(
				Dotdigital_WordPress_Config::SETTING_LISTS_PATH,
				array()
			),
		);

		foreach ( $configs as $config_path => &$config_values ) {
			$order = 1;
			foreach ( $config_values as $key => &$value ) {
				if ( ! isset( $value['order'] ) ) {
					$value['order'] = $order;
					$order++;
				}
			}
			update_option( $config_path, $config_values );
		}

		update_option(
			Dotdigital_WordPress_Config::SETTING_DOTDIGITAL_WORDPRESS_VERSION,
			DOTDIGITAL_WORDPRESS_VERSION,
			DOTDIGITAL_WORDPRESS_VERSION
		);
	}

	/**
	 * @inheritDoc
	 */
	public static function rollback_patch() {}

	/**
	 * @inheritDoc
	 */
	public static function get_patch_version() {
		return '7.3.0';
	}

	/**
	 * @inheritDoc
	 */
	public static function can_apply(): bool {
		return version_compare( DOTDIGITAL_WORDPRESS_VERSION, '7.3.0', '>=' );
	}
}
