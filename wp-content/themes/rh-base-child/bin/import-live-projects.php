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

require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

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

/**
 * @return array<int, array{title: string, sector: string, url: string, img: string}>
 */
function rh_import_live_project_rows(): array {
	return array(
		array(
			'title'  => 'Barton Road',
			'sector' => 'New builds',
			'url'    => 'https://rhcarpentersukltd.co.uk/work/barton-road/',
			'img'    => 'https://rhcarpentersukltd.co.uk/wp-content/uploads/2024/07/road-view-480x568.jpg',
		),
		array(
			'title'  => 'Victoria Espange',
			'sector' => 'New builds',
			'url'    => 'https://rhcarpentersukltd.co.uk/work/victoria-espange/',
			'img'    => 'https://rhcarpentersukltd.co.uk/wp-content/uploads/2024/07/annexe-and-garage-480x568.jpg',
		),
		array(
			'title'  => '7 Dwellings',
			'sector' => 'New builds',
			'url'    => 'https://rhcarpentersukltd.co.uk/work/7-dwellings/',
			'img'    => 'https://rhcarpentersukltd.co.uk/wp-content/uploads/2024/07/IMG-1673-853x568.jpg',
		),
		array(
			'title'  => 'Willow Cottage',
			'sector' => 'Refurbishment',
			'url'    => 'https://rhcarpentersukltd.co.uk/work/willow-cottage/',
			'img'    => 'https://rhcarpentersukltd.co.uk/wp-content/uploads/2024/07/31841564_238704146873792_2244665198696726528_n-853x568.jpg',
		),
		array(
			'title'  => 'Flat 3',
			'sector' => 'Refurbishment',
			'url'    => 'https://rhcarpentersukltd.co.uk/work/flat-3/',
			'img'    => 'https://rhcarpentersukltd.co.uk/wp-content/uploads/2024/07/IMG_2218-853x568.jpg',
		),
		array(
			'title'  => 'Allen House',
			'sector' => 'Refurbishment',
			'url'    => 'https://rhcarpentersukltd.co.uk/work/allen-house/',
			'img'    => 'https://rhcarpentersukltd.co.uk/wp-content/uploads/2024/07/KMTT2922.jpg',
		),
		array(
			'title'  => 'Holts Court',
			'sector' => 'Barn Conversions',
			'url'    => 'https://rhcarpentersukltd.co.uk/work/holts-court/',
			'img'    => 'https://rhcarpentersukltd.co.uk/wp-content/uploads/2018/09/423-853x568.jpg',
		),
		array(
			'title'  => 'Kirby-le-Soken',
			'sector' => 'Barn Conversions',
			'url'    => 'https://rhcarpentersukltd.co.uk/work/kirby-le-soken/',
			'img'    => 'https://rhcarpentersukltd.co.uk/wp-content/uploads/2018/09/025-853x568.jpg',
		),
		array(
			'title'  => 'Papworth Village Hall',
			'sector' => 'Refurbishment',
			'url'    => 'https://rhcarpentersukltd.co.uk/work/papworth-village-hall/',
			'img'    => 'https://rhcarpentersukltd.co.uk/wp-content/uploads/2018/09/r-cam-31.3.15-709-853x568.jpg',
		),
		array(
			'title'  => 'Salvation Army, Colchester',
			'sector' => 'Refurbishment',
			'url'    => 'https://rhcarpentersukltd.co.uk/work/salvation-army-colchester/',
			'img'    => 'https://rhcarpentersukltd.co.uk/wp-content/uploads/2018/09/020-853x568.jpg',
		),
		array(
			'title'  => 'Barn Cafe',
			'sector' => 'Hospitality',
			'url'    => 'https://rhcarpentersukltd.co.uk/work/barn-cafe/',
			'img'    => 'https://rhcarpentersukltd.co.uk/wp-content/uploads/2018/09/small.jpg',
		),
		array(
			'title'  => 'John Warner School',
			'sector' => 'Education',
			'url'    => 'https://rhcarpentersukltd.co.uk/work/john-warner-school/',
			'img'    => 'https://rhcarpentersukltd.co.uk/wp-content/uploads/2018/09/SAM_1073-853x568.jpg',
		),
		array(
			'title'  => 'Birch House',
			'sector' => 'New builds',
			'url'    => 'https://rhcarpentersukltd.co.uk/work/birch-house/',
			'img'    => 'https://rhcarpentersukltd.co.uk/wp-content/uploads/2018/09/Untitled-1-853x568.jpg',
		),
		array(
			'title'  => 'Lt.Clacton',
			'sector' => 'New builds',
			'url'    => 'https://rhcarpentersukltd.co.uk/work/lt-clacton/',
			'img'    => 'https://rhcarpentersukltd.co.uk/wp-content/uploads/2018/09/1-3-853x568.jpg',
		),
		array(
			'title'  => 'Kirby',
			'sector' => 'New builds',
			'url'    => 'https://rhcarpentersukltd.co.uk/work/kirby/',
			'img'    => 'https://rhcarpentersukltd.co.uk/wp-content/uploads/2018/09/2-1-853x568.jpg',
		),
		array(
			'title'  => 'New House, Langham',
			'sector' => 'New builds',
			'url'    => 'https://rhcarpentersukltd.co.uk/work/new-house-langham/',
			'img'    => 'https://rhcarpentersukltd.co.uk/wp-content/uploads/2018/09/410-853x568.jpg',
		),
		array(
			'title'  => 'Muckmurdos',
			'sector' => 'New builds',
			'url'    => 'https://rhcarpentersukltd.co.uk/work/muckmurdos/',
			'img'    => 'https://rhcarpentersukltd.co.uk/wp-content/uploads/2018/09/r-cam-31.3.15-749-853x568.jpg',
		),
		array(
			'title'  => 'Garage Cart Lodge',
			'sector' => 'New builds',
			'url'    => 'https://rhcarpentersukltd.co.uk/work/garage-cart-lodge/',
			'img'    => 'https://rhcarpentersukltd.co.uk/wp-content/uploads/2018/09/1-1-853x568.jpg',
		),
		array(
			'title'  => 'Bromley Road',
			'sector' => 'New builds',
			'url'    => 'https://rhcarpentersukltd.co.uk/work/bromley-road/',
			'img'    => 'https://rhcarpentersukltd.co.uk/wp-content/uploads/2018/09/477-1-853x568.jpg',
		),
		array(
			'title'  => 'Collingwood Road',
			'sector' => 'New builds',
			'url'    => 'https://rhcarpentersukltd.co.uk/work/collingwood-road/',
			'img'    => 'https://rhcarpentersukltd.co.uk/wp-content/uploads/2016/03/Debugh-Road-51-853x568.jpg',
		),
	);
}

$rows    = rh_import_live_project_rows();
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

	$att_id = media_sideload_image($image_url, (int) $post_id, $title, 'id');
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
