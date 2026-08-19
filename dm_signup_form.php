<?php // phpcs:disable WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * The plugin bootstrap file
 *
 * @package    Dotdigital_WordPress
 *
 * @wordpress-plugin
 * Plugin Name:       Dotdigital for WordPress
 * Plugin URI:        https://integrations.dotdigital.com/technology-partners/wordpress
 * Description:       Add a "Subscribe to Newsletter" widget to your website that will insert your contact in one of your Dotdigital lists.
 * Version:           7.5.0
 * Requires PHP:      7.4
 * Requires at least: 5.8
 * Author:            dotdigital
 * Author URI:        https://www.dotdigital.com/
 * License:           MIT
 * License URI:       https://opensource.org/licenses/MIT
 * Text Domain:       dotdigital-for-wordpress
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once __DIR__ . '/vendor/autoload.php';

define( 'DOTDIGITAL_WORDPRESS_VERSION', '7.5.0' );
define( 'DOTDIGITAL_WORDPRESS_PLUGIN_NAME', 'dotdigital-for-wordpress' );
define( 'DOTDIGITAL_WORDPRESS_PLUGIN_SLUG', 'dotdigital_for_wordpress' );
define( 'DOTDIGITAL_WORDPRESS_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'DOTDIGITAL_WORDPRESS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'DOTDIGITAL_WORDPRESS_PLUGIN_ICON', 'PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz4KPHN2ZyBpZD0iQ2FwYV8xIiBkYXRhLW5hbWU9IkNhcGEgMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB2aWV3Qm94PSIwIDAgNjgyIDY4MiI+CiAgPGRlZnM+CiAgICA8c3R5bGU+CiAgICAgIC5jbHMtMSB7CiAgICAgICAgZmlsbDogI2ZmZjsKICAgICAgfQogICAgPC9zdHlsZT4KICA8L2RlZnM+CiAgPHBhdGggY2xhc3M9ImNscy0xIiBkPSJNMjAwLjk0LDI2NS43OWMzNS44MiwwLDY0Ljg2LTI5LjA0LDY0Ljg2LTY0Ljg2cy0yOS4wNC02NC44Ni02NC44Ni02NC44NiwyOS4wNC02NC44Niw2NC44NiwyOS4wNCw2NC44Niw2NC44Niw2NC44NloiLz4KICA8cGF0aCBjbGFzcz0iY2xzLTEiIGQ9Ik01MzUuOTIsMjY2LjE5Yy0xMC44NywwLTI3Ljc4LDE2Ljg4LTQzLjI2LDQxLjQ0LTEuMTcsMS44NS0zLjIyLDMuMDMtNS40MywzLjAzLTQuMzksMC03LjQ4LTQuMzYtNi4wMi04LjUyLDIxLjkyLTYxLjk3LDIzLjI5LTEwNy41LjgxLTEwNy41LTE4LjgsMC00OS4zOCwzMS45NC03OC4zMyw3OC43NS0xLjMsMi4xMS0zLjYxLDMuNDItNi4wOCwzLjQyLTQuODUsMC04LjI5LTQuNzUtNi44LTkuMzQsMjQuODItNzYuODMsMjUuNDctMTMxLjM4LTIuMjQtMTMxLjM4LTM1Ljk0LDAtMTA1LjkxLDkxLjc2LTE1Ni4zLDIwNC45Mi01MC4zOSwxMTMuMTYtNjIuMTMsMjA0LjkzLTI2LjE4LDIwNC45MywyOS40LDAsODEuNTgtNjEuNDQsMTI3LjE4LTE0NS43OSwxLjA3LTEuOTgsMy4xMi0zLjIyLDUuNC0zLjIyLDQuMDcsMCw2Ljk5LDMuOSw1Ljg5LDcuODEtMTMuODksNDguODktMTIuMiw4Mi42NSw3LjEyLDgyLjY1LDIyLjAyLDAsNjAuMTEtNDMuNzgsOTIuOTYtMTAzLjg5LDEuNDMtMi42Myw0LjIzLTQuMzMsNy4yMi00LjMzLDUuMDQsMCw4Ljg1LDQuNDksOC4xMyw5LjQ2LTIuMzEsMTYuNTkuNjUsMjcuMTksOS4yNywyNy4xOSwxNS4yOSwwLDQyLjYxLTMzLjUsNjEuMDItNzQuODEsMTguNDEtNDEuMzEsMjAuOTEtNzQuODEsNS41OS03NC44MWguMDNaIi8+Cjwvc3ZnPgo=' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-dotdigital-wordpress-activator.php
 */
function activate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-dotdigital-wordpress-activator.php';
	\Dotdigital_WordPress\Includes\Dotdigital_WordPress_Activator::activate();
}
register_activation_hook( __FILE__, 'activate' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-dotdigital-wordpress.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 */
function run_dotdigital_wordpress() {

	$plugin = new \Dotdigital_WordPress\Includes\Dotdigital_WordPress();
	$plugin->run();
}
run_dotdigital_wordpress();
