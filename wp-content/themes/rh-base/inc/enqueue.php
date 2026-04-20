<?php
/**
 * Enqueue scripts and styles.
 *
 * @package RH_Base
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
	exit;
}

/**
 * Front-end assets.
 */
function rh_base_enqueue_assets(): void {
	$theme_version = wp_get_theme()->get('Version');
	$template_uri  = get_template_directory_uri();

	wp_enqueue_style(
		'rh-base-style',
		$template_uri . '/build/main.css',
		array(),
		$theme_version
	);

	wp_enqueue_script(
		'rh-base-main',
		$template_uri . '/build/main.js',
		array(),
		$theme_version,
		true
	);

	if (is_singular() && comments_open() && get_option('thread_comments')) {
		wp_enqueue_script('comment-reply');
	}
}
add_action('wp_enqueue_scripts', 'rh_base_enqueue_assets');

/**
 * Optional JS “island” — loaded only when the template requests it.
 */
function rh_base_enqueue_interactive(): void {
	if (! is_page_template('templates/template-with-interactive.php')) {
		return;
	}

	$theme_version = wp_get_theme()->get('Version');
	$template_uri  = get_template_directory_uri();

	wp_enqueue_script(
		'rh-base-interactive',
		$template_uri . '/build/rh-base-interactive.js',
		array(),
		$theme_version,
		true
	);
}
add_action('wp_enqueue_scripts', 'rh_base_enqueue_interactive', 20);
