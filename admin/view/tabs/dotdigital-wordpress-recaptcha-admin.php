<?php
/**
 * Credentials tab view
 *
 * This file is used to display the credentials tab
 *
 * @package    Dotdigital_WordPress
 *
 * @var array $account_info
 * @var \Dotdigital_WordPress\Admin\Page\Tab\Dotdigital_WordPress_Credentials_Admin $view
 * @var \Dotdigital_WordPress\Includes\Setting\Form\Dotdigital_WordPress_Setting_Form $form
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Dotdigital_WordPress\Admin\Page\Tab\Dotdigital_WordPress_Credentials_Admin;
use Dotdigital_WordPress\Includes\Setting\Form\Dotdigital_WordPress_Setting_Form;

?>
<div class="wrap">
	<div class="card w-100 widefat">
		<p>Protect your form submissions from spam using Google reCAPTCHA v3. <a href="https://cloud.google.com/recaptcha/docs/overview" target="_blank">Read more</a>.</p>
		<?php $form->render(); ?>
		<p>Register reCAPTCHA v3 keys on the reCAPTCHA <a href="https://www.google.com/recaptcha/admin/create" target="_blank">Admin console</a>.</p>
	</div>
</div>
