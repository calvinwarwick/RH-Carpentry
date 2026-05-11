<?php
/**
 * Projects custom post type and sectors taxonomy.
 *
 * @package RH_Base_Child
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
	exit;
}

/**
 * Register project post type and sector taxonomy.
 */
function rh_project_register(): void {
	register_post_type(
		'rh_project',
		array(
			'labels'              => array(
				'name'          => __('Projects', 'rh-base-child'),
				'singular_name' => __('Project', 'rh-base-child'),
				'add_new_item'  => __('Add new project', 'rh-base-child'),
				'edit_item'     => __('Edit project', 'rh-base-child'),
				'view_item'     => __('View project', 'rh-base-child'),
				'all_items'     => __('All projects', 'rh-base-child'),
			),
			'public'              => true,
			'has_archive'         => 'projects',
			'menu_icon'           => 'dashicons-portfolio',
			'supports'            => array('title', 'editor', 'excerpt', 'thumbnail', 'revisions'),
			'show_in_rest'        => true,
			'rewrite'             => array(
				'slug'       => 'project',
				'with_front' => false,
			),
		)
	);

	register_taxonomy(
		'rh_project_sector',
		'rh_project',
		array(
			'labels'       => array(
				'name'          => __('Sectors', 'rh-base-child'),
				'singular_name' => __('Sector', 'rh-base-child'),
			),
			'public'       => true,
			'hierarchical' => false,
			'show_in_rest' => true,
			'rewrite'      => array(
				'slug'         => 'project-sector',
				'with_front'   => false,
			),
		)
	);
}
add_action('init', 'rh_project_register', 5);

/**
 * Seed default sector terms once (matches common R H Carpentry categories).
 */
function rh_project_seed_sectors(): void {
	if (get_option('rh_base_child_sectors_seeded')) {
		return;
	}
	$sectors = array(
		'New builds',
		'Refurbishment',
		'Barn conversions',
		'Education',
		'Hospitality',
	);
	foreach ($sectors as $name) {
		if (! term_exists($name, 'rh_project_sector')) {
			$result = wp_insert_term($name, 'rh_project_sector');
			if (is_wp_error($result)) {
				break;
			}
		}
	}
	update_option('rh_base_child_sectors_seeded', '1');
}
add_action('init', 'rh_project_seed_sectors', 20);

/**
 * Flush permalinks once after CPT/tax changes (covers deploy without re-saving theme).
 */
function rh_base_child_maybe_flush_rewrites(): void {
	$version = '2';
	if (get_option('rh_base_child_rewrite_version') === $version) {
		return;
	}
	flush_rewrite_rules(false);
	update_option('rh_base_child_rewrite_version', $version);
}
add_action('init', 'rh_base_child_maybe_flush_rewrites', 999);

/**
 * MD5 of the attachment file on disk (empty string if unavailable).
 *
 * @param int $att_id Attachment ID.
 */
function rh_project_attachment_file_md5(int $att_id): string {
	if ($att_id <= 0) {
		return '';
	}
	$path = get_attached_file($att_id);
	if (! is_string($path) || $path === '' || ! is_readable($path)) {
		return '';
	}
	// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions -- binary comparison for dedupe only
	$h = md5_file($path);
	return is_string($h) ? $h : '';
}

/**
 * Whether an attachment looks like site chrome (logo, icons) rather than project photography.
 *
 * @param int $att_id Attachment ID.
 */
function rh_project_attachment_is_brand_asset(int $att_id): bool {
	if ($att_id <= 0) {
		return false;
	}
	$file = get_attached_file($att_id);
	if (! is_string($file) || $file === '') {
		return false;
	}
	$base = strtolower(wp_basename($file));
	if (preg_match('/(^|-)(logo|icon|favicon|site-icon|apple-touch)(-|\.|$)/', $base)) {
		return true;
	}
	if (strpos($base, 'cropped-') === 0) {
		return true;
	}
	if (strpos($base, 'logo') !== false && strpos($base, 'carpenter') !== false) {
		return true;
	}
	if (strpos($base, 'reversed_final') !== false) {
		return true;
	}
	$url = wp_get_attachment_url($att_id);
	if (is_string($url) && $url !== '') {
		$path = strtolower((string) wp_parse_url($url, PHP_URL_PATH));
		if (strpos($path, 'logo') !== false && strpos($path, 'carpenter') !== false) {
			return true;
		}
	}
	return false;
}

/**
 * Drop duplicate attachments that share the same file contents (e.g. cover sideloaded twice with different IDs).
 *
 * @param int[] $ids Ordered attachment IDs; first occurrence of each file is kept.
 * @return int[]
 */
function rh_project_dedupe_attachment_ids_by_file_hash(array $ids): array {
	$out  = array();
	$seen = array();
	foreach ($ids as $id) {
		$id = (int) $id;
		if ($id <= 0 || ! wp_attachment_is_image($id)) {
			continue;
		}
		$path = get_attached_file($id);
		if (is_string($path) && $path !== '' && is_readable($path)) {
			// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions -- binary comparison for dedupe only
			$hash = md5_file($path);
			if (is_string($hash) && $hash !== '') {
				if (isset($seen[ $hash ])) {
					continue;
				}
				$seen[ $hash ] = true;
			}
		}
		$out[] = $id;
	}
	return $out;
}

/**
 * Attachment IDs for the project slideshow: gallery only (deduped), so the cover image is not repeated
 * as the first slide. Falls back to the featured image when the gallery is empty or only duplicates of it.
 *
 * @param int $post_id Project post ID.
 * @return int[]
 */
function rh_project_get_slideshow_attachment_ids(int $post_id): array {
	if ($post_id <= 0 || get_post_type($post_id) !== 'rh_project') {
		return array();
	}
	$ids        = array();
	$thumb      = (int) get_post_thumbnail_id($post_id);
	$thumb_hash = $thumb > 0 ? rh_project_attachment_file_md5($thumb) : '';

	$gallery = get_post_meta($post_id, 'rh_project_gallery', true);
	if (is_array($gallery)) {
		foreach ($gallery as $aid) {
			$aid = (int) $aid;
			if ($aid <= 0 || ! wp_attachment_is_image($aid)) {
				continue;
			}
			if (rh_project_attachment_is_brand_asset($aid)) {
				continue;
			}
			if ($aid === $thumb) {
				continue;
			}
			if ($thumb_hash !== '' && rh_project_attachment_file_md5($aid) === $thumb_hash) {
				continue;
			}
			if (! in_array($aid, $ids, true)) {
				$ids[] = $aid;
			}
		}
	}

	$ids = rh_project_dedupe_attachment_ids_by_file_hash($ids);
	if ($ids !== array()) {
		return $ids;
	}

	if ($thumb > 0 && ! rh_project_attachment_is_brand_asset($thumb)) {
		return array( $thumb );
	}

	return array();
}

/**
 * Clean stored gallery meta: drop branding files and anything identical to the featured image on disk.
 *
 * @param int $post_id Project post ID.
 * @return int[] New gallery attachment IDs (not including featured).
 */
function rh_project_prune_stored_gallery_attachment_ids(int $post_id): array {
	$gallery = get_post_meta($post_id, 'rh_project_gallery', true);
	if (! is_array($gallery)) {
		return array();
	}
	$thumb      = (int) get_post_thumbnail_id($post_id);
	$thumb_hash = $thumb > 0 ? rh_project_attachment_file_md5($thumb) : '';
	$out        = array();
	foreach ($gallery as $aid) {
		$aid = (int) $aid;
		if ($aid <= 0 || ! wp_attachment_is_image($aid)) {
			continue;
		}
		if (rh_project_attachment_is_brand_asset($aid)) {
			continue;
		}
		if ($aid === $thumb) {
			continue;
		}
		if ($thumb_hash !== '' && rh_project_attachment_file_md5($aid) === $thumb_hash) {
			continue;
		}
		$out[] = $aid;
	}
	return rh_project_dedupe_attachment_ids_by_file_hash($out);
}

/**
 * Strip legacy import boilerplate (sector blurb + legacy site link) from HTML.
 */
function rh_project_strip_import_boilerplate_html(string $html): string {
	$out = preg_replace(
		'#<p>[^<]*project delivered by R H Carpenters\.?</p>#iu',
		'',
		$html
	);
	$out = preg_replace(
		'#<p>\s*<a[^>]+href=["\'][^"\']*rhcarpentersukltd\.co\.uk[^"\']*["\'][^>]*>.*?</a>\s*</p>#is',
		'',
		(string) $out
	);
	$out = preg_replace(
		'#<p>\s*<a[^>]*>[^<]*Original project page[^<]*</a>\s*</p>#iu',
		'',
		(string) $out
	);
	return trim((string) $out);
}

/**
 * Remove import boilerplate from rendered single-project body (existing posts).
 *
 * @param string $content Post content HTML.
 * @return string
 */
function rh_project_filter_single_the_content(string $content): string {
	if (! is_singular('rh_project') || ! in_the_loop() || ! is_main_query()) {
		return $content;
	}
	return rh_project_strip_import_boilerplate_html($content);
}
add_filter('the_content', 'rh_project_filter_single_the_content', 12);

/**
 * Hide legacy import excerpt lines ("… — portfolio project.") on cards and feeds.
 *
 * @param string       $excerpt Post excerpt.
 * @param WP_Post|null $post    Post object (WP 5.3+).
 * @return string
 */
function rh_project_filter_get_the_excerpt(string $excerpt, $post = null): string {
	if (! $post instanceof WP_Post || $post->post_type !== 'rh_project' || $excerpt === '') {
		return $excerpt;
	}
	if (preg_match('/portfolio project/i', $excerpt)) {
		return '';
	}
	return $excerpt;
}
add_filter('get_the_excerpt', 'rh_project_filter_get_the_excerpt', 10, 2);

/**
 * Related projects: same sector first, then recent others, excluding current.
 *
 * @param int $post_id Current project ID.
 * @param int $limit   Max posts (default 3).
 * @return WP_Post[]
 */
function rh_project_get_related_posts(int $post_id, int $limit = 3): array {
	if ($post_id <= 0 || ! post_type_exists('rh_project') || $limit < 1) {
		return array();
	}
	$exclude = array($post_id);
	$posts   = array();

	$term_ids = wp_get_post_terms($post_id, 'rh_project_sector', array('fields' => 'ids'));
	if (! is_wp_error($term_ids) && $term_ids !== array()) {
		$q = new WP_Query(
			array(
				'post_type'           => 'rh_project',
				'posts_per_page'      => $limit,
				'post__not_in'        => $exclude,
				'post_status'         => 'publish',
				'orderby'             => 'date',
				'order'               => 'DESC',
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
				'tax_query'           => array(
					array(
						'taxonomy' => 'rh_project_sector',
						'field'    => 'term_id',
						'terms'    => $term_ids,
					),
				),
			)
		);
		if ($q->have_posts()) {
			$posts = $q->posts;
		}
		wp_reset_postdata();
	}

	$have_ids = array_merge($exclude, wp_list_pluck($posts, 'ID'));
	if (count($posts) < $limit) {
		$need = $limit - count($posts);
		$q2   = new WP_Query(
			array(
				'post_type'           => 'rh_project',
				'posts_per_page'      => $need,
				'post__not_in'        => $have_ids,
				'post_status'         => 'publish',
				'orderby'             => 'date',
				'order'               => 'DESC',
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
			)
		);
		if ($q2->have_posts()) {
			$posts = array_merge($posts, $q2->posts);
		}
		wp_reset_postdata();
	}

	return array_slice($posts, 0, $limit);
}

/**
 * All other published projects (excluding current), newest first.
 *
 * @param int $post_id Current project ID.
 * @return WP_Post[]
 */
function rh_project_get_other_projects(int $post_id): array {
	if ($post_id <= 0 || ! post_type_exists('rh_project')) {
		return array();
	}
	$q = new WP_Query(
		array(
			'post_type'           => 'rh_project',
			'post_status'         => 'publish',
			'posts_per_page'      => -1,
			'post__not_in'        => array($post_id),
			'orderby'             => 'date',
			'order'               => 'DESC',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		)
	);
	$posts = $q->have_posts() ? $q->posts : array();
	wp_reset_postdata();
	return $posts;
}
