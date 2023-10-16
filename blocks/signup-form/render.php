<?php
/**
 * Block Name: Dotdigital Signup Form
 *
 * @package dotdigital/dotdigital-signup-form
 * @var array $attributes
 */

the_widget(
	'DM_Widget',
	array(),
	array(
		'showtitle' => $attributes['showtitle'] ?? false,
		'showdesc' => $attributes['showdesc'] ?? false,
		'redirection' => $attributes['redirecturl'] ?? false,
	)
);
