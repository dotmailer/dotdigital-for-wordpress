<?php
/**
 * Admin notice template.
 *
 * This template is used to display an admin notice.
 *
 * @package    Dotdigital_WordPress
 * @var string $message
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="notice notice-success is-dismissible">
	<p><?php echo esc_html( $message ); ?></p>
</div>
