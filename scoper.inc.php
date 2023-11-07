<?php
// @phpcs:ignoreFile
declare( strict_types=1 );

use Isolated\Symfony\Component\Finder\Finder;

/**
 *
 * To modify consult the PHP-Scoper template:
 * https://github.com/humbug/php-scoper/blob/main/src/scoper.inc.php.tpl
 */
return [
	'finders' => [
		Finder::create()
			->files()
			->ignoreVCS( true )
			->notName( '/.*\\.md|.*\\.dist|.*\\.lock|scoper\\.inc\\.php|.*\\.phar/' )
			->exclude( [
				'bin',
				'docs',
				'node_modules',
				'cypress',
				'tests',
				'build'
			] )
			->in( __DIR__ )
	],

	'patchers' => [],
	'exclude-namespaces' => [
		'Dotdigital_WordPress'
	],
	'exclude-files' => [
		'dm_signup_form.php',
		'includes/legacy/DM_Widget.php'
	],
	'exclude-classes' => [
		'WP_Widget',
		'DM_Widget',
		'WP_REST_Request'
	],
];
