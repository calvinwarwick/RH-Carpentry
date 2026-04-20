<?php
/**
 * RH Base parent theme.
 *
 * @package RH_Base
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
	exit;
}

$rh_base_dir = get_template_directory();

$rh_base_includes = array(
	'/inc/setup.php',
	'/inc/enqueue.php',
	'/inc/menus.php',
	'/inc/template-tags.php',
	'/inc/security.php',
);

foreach ($rh_base_includes as $file) {
	$path = $rh_base_dir . $file;
	if (is_readable($path)) {
		require_once $path;
	}
}
