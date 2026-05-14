<?php
/**
 * Fetch legacy project pages (rh_legacy_project_url) and sideload gallery images into rh_project_gallery.
 *
 * Run: docker compose run --rm wpcli eval-file wp-content/themes/rh-base-child/bin/enrich-project-galleries.php
 * Re-run skips posts that already have rh_project_gallery meta; delete that meta to re-fetch.
 *
 * @package RH_Base_Child
 */

if (! defined('ABSPATH')) {
	exit(1);
}

require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

/**
 * @param string $url
 */
function rh_enrich_is_resized_upload_filename(string $url): bool {
	return (bool) preg_match('#-\d+x\d+\.(jpe?g|png|webp)$#i', $url);
}

/**
 * @param string $url
 */
function rh_enrich_is_tiny_brand_thumb(string $url): bool {
	return (bool) preg_match('#-(32x32|180x180|192x192|270x270)(\.[^.]+)$#i', $url);
}

/**
 * Normalised upload path key for deduping (drops -WxH- and -scaled before extension).
 *
 * @param string $url
 */
function rh_enrich_canonical_upload_key(string $url): string {
	$path = (string) wp_parse_url($url, PHP_URL_PATH);
	if ($path === '') {
		return strtolower($url);
	}
	$path = preg_replace('#-\d+x\d+(\.[^.]+)$#i', '$1', $path);
	$path = preg_replace('#-scaled(\.[^.]+)$#i', '$1', $path);
	return strtolower((string) $path);
}

/**
 * Site chrome / logos from the legacy theme header (not project photos).
 *
 * @param string $url Full image URL.
 */
function rh_enrich_is_brand_or_logo_url(string $url): bool {
	$path = strtolower((string) wp_parse_url($url, PHP_URL_PATH));
	if ($path === '') {
		return true;
	}
	if (preg_match('#/(logo|icons?|favicon)(/|\.|$)#', $path)) {
		return true;
	}
	if (strpos($path, 'cropped-') !== false) {
		return true;
	}
	if (strpos($path, 'apple-touch') !== false) {
		return true;
	}
	if (strpos($path, 'site-icon') !== false || strpos($path, 'site_icon') !== false) {
		return true;
	}
	if (strpos($path, 'logo') !== false && strpos($path, 'carpenter') !== false) {
		return true;
	}
	if (strpos($path, 'reversed_final') !== false) {
		return true;
	}
	return false;
}

/**
 * @param string $html
 * @return string[]
 */
function rh_enrich_extract_best_upload_urls(string $html): array {
	if ($html === '') {
		return array();
	}
	preg_match_all('#https?://rhcarpentersukltd\.co\.uk/wp-content/uploads/[^"\'\\s<>]+#i', $html, $m);
	$raw = isset($m[0]) && is_array($m[0]) ? $m[0] : array();
	$best = array();
	foreach ($raw as $url) {
		$url = is_string($url) ? str_replace('http://', 'https://', $url) : '';
		if ($url === '' || ! preg_match('#\.(jpe?g|png|webp)$#i', $url)) {
			continue;
		}
		if (rh_enrich_is_tiny_brand_thumb($url)) {
			continue;
		}
		if (rh_enrich_is_brand_or_logo_url($url)) {
			continue;
		}
		$key = rh_enrich_canonical_upload_key($url);
		if ($key === '') {
			continue;
		}
		if (! isset($best[ $key ])) {
			$best[ $key ] = $url;
			continue;
		}
		$prev = $best[ $key ];
		if (rh_enrich_is_resized_upload_filename($prev) && ! rh_enrich_is_resized_upload_filename($url)) {
			$best[ $key ] = $url;
		}
	}
	$out = array_values($best);
	if (count($out) > 18) {
		$out = array_slice($out, 0, 18);
	}
	return $out;
}

$posts = get_posts(
	array(
		'post_type'              => 'rh_project',
		'post_status'            => 'publish',
		'posts_per_page'         => -1,
		'orderby'                => 'ID',
		'order'                  => 'ASC',
		'fields'                 => 'ids',
		'no_found_rows'          => true,
		'update_post_meta_cache' => true,
	)
);

$done = 0;
$skipped = 0;

foreach ($posts as $post_id) {
	$post_id = (int) $post_id;
	$legacy  = (string) get_post_meta($post_id, 'rh_legacy_project_url', true);
	if ($legacy === '') {
		++$skipped;
		continue;
	}
	$existing = get_post_meta($post_id, 'rh_project_gallery', true);
	if (is_array($existing) && count($existing) >= 1) {
		if (defined('WP_CLI') && WP_CLI) {
			WP_CLI::log('Skip (gallery already set): post ' . $post_id);
		}
		++$skipped;
		continue;
	}

	$res = wp_remote_get(
		$legacy,
		array(
			'timeout' => 25,
			'headers' => array(
				'User-Agent' => 'RH-Carpentry-Local-Import/1.0',
			),
		)
	);
	if (is_wp_error($res)) {
		if (defined('WP_CLI') && WP_CLI) {
			WP_CLI::warning('Fetch failed ' . $post_id . ': ' . $res->get_error_message());
		}
		++$skipped;
		continue;
	}
	$html = wp_remote_retrieve_body($res);
	if (! is_string($html) || $html === '') {
		++$skipped;
		continue;
	}

	$urls     = rh_enrich_extract_best_upload_urls($html);
	$thumb_id = (int) get_post_thumbnail_id($post_id);
	$thumb_key = '';
	if ($thumb_id > 0) {
		$thumb_url = wp_get_attachment_url($thumb_id);
		if (is_string($thumb_url) && $thumb_url !== '') {
			$thumb_key = rh_enrich_canonical_upload_key($thumb_url);
		}
	}
	$new_ids = array();
	$title   = get_the_title($post_id);
	foreach ($urls as $img_url) {
		if (rh_enrich_is_brand_or_logo_url($img_url)) {
			continue;
		}
		if ($thumb_key !== '' && rh_enrich_canonical_upload_key($img_url) === $thumb_key) {
			continue;
		}
		$aid = rh_project_sideload_image_unique_file($img_url, (int) $post_id, $title);
		if (is_wp_error($aid)) {
			continue;
		}
		$aid = (int) $aid;
		if ($aid > 0) {
			$new_ids[] = $aid;
		}
	}
	$new_ids = array_values(array_unique($new_ids));
	$new_ids = array_values(
		array_filter(
			$new_ids,
			static function ($id) use ($thumb_id) {
				return $id > 0 && $id !== $thumb_id;
			}
		)
	);
	if ($new_ids === array()) {
		if (defined('WP_CLI') && WP_CLI) {
			WP_CLI::log('No new images for post ' . $post_id);
		}
		++$skipped;
		continue;
	}
	update_post_meta($post_id, 'rh_project_gallery', $new_ids);
	++$done;
	if (defined('WP_CLI') && WP_CLI) {
		WP_CLI::log('Updated post ' . $post_id . ' — ' . count($new_ids) . ' gallery image(s)');
	}
}

if (defined('WP_CLI') && WP_CLI) {
	WP_CLI::success('Gallery enrichment finished. Updated ' . $done . ', skipped ' . $skipped . '.');
}
