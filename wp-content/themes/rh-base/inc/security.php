<?php
/**
 * Hardening helpers (non-destructive defaults).
 *
 * @package RH_Base
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
	exit;
}

/**
 * Remove WordPress version from asset query strings (minor obscurity).
 *
 * @param string $src Source URL.
 * @return string
 */
function rh_base_remove_wp_version_strings(string $src): string {
	global $wp_version;
	if (strpos($src, 'ver=' . $wp_version) !== false) {
		$src = remove_query_arg('ver', $src);
	}
	return $src;
}
add_filter('script_loader_src', 'rh_base_remove_wp_version_strings', 15);
add_filter('style_loader_src', 'rh_base_remove_wp_version_strings', 15);
