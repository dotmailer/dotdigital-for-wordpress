<?php
/**
 * Manages the application of patches for the Dotdigital WordPress plugin.
 *
 * @since 7.3.0
 * @package Dotdigital_WordPress
 */

namespace Dotdigital_WordPress\Includes;

use Dotdigital_WordPress\Includes\Setting\Dotdigital_WordPress_Config;
use Exception;

class Dotdigital_WordPress_Patch_Manager {

	/**
	 * @var string The name of the plugin.
	 *
	 * @since 7.3.0
	 */
	private $plugin_name;

	/**
	 * @var string The current version of the plugin.
	 *
	 * @since 7.3.0
	 */
	private $current_version;

	/**
	 * @var string The directory where patch files are stored.
	 *
	 * @since 7.3.0
	 */
	private $patches_directory;

	/**
	 * Constructor.
	 *
	 * @param string $plugin_name The name of the plugin.
	 *
	 * @since 7.3.0
	 */
	public function __construct( $plugin_name ) {
		$this->plugin_name = $plugin_name;
		$this->current_version = Dotdigital_WordPress_Config::get_version();
		$this->patches_directory = Dotdigital_WordPress_Config::get_patches_directory();
	}

	/**
	 * Checks and applies patches if necessary.
	 *
	 * @since 7.3.0
	 */
	public function check_patches() {
		$patches = $this->get_patches();
		$applied_patches = $this->get_applied_patches();

		foreach ( $patches as $patch ) {
			if ( ! $patch::can_apply() ) {
				error_log( sprintf( 'Patch %s cannot be applied', $patch ) );
				return;
			}

			if ( ! in_array( $patch, $applied_patches ) ) {
				try {
					$patch::apply_patch();
					$this->update_applied_patches( $patch );
				} catch ( Exception $e ) {
					$this->notify_admin( $e->getMessage() );
					$patch::rollback_patch();
					error_log( sprintf( 'Patch %s failed', $patch ) );
					return;
				}
			}
		}
	}

	/**
	 * Retrieves the list of patch classes from the patches directory.
	 *
	 * @return array An array of patch class names.
	 *
	 * @since 7.3.0
	 */
	public function get_patches() {
		$patches = array();
		$files = scandir( $this->patches_directory );
		foreach ( $files as $file ) {
			if ( '.' == $file || '..' == $file ) {
				continue;
			}
			$file_path = $this->patches_directory . '/' . $file;
			$class_name = $this->get_class_name_from_file( $file_path );
			if ( $class_name && $this->implements_patch_interface( $class_name ) ) {
				$patches[] = $class_name;
			}
		}
		return $patches;
	}

	/**
	 * Extracts the fully qualified class name from a file.
	 *
	 * @param string $file The file path.
	 * @return string|null The fully qualified class name or null if not found.
	 *
	 * @since 7.3.0
	 */
	public function get_class_name_from_file( $file ) {
		$contents = file_get_contents( $file );
		$namespace = '';
		if ( preg_match( '/namespace\s+([^;]+);/', $contents, $matches ) ) {
			$namespace = $matches[1] . '\\';
		}
		if ( preg_match( '/class\s+(\w+)/', $contents, $matches ) ) {
			$class_name = $namespace . $matches[1];
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG && defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
				error_log( 'Extracted class name: ' . $class_name ); // Add this line for logging.
			}
			return $class_name;
		}
		return null;
	}

	/**
	 * Checks if a class implements the Dotdigital_WordPress_Patch_Interface.
	 *
	 * @param string $class_name The fully qualified class name.
	 * @return bool True if the class implements the interface, false otherwise.
	 *
	 * @since 7.3.0
	 */
	public function implements_patch_interface( $class_name ) {
		if ( class_exists( $class_name ) ) {
			return in_array( 'Dotdigital_WordPress\Includes\Patches\Dotdigital_WordPress_Patch_Interface', class_implements( $class_name ) );
		}
		return false;
	}

	/**
	 * Retrieves the list of applied patches.
	 *
	 * @return array An array of applied patch class names.
	 *
	 * @since 7.3.0
	 */
	public function get_applied_patches() {
		return get_option( Dotdigital_WordPress_Config::SETTING_DOTDIGITAL_WORDPRESS_PATCHES, array() );
	}

	/**
	 * Updates the list of applied patches.
	 *
	 * @param string $applied_patch The class name of the applied patch.
	 *
	 * @since 7.3.0
	 */
	private function update_applied_patches( $applied_patch ) {
		error_log( sprintf( 'Updating applied patches with %s', $applied_patch ) );
		$applied_patches = $this->get_applied_patches();
		$applied_patches[] = $applied_patch;
		update_option( Dotdigital_WordPress_Config::SETTING_DOTDIGITAL_WORDPRESS_PATCHES, $applied_patches );
	}

	/**
	 * Notifies the admin with a message.
	 *
	 * @param string $amin_notice The message to display.
	 *
	 * @since 7.3.0
	 */
	private function notify_admin( $amin_notice = '' ) {
		add_action(
			'admin_notices',
			function () use ( $amin_notice ) {
				$message = $amin_notice;
				include plugin_dir_path( __FILE__ ) . 'admin/views/dotdigital-wordpress-admin-notice.php';
			}
		);
	}
}
