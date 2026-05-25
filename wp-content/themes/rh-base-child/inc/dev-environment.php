<?php
/**
 * Local / development environment helpers.
 *
 * @package RH_Base_Child
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
	exit;
}

/**
 * True when this install should use dev-only behaviour (e.g. green favicon).
 */
function rh_carpentry_is_dev_environment(): bool {
	if (defined('RH_IS_DEV') && RH_IS_DEV) {
		return true;
	}

	if (function_exists('wp_get_environment_type')) {
		$env = wp_get_environment_type();
		if ($env === 'local' || $env === 'development') {
			return true;
		}
	}

	$host = wp_parse_url(home_url(), PHP_URL_HOST);
	if (! is_string($host) || $host === '') {
		return false;
	}

	$host = strtolower($host);

	if ($host === 'localhost' || $host === '127.0.0.1') {
		return true;
	}

	if (str_ends_with($host, '.local') || str_ends_with($host, '.test')) {
		return true;
	}

	return false;
}

/**
 * Green-circle favicon URL for local development.
 */
function rh_carpentry_dev_favicon_url(): string {
	return get_stylesheet_directory_uri() . '/assets/images/dev-favicon.svg';
}

/**
 * Swap the site icon on dev so live favicon is not confused with local.
 */
function rh_carpentry_register_dev_favicon(): void {
	if (! rh_carpentry_is_dev_environment()) {
		return;
	}

	add_filter(
		'get_site_icon_url',
		static function ($url) {
			return rh_carpentry_dev_favicon_url();
		},
		10,
		1
	);

	add_filter(
		'site_icon_meta_tags',
		static function () {
			$url = esc_url(rh_carpentry_dev_favicon_url());

			return array(
				'<link rel="icon" href="' . $url . '" type="image/svg+xml" sizes="any" />',
				'<link rel="shortcut icon" href="' . $url . '" type="image/svg+xml" />',
				'<link rel="apple-touch-icon" href="' . $url . '" />',
			);
		}
	);
}
add_action('after_setup_theme', 'rh_carpentry_register_dev_favicon');
