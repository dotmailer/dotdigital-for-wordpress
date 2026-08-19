<?php
/**
 * Provide a public-facing view for the widget error
 *
 * @package    Dotdigital_WordPress
 *
 * @var \Exception $exception
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
	<div>
		<p class='error_message'><?php echo esc_html( $exception->getMessage() ); ?></p>
	</div>
<?php

