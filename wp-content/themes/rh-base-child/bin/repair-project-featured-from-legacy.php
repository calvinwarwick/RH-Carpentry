<?php
/**
 * Re-assign featured images for legacy-imported rh_project posts so each post owns
 * its own media file. Fixes duplicates caused when media_sideload_image() returned
 * the same attachment ID for the same source URL on different posts.
 *
 * Idempotent: skips posts whose featured upload basename already starts with
 * rh-project-{post_id}- (written by rh_project_sideload_image_unique_file()).
 * A second pass finds _thumbnail_id values still shared by multiple rh_project
 * posts and re-sideloads from the legacy URL map (covers edge cases such as
 * road-view appearing on many projects).
 *
 * Run from repo root:
 *   docker compose run --rm wpcli eval-file wp-content/themes/rh-base-child/bin/repair-project-featured-from-legacy.php
 *
 * @package RH_Base_Child
 */

if (! defined('ABSPATH')) {
	exit(1);
}

if (! post_type_exists('rh_project')) {
	if (defined('WP_CLI') && WP_CLI) {
		WP_CLI::error('Post type rh_project is not registered. Activate rh-base-child theme.');
	}
	exit(1);
}

if (! function_exists('rh_project_legacy_import_rows') || ! function_exists('rh_project_sideload_image_unique_file')) {
	if (defined('WP_CLI') && WP_CLI) {
		WP_CLI::error('Theme helpers missing. Ensure rh-base-child is active.');
	}
	exit(1);
}

$rows            = rh_project_legacy_import_rows();
$repaired        = 0;
$skipped_ok      = 0;
$skipped_missing = 0;
$failed          = 0;

foreach ($rows as $row) {
	$legacy_url = $row['url'];
	$image_url  = $row['img'];
	$title      = isset($row['title']) ? (string) $row['title'] : '';

	$posts = get_posts(
		array(
			'post_type'              => 'rh_project',
			'post_status'            => 'any',
			'fields'                 => 'ids',
			'posts_per_page'         => 1,
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'meta_query'             => array(
				array(
					'key'   => 'rh_legacy_project_url',
					'value' => $legacy_url,
				),
			),
		)
	);

	if ($posts === array()) {
		++$skipped_missing;
		if (defined('WP_CLI') && WP_CLI) {
			WP_CLI::log('No post for legacy URL: ' . $legacy_url);
		}
		continue;
	}

	$post_id = (int) $posts[0];
	$prefix  = 'rh-project-' . $post_id . '-';

	$thumb_id = (int) get_post_thumbnail_id($post_id);
	if ($thumb_id > 0) {
		$file = get_attached_file($thumb_id);
		$base = is_string($file) && $file !== '' ? basename($file) : '';
		if ($base !== '' && str_starts_with($base, $prefix)) {
			++$skipped_ok;
			if (defined('WP_CLI') && WP_CLI) {
				WP_CLI::log("Skip (already unique featured): {$title} ({$post_id})");
			}
			continue;
		}
	}

	$att_id = rh_project_sideload_image_unique_file($image_url, $post_id, $title);
	if (is_wp_error($att_id)) {
		++$failed;
		if (defined('WP_CLI') && WP_CLI) {
			WP_CLI::warning("Featured repair failed {$title} ({$post_id}): " . $att_id->get_error_message());
		}
		continue;
	}

	set_post_thumbnail($post_id, (int) $att_id);
	++$repaired;
	if (defined('WP_CLI') && WP_CLI) {
		WP_CLI::log("Repaired featured: {$title} ({$post_id}) → attachment {$att_id}");
	}
}

// Phase 2: same _thumbnail_id on multiple rh_project posts (e.g. road-view reused). Force unique sideload from legacy map.
global $wpdb;
$dup_thumb = $wpdb->get_col(
	"SELECT pm.meta_value FROM {$wpdb->postmeta} pm
	INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id AND p.post_type = 'rh_project' AND p.post_status != 'trash'
	WHERE pm.meta_key = '_thumbnail_id' AND pm.meta_value != '' AND pm.meta_value != '0'
	GROUP BY pm.meta_value HAVING COUNT(DISTINCT pm.post_id) > 1"
);

$url_to_img = array();
foreach ($rows as $r) {
	if (isset($r['url'], $r['img']) && is_string($r['url']) && $r['url'] !== '' && is_string($r['img']) && $r['img'] !== '') {
		$url_to_img[ $r['url'] ] = $r['img'];
	}
}

$dup_repaired = 0;
$dup_skipped  = 0;

foreach ($dup_thumb as $tid_raw) {
	$tid = (int) $tid_raw;
	if ($tid <= 0) {
		continue;
	}
	$post_ids = get_posts(
		array(
			'post_type'              => 'rh_project',
			'post_status'            => 'any',
			'posts_per_page'         => -1,
			'fields'                 => 'ids',
			'no_found_rows'          => true,
			'update_post_meta_cache' => true,
			'meta_key'               => '_thumbnail_id',
			'meta_value'             => (string) $tid,
		)
	);
	if (count($post_ids) < 2) {
		continue;
	}
	foreach ($post_ids as $post_id) {
		$post_id = (int) $post_id;
		$legacy  = get_post_meta($post_id, 'rh_legacy_project_url', true);
		if (! is_string($legacy) || $legacy === '' || ! isset($url_to_img[ $legacy ])) {
			++$dup_skipped;
			if (defined('WP_CLI') && WP_CLI) {
				WP_CLI::log("Duplicate thumb {$tid}: post {$post_id} has no legacy URL in import map — skipped.");
			}
			continue;
		}
		$img_url = $url_to_img[ $legacy ];
		$title   = get_the_title($post_id);
		$att_id  = rh_project_sideload_image_unique_file($img_url, $post_id, $title);
		if (is_wp_error($att_id)) {
			++$failed;
			if (defined('WP_CLI') && WP_CLI) {
				WP_CLI::warning("Duplicate-thumb repair failed post {$post_id}: " . $att_id->get_error_message());
			}
			continue;
		}
		set_post_thumbnail($post_id, (int) $att_id);
		++$dup_repaired;
		if (defined('WP_CLI') && WP_CLI) {
			WP_CLI::log("Duplicate-thumb repair: post {$post_id} ({$title}) → attachment {$att_id}");
		}
	}
}

if (defined('WP_CLI') && WP_CLI) {
	WP_CLI::success(
		sprintf(
			'Done. Repaired %d, skipped already-OK %d, no matching post %d, failed %d. Duplicate-thumb pass: repaired %d, skipped (no map) %d.',
			$repaired,
			$skipped_ok,
			$skipped_missing,
			$failed,
			$dup_repaired,
			$dup_skipped
		)
	);
}
