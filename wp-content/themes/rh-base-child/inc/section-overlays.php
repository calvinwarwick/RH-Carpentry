<?php
/**
 * About / Services section overlays (open on current page via #about, #services).
 *
 * @package RH_Base_Child
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
	exit;
}

/**
 * Hash link that opens a section overlay on the current page.
 */
function rh_carpentry_section_overlay_href(string $fragment): string {
	return '#' . ltrim($fragment, '#');
}

/**
 * Nav / CTA: About landing page.
 */
function rh_carpentry_about_page_url(): string {
	return rh_carpentry_about_landing_url();
}

/**
 * Nav / CTA: Services hub landing page.
 */
function rh_carpentry_services_hub_url(): string {
	return rh_carpentry_services_landing_url();
}

/**
 * SEO landing page permalink for About (full page).
 */
function rh_carpentry_about_landing_url(): string {
	$page = rh_carpentry_get_page_by_path('about');
	if ($page instanceof WP_Post) {
		return (string) get_permalink($page);
	}
	return home_url('/about/');
}

/**
 * SEO landing page permalink for Services hub (full page).
 */
function rh_carpentry_services_landing_url(): string {
	$page = rh_carpentry_get_page_by_path('services');
	if ($page instanceof WP_Post) {
		return (string) get_permalink($page);
	}
	return home_url('/services/');
}

