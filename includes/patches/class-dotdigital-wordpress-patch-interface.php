<?php
/**
 * Interface Dotdigital_WordPress_Patch_Interface
 *
 * This interface defines the structure for patch classes in the Dotdigital WordPress plugin.
 * Each patch class must implement the methods defined in this interface.
 *
 * @package Dotdigital_WordPress
 * @since 7.3.0
 */

namespace Dotdigital_WordPress\Includes\Patches;

interface Dotdigital_WordPress_Patch_Interface {

	/**
	 * Apply the patch.
	 *
	 * This method contains the logic to apply the patch.
	 *
	 * @since 7.3.0
	 * @return void
	 */
	public static function apply_patch();

	/**
	 * Rollback the patch.
	 *
	 * This method contains the logic to rollback the patch.
	 *
	 * @since 7.3.0
	 * @return void
	 */
	public static function rollback_patch();

	/**
	 * Get the patch version.
	 *
	 * This method returns the version of the patch.
	 *
	 * @since 7.3.0
	 * @return string The version of the patch.
	 */
	public static function get_patch_version();

	/**
	 * Can apply patch.
	 *
	 * Allows you to add critical conditions before apply a patch.
	 *
	 * @since 7.3.0
	 * @return bool True if the patch can be applied, false otherwise.
	 */
	public static function can_apply(): bool;
}
