<?php
/**
 * About tab view
 *
 * This file is used to display the about tab
 *
 * @var string $plugin_version
 * @var string[] $applied_patches
 * @var string[] $not_applied_patches
 *
 * @package    Dotdigital_WordPress
 * @since      7.3.0
 */

?>
<div class="wrap">
	<table width="100%" cellspacing="0" cellpadding="0">
		<tbody>
		<tr valign="top">
			<td>
				<div class="card w-100 widefat">
					<h3>What it does</h3>
					<p>Add the Dotdigital for WordPress plugin to your site and allow your visitors to sign up to your Dotdigital-powered newsletter and email marketing campaigns. The email addresses of new subscribers can be added to multiple Dotdigital lists, and you can capture contact data fields too.</p>
					<p>If you're not a Dotdigital user already you can find out more about us at <a href="https://www.dotdigital.com">dotdigital.com</a>.</p>
					<p></p>
					<a href="https://wordpress.org/plugins/dotmailer-sign-up-widget/#developers" target="_blank">See the full changelog here...</a>
				</div>

				<div class="card w-100 widefat">
					<h3>Setup advice</h3>
					<p>To get you up and running, we have full setup instructions on the <a href="https://support.dotdigital.com/hc/en-gb/articles/212216058-Using-the-dotmailer-WordPress-sign-up-form-plugin-v2" target="_blank">Dotdigital knowledge base</a>.</p>
				</div>

				<div class="card w-100 widefat">
					<h3>Current plugin version</h3>
					<p><?php echo esc_html( $plugin_version ); ?></p>

					<h3>Patches</h3>
					<ul>
						<?php foreach ( $applied_patches as $patch_class ) : ?>
							<li><?php echo esc_html( $patch_class ); ?> - <strong>Applied</strong></li>
						<?php endforeach; ?>
						<?php foreach ( $not_applied_patches as $patch_class ) : ?>
							<li><?php echo esc_html( $patch_class ); ?> - <strong>Not applied</strong></li>
						<?php endforeach; ?>
					</ul>
				</div>

			</td>
			<td width="10"></td>
			<td width="350">
				<div class="card w-100 widefat">
					<img src="<?php echo esc_html( plugins_url( '../../../assets/dotdigital-logo.png', __FILE__ ) ); ?>" alt="dotdigital" />
					<p>Powerful email marketing made easy - with the most intuitive, easy to use email marketing platform you will find. Grab yourself a free 30-day trial now from our website.&nbsp;Visit <a href="http://dotdigital.com" target="_blank">dotdigital.com &gt;&gt;</a></p>
				</div>
			</td>
		</tr>
		</tbody>
	</table>
</div>
