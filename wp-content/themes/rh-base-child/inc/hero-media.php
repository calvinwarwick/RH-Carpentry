<?php
/**
 * Seed default hero image into the Media Library (one-time).
 *
 * @package RH_Base_Child
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
	exit;
}

/**
 * Copy bundled hero JPEG into uploads, attach to Media, set Customizer theme mod.
 */
function rh_carpentry_import_default_hero_if_needed(): void {
	$mod_id = (int) get_theme_mod('rh_hero_background_id', 0);
	if ($mod_id > 0 && wp_attachment_is_image($mod_id)) {
		return;
	}

	// One-time import only (avoids duplicate uploads). If the Customizer image is cleared, the hero falls back to the bundled theme file until a new image is chosen.
	if ((int) get_option('rh_carpentry_hero_attachment_id', 0) > 0) {
		return;
	}

	$source = get_stylesheet_directory() . '/assets/images/hero-default.jpg';
	if (! is_readable($source)) {
		return;
	}

	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$filename = 'rh-carpentry-hero-default.jpg';
	$contents = file_get_contents($source);
	if ($contents === false) {
		return;
	}

	$upload = wp_upload_bits($filename, null, $contents);
	if (! empty($upload['error'])) {
		return;
	}

	$filetype = wp_check_filetype(basename($upload['file']), null);
	$attachment_id = wp_insert_attachment(
		array(
			'post_mime_type' => $filetype['type'],
			'post_title'     => __('RH Carpentry hero background', 'rh-base-child'),
			'post_content'     => '',
			'post_status'      => 'inherit',
		),
		$upload['file']
	);

	if (is_wp_error($attachment_id) || ! $attachment_id) {
		return;
	}

	$metadata = wp_generate_attachment_metadata((int) $attachment_id, $upload['file']);
	wp_update_attachment_metadata((int) $attachment_id, $metadata);

	$id = (int) $attachment_id;
	set_theme_mod('rh_hero_background_id', $id);
	update_option('rh_carpentry_hero_attachment_id', $id, false);
}
add_action('init', 'rh_carpentry_import_default_hero_if_needed', 2);
