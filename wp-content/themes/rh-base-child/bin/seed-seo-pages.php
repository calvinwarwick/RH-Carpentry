<?php
/**
 * Seed SEO landing pages (WP-CLI or eval-file).
 *
 * Usage:
 *   wp eval-file wp-content/themes/rh-base-child/bin/seed-seo-pages.php
 *
 * @package RH_Base_Child
 */

if (! defined('ABSPATH')) {
	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput
	$wp_load = dirname(__DIR__, 4) . '/wp-load.php';
	if (! is_readable($wp_load)) {
		fwrite(STDERR, "Cannot find wp-load.php. Run via WP-CLI from site root.\n");
		exit(1);
	}
	require_once $wp_load;
}

if (! function_exists('rh_carpentry_seed_seo_pages')) {
	fwrite(STDERR, "rh-base-child theme not loaded.\n");
	exit(1);
}

delete_option('rh_seo_pages_seeded_version');
$stats = rh_carpentry_seed_seo_pages();
do_action('rh_seo_pages_seeded');

echo "SEO pages seeded.\n";
echo wp_json_encode($stats) . "\n";
