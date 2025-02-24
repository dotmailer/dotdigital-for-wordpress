<?php
/**
 * Admin notice template.
 *
 * This template is used to display an admin notice.
 *
 * @package    Dotdigital_WordPress
 * @var string $message
 */

?>
<div class="notice notice-success is-dismissible">
	<p><?php echo esc_html( $message ); ?></p>
</div>
