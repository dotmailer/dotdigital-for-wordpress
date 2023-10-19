<?php
/**
 * Print out widget success/error messages.
 *
 * @package    Dotdigital_WordPress
 *
 * @var \Dotdigital_WordPress\Includes\Widget\Dotdigital_WordPress_Sign_Up_Widget $widget
 */

?>
	<div class="form_messages">
		<p class='<?php echo esc_attr( $widget->get_message_class( $widget->id ) ); ?>'><?php echo esc_html( $widget->get_message( $widget->id ) ); ?></p>
	</div>
<?php

