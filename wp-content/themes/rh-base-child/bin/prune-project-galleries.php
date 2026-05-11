<?php
/**
 * Remove logo/branding attachments and duplicates of the featured file from rh_project_gallery meta.
 *
 * Run: docker compose run --rm wpcli eval-file wp-content/themes/rh-base-child/bin/prune-project-galleries.php
 *
 * @package RH_Base_Child
 */

if (! defined('ABSPATH')) {
	exit(1);
}

if (! function_exists('rh_project_prune_stored_gallery_attachment_ids')) {
	if (defined('WP_CLI') && WP_CLI) {
		WP_CLI::error('Theme helpers not loaded (rh_project_prune_stored_gallery_attachment_ids).');
	}
	exit(1);
}

$post_ids = get_posts(
	array(
		'post_type'              => 'rh_project',
		'post_status'            => 'any',
		'posts_per_page'         => -1,
		'fields'                 => 'ids',
		'orderby'                => 'ID',
		'order'                  => 'ASC',
		'no_found_rows'          => true,
		'update_post_meta_cache' => true,
	)
);

$updated = 0;

foreach ($post_ids as $post_id) {
	$post_id = (int) $post_id;
	$old   = get_post_meta($post_id, 'rh_project_gallery', true);
	$new_n = rh_project_prune_stored_gallery_attachment_ids($post_id);
	$old_n = is_array($old) ? array_values(array_map('intval', $old)) : array();
	$old_s = $old_n;
	$new_s = $new_n;
	sort($old_s);
	sort($new_s);
	if ($old_s === $new_s) {
		continue;
	}
	if ($new_n === array()) {
		delete_post_meta($post_id, 'rh_project_gallery');
	} else {
		update_post_meta($post_id, 'rh_project_gallery', $new_n);
	}
	++$updated;
	if (defined('WP_CLI') && WP_CLI) {
		WP_CLI::log('Pruned gallery for post ' . $post_id . ' (' . count($old_n) . ' → ' . count($new_n) . ')');
	}
}

if (defined('WP_CLI') && WP_CLI) {
	WP_CLI::success('Pruned ' . $updated . ' project(s).');
}
