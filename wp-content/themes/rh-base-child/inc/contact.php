<?php
/**
 * Contact overlay routing — opens #contact on the current page (or home).
 *
 * @package RH_Base_Child
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
	exit;
}

/**
 * Canonical URL for the page being viewed (no hash or contact query).
 */
function rh_carpentry_current_page_url(): string {
	if (is_front_page()) {
		return trailingslashit(home_url('/'));
	}

	if (is_singular()) {
		$permalink = get_permalink();
		if (is_string($permalink) && $permalink !== '') {
			return $permalink;
		}
	}

	$queried = get_queried_object();
	if ($queried instanceof WP_Term) {
		$link = get_term_link($queried);
		if (! is_wp_error($link)) {
			return (string) $link;
		}
	}

	if (is_post_type_archive()) {
		$post_type = get_query_var('post_type');
		if (is_array($post_type)) {
			$post_type = reset($post_type);
		}
		if (is_string($post_type) && $post_type !== '') {
			$link = get_post_type_archive_link($post_type);
			if (is_string($link) && $link !== '') {
				return $link;
			}
		}
	}

	$path = isset($_SERVER['REQUEST_URI']) ? (string) wp_parse_url(wp_unslash($_SERVER['REQUEST_URI']), PHP_URL_PATH) : '/';
	if (! is_string($path) || $path === '') {
		$path = '/';
	}

	return user_trailingslashit(home_url($path));
}

/**
 * Base URL to return to after a non-AJAX form post (same site referer when possible).
 */
function rh_carpentry_contact_return_base_url(): string {
	$referer = wp_get_referer(false);
	if (is_string($referer) && $referer !== '') {
		$referer = remove_query_arg('contact', $referer);
		$referer = preg_replace('/#.*$/', '', $referer) ?? $referer;

		$referer_host = wp_parse_url($referer, PHP_URL_HOST);
		$site_host    = wp_parse_url(home_url(), PHP_URL_HOST);
		if (
			is_string($referer_host) &&
			is_string($site_host) &&
			strtolower($referer_host) === strtolower($site_host)
		) {
			return $referer;
		}
	}

	return trailingslashit(home_url('/'));
}

/**
 * URL that opens the contact overlay (hash + optional status query).
 *
 * @param string      $status   Optional contact form status (sent, failed, etc.).
 * @param string|null $base_url Page URL without hash; defaults to the current request.
 */
function rh_carpentry_contact_overlay_url(string $status = '', ?string $base_url = null): string {
	$url = $base_url ?? rh_carpentry_current_page_url();
	if ($status !== '') {
		$url = add_query_arg('contact', sanitize_key($status), $url);
	}

	return $url . '#contact';
}

/**
 * “Get in touch” / Contact links — overlay on the current page, home on the front page.
 */
function rh_carpentry_contact_url(): string {
	return rh_carpentry_contact_overlay_url();
}

/**
 * Redirect /contact/ and the contact page template to the homepage overlay.
 */
function rh_carpentry_redirect_contact_page_to_overlay(): void {
	if (is_admin() || wp_doing_ajax() || wp_doing_cron()) {
		return;
	}

	$is_contact = false;
	if (is_page()) {
		$post = get_queried_object();
		if ($post instanceof WP_Post && $post->post_name === 'contact') {
			$is_contact = true;
		}
	}

	$path = isset($_SERVER['REQUEST_URI']) ? (string) wp_parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) : '';
	$path = '/' . trim($path, '/') . '/';
	if ($path === '/contact/') {
		$is_contact = true;
	}

	if (! $is_contact) {
		return;
	}

	$status = isset($_GET['contact']) ? sanitize_key(wp_unslash((string) $_GET['contact'])) : '';
	$target = rh_carpentry_contact_overlay_url($status);

	// phpcs:ignore WordPress.Security.SafeRedirect.wp_redirect_wp_redirect -- fragment required for overlay.
	wp_redirect($target, 302);
	exit;
}
add_action('template_redirect', 'rh_carpentry_redirect_contact_page_to_overlay', 1);
