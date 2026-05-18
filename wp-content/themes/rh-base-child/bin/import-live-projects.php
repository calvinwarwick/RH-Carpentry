<?php
/**
 * Import portfolio items from the legacy listing at
 * https://rhcarpentersukltd.co.uk/projects/ into rh_project posts.
 *
 * Idempotent: skips posts that already have post meta rh_legacy_project_url
 * matching the source work URL.
 *
 * Run from repo root (Docker mounts wp-content into the wpcli container):
 *   docker compose run --rm wpcli eval-file wp-content/themes/rh-base-child/bin/import-live-projects.php
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

/**
 * Map legacy filter label to taxonomy term name (matches rh_project_seed_sectors).
 */
function rh_import_live_map_sector(string $label): string {
	$label = trim(preg_replace('/[\x{200B}\x{FEFF}]/u', '', $label));
	if (strcasecmp($label, 'Barn Conversions') === 0) {
		return 'Barn conversions';
	}
	return $label;
}

/**
 * Strip zero-width characters from titles.
 */
function rh_import_live_clean_title(string $title): string {
	return trim(preg_replace('/[\x{200B}\x{FEFF}]/u', '', $title));
}

$rows    = rh_project_legacy_import_rows();
$added   = 0;
$skipped = 0;
$failed  = 0;

foreach ($rows as $row) {
	$title       = rh_import_live_clean_title($row['title']);
	$sector_name = rh_import_live_map_sector($row['sector']);
	$source_url  = $row['url'];
	$image_url   = $row['img'];

	$existing = get_posts(
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
					'value' => $source_url,
				),
			),
		)
	);

	if ($existing !== array()) {
		++$skipped;
		if (defined('WP_CLI') && WP_CLI) {
			WP_CLI::log("Skip (already imported): {$title}");
		}
		continue;
	}

	$post_id = wp_insert_post(
		array(
			'post_title'   => $title,
			'post_content' => '',
			'post_excerpt' => '',
			'post_status'  => 'publish',
			'post_type'    => 'rh_project',
			'meta_input'   => array(
				'rh_legacy_project_url' => $source_url,
			),
		),
		true
	);

	if (is_wp_error($post_id) || $post_id === 0) {
		++$failed;
		if (defined('WP_CLI') && WP_CLI) {
			WP_CLI::warning('Could not create post: ' . $title);
		}
		continue;
	}

	$term = get_term_by('name', $sector_name, 'rh_project_sector');
	if (! $term instanceof WP_Term) {
		$inserted = wp_insert_term($sector_name, 'rh_project_sector');
		if (is_wp_error($inserted)) {
			if (defined('WP_CLI') && WP_CLI) {
				WP_CLI::warning('Sector term missing and could not be created: ' . $sector_name);
			}
		} else {
			wp_set_object_terms((int) $post_id, array((int) $inserted['term_id']), 'rh_project_sector');
		}
	} else {
		wp_set_object_terms((int) $post_id, array((int) $term->term_id), 'rh_project_sector');
	}

	$att_id = rh_project_sideload_image_unique_file($image_url, (int) $post_id, $title);
	if (is_wp_error($att_id)) {
		if (defined('WP_CLI') && WP_CLI) {
			WP_CLI::warning('Featured image failed for ' . $title . ': ' . $att_id->get_error_message());
		}
	} else {
		set_post_thumbnail((int) $post_id, (int) $att_id);
	}

	++$added;
	if (defined('WP_CLI') && WP_CLI) {
		WP_CLI::log("Imported: {$title}");
	}
}

if (defined('WP_CLI') && WP_CLI) {
	WP_CLI::success(sprintf('Done. Added %d, skipped %d (already present), failed %d.', $added, $skipped, $failed));
}
