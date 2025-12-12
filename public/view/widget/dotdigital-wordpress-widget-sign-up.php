<?php
/**
 * The file that is the render for the sign up widget
 *
 * @package    Dotdigital_WordPress
 */

use Dotdigital_WordPress\Includes\Widget\Dotdigital_WordPress_Sign_Up_Widget;

/**
 * Provide a public-facing view for the widget
 *
 * @package    Dotdigital_WordPress
 *
 * @var Dotdigital_WordPress_Sign_Up_Widget $widget
 * @var bool $showtitle
 * @var bool $showdesc
 * @var mixed $redirection
 * @var bool $is_ajax
 * @var string $dd_widget_id
 * @var string $recaptcha_site_key
 * @var string $recaptcha_secret_key
 * @var bool $recaptcha_hide_badge
 */
?>
<?php if ( $recaptcha_hide_badge ) : ?>
<style>.grecaptcha-badge { visibility: hidden; }</style>
<?php endif; ?>
<form id="<?php echo esc_attr( $dd_widget_id ); ?>" class="dotdigital-signup-form widget" method="post" action="<?php echo esc_attr( rest_url( 'dotdigital/v1/signup-widget' ) ); ?>">
	<div class="<?php echo esc_attr( DOTDIGITAL_WORDPRESS_PLUGIN_NAME ); ?>-widget-title">
		<?php if ( $showtitle ) : ?>
			<h2><?php echo esc_html( $widget->get_form_title() ); ?></h2>
		<?php endif; ?>
	</div>

	<div class="<?php echo esc_attr( DOTDIGITAL_WORDPRESS_PLUGIN_NAME ); ?>-widget-description">
		<?php if ( $showdesc ) : ?>
			<p><?php echo esc_html( __( 'Please complete the fields below:', 'dotdigital-for-wordpress' ) ); ?></p>
		<?php endif; ?>
	</div>

	<div class="ddg-form-group">
		<label for="<?php echo esc_attr( apply_filters( 'dotdigital_email_input_id', 'email', $dd_widget_id ) ); ?>">
			<?php echo esc_html( __( 'Your email address*:', 'dotdigital-for-wordpress' ) ); ?>
		</label>
		<input class="email" type="email" id="<?php echo esc_attr( apply_filters( 'dotdigital_email_input_id', 'email', $dd_widget_id ) ); ?>" name="email" required/>
	</div>

	<?php do_action( DOTDIGITAL_WORDPRESS_PLUGIN_NAME . '-public-datafields', $dd_widget_id ); ?>
	<?php do_action( DOTDIGITAL_WORDPRESS_PLUGIN_NAME . '-public-lists', $dd_widget_id ); ?>
	<input type="hidden" name="redirection" value="<?php echo esc_attr( $redirection ); ?>" />
	<input type="hidden" name="widget_id" value="<?php echo esc_attr( $dd_widget_id ); ?>" />
	<input type="hidden" name="origin" value="<?php echo esc_attr( $widget->get_origin_url() ); ?>" />
	<input type="hidden" name="is_ajax" value="<?php echo $is_ajax ? '1' : '0'; ?>" />
	<?php if ( $recaptcha_site_key && $recaptcha_secret_key ) : ?>
	<input type="hidden" name="recaptcha_response_<?php echo esc_attr( $dd_widget_id ); ?>" id="recaptcha_response_<?php echo esc_attr( $dd_widget_id ); ?>" value="">
	<?php endif; ?>
	<input type="text" name="dm_name" style="display:none !important" tabindex="-1" autocomplete="new-password" />
	<div class="dotdigital-form-submit">
		<button type="submit"  name="dm_submit_btn"><?php echo esc_attr( $widget->get_subscribe_button_title() ); ?></button>
	</div>
</form>
