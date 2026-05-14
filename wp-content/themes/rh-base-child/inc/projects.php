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

/**
 * Cards per "page" on the projects archive (initial render + each infinite-scroll page).
 */
function rh_project_archive_per_page(): int {
	/**
	 * Filter the number of project cards loaded per page on the archive.
	 *
	 * @param int $per_page Default 10.
	 */
	return (int) apply_filters('rh_project_archive_per_page', 10);
}

/**
 * Force a deterministic posts_per_page on the projects archive so the infinite-scroll
 * REST endpoint and the initial server render stay in sync.
 *
 * @param WP_Query $query Main query.
 */
function rh_project_archive_query(WP_Query $query): void {
	if (is_admin() || ! $query->is_main_query()) {
		return;
	}
	if (! $query->is_post_type_archive('rh_project')) {
		return;
	}
	$query->set('posts_per_page', rh_project_archive_per_page());
	$query->set('orderby', 'date');
	$query->set('order', 'DESC');
}
add_action('pre_get_posts', 'rh_project_archive_query');

/**
 * Render a single project bento card (matches the "More projects" slider markup).
 *
 * @param WP_Post|int $post  Post or ID.
 * @param int         $index 0-based index used for the card's id attribute.
 */
function rh_project_render_bento_card($post, int $index = 0): string {
	$post = get_post($post);
	if (! $post instanceof WP_Post || $post->post_type !== 'rh_project' || $post->post_status !== 'publish') {
		return '';
	}

	$pid       = (int) $post->ID;
	$permalink = (string) get_permalink($post);
	$thumb_id  = (int) get_post_thumbnail_id($pid);
	$bg_url    = $thumb_id > 0 ? wp_get_attachment_image_url($thumb_id, 'large') : '';
	$terms     = get_the_terms($pid, 'rh_project_sector');
	$badges    = array();
	$slugs     = array();
	if ($terms && ! is_wp_error($terms)) {
		foreach (array_slice($terms, 0, 4) as $t) {
			$badges[] = (string) $t->name;
			$slugs[]  = (string) $t->slug;
		}
	}
	$sectors_attr = $slugs !== array() ? implode( ' ', $slugs ) : '';
	$title        = get_the_title($post);
	$card_id_att  = 'rh-archive-project-' . $pid;

	ob_start();
	?>
	<article
		class="rh-home-project-card rh-bento-cell is-active rh-archive-project-card-v2"
		id="<?php echo esc_attr($card_id_att); ?>"
		role="listitem"
		data-rh-project-url="<?php echo esc_url($permalink); ?>"
		<?php if ($sectors_attr !== '') : ?>
			data-rh-sectors="<?php echo esc_attr($sectors_attr); ?>"
		<?php endif; ?>
		data-rh-fx="scale"
		aria-label="<?php echo esc_attr($title); ?>"
	>
		<a class="rh-archive-project-cover-link" href="<?php echo esc_url($permalink); ?>" aria-label="<?php echo esc_attr(sprintf(
			/* translators: %s: project title */
			__('View project: %s', 'rh-base-child'),
			$title
		)); ?>"></a>
		<span class="rh-home-project-card__cta" aria-hidden="true">
			<?php esc_html_e('Find out more', 'rh-base-child'); ?>
			<i class="fa-solid fa-chevron-right rh-home-project-card__cta-icon" aria-hidden="true"></i>
		</span>
		<?php if ($bg_url) : ?>
			<span class="rh-home-project-card__bg" style="background-image: url('<?php echo esc_url($bg_url); ?>');"></span>
		<?php else : ?>
			<span class="rh-home-project-card__bg rh-home-project-card__bg--placeholder" aria-hidden="true"></span>
		<?php endif; ?>
		<span class="rh-home-project-card__overlay" aria-hidden="true"></span>
		<div class="rh-home-project-card__text">
			<span class="rh-home-project-card__title"><?php echo esc_html($title); ?></span>
			<?php if ($badges !== array()) : ?>
				<ul class="rh-home-project-card__badges">
					<?php foreach ($badges as $badge_label) : ?>
						<li><span class="rh-home-project-card__badge"><?php echo esc_html($badge_label); ?></span></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
	</article>
	<?php
	return (string) ob_get_clean();
}

/**
 * REST endpoint returning the next page of project cards (used by infinite scroll on the archive).
 */
function rh_project_register_rest_routes(): void {
	register_rest_route(
		'rh/v1',
		'/projects',
		array(
			'methods'             => 'GET',
			'permission_callback' => '__return_true',
			'args'                => array(
				'page'     => array(
					'type'              => 'integer',
					'default'           => 1,
					'minimum'           => 1,
					'sanitize_callback' => 'absint',
				),
				'per_page' => array(
					'type'              => 'integer',
					'default'           => 0,
					'minimum'           => 0,
					'maximum'           => 50,
					'sanitize_callback' => 'absint',
				),
			),
			'callback'            => 'rh_project_rest_archive_page',
		)
	);
}
add_action('rest_api_init', 'rh_project_register_rest_routes');

/**
 * REST callback: returns a page of bento card HTML.
 *
 * @param WP_REST_Request $request
 * @return WP_REST_Response
 */
function rh_project_rest_archive_page(WP_REST_Request $request): WP_REST_Response {
	$page     = max(1, (int) $request->get_param('page'));
	$per_page = (int) $request->get_param('per_page');
	if ($per_page <= 0) {
		$per_page = rh_project_archive_per_page();
	}

	$q = new WP_Query(
		array(
			'post_type'           => 'rh_project',
			'post_status'         => 'publish',
			'posts_per_page'      => $per_page,
			'paged'               => $page,
			'orderby'             => 'date',
			'order'               => 'DESC',
			'ignore_sticky_posts' => true,
		)
	);

	$html  = '';
	$count = 0;
	$base  = ($page - 1) * $per_page;
	if ($q->have_posts()) {
		while ($q->have_posts()) {
			$q->the_post();
			$html .= rh_project_render_bento_card(get_post(), $base + $count);
			$count++;
		}
		wp_reset_postdata();
	}

	return new WP_REST_Response(
		array(
			'page'        => $page,
			'per_page'    => $per_page,
			'count'       => $count,
			'total'       => (int) $q->found_posts,
			'total_pages' => (int) $q->max_num_pages,
			'has_more'    => $page < (int) $q->max_num_pages,
			'html'        => $html,
		)
	);
}

/**
 * Legacy portfolio rows (source work URL + cover image URL) for import/repair scripts.
 *
 * @return array<int, array{title: string, sector: string, url: string, img: string}>
 */
function rh_project_legacy_import_rows(): array {
	static $cache = null;
	if ($cache !== null) {
		return $cache;
	}
	$file = __DIR__ . '/project-legacy-import-rows.php';
	if (! is_readable($file)) {
		$cache = array();
		return $cache;
	}
	$data = require $file;
	$cache = is_array($data) ? $data : array();
	return $cache;
}

/**
 * Sideload a remote image with a filename scoped to the post so WordPress does not
 * return an existing attachment reused from another project for the same source URL.
 *
 * @param string $url     Remote image URL.
 * @param int    $post_id Parent post ID.
 * @param string $desc    Attachment title / description.
 * @return int|WP_Error Attachment ID on success.
 */
function rh_project_sideload_image_unique_file(string $url, int $post_id, string $desc): int|WP_Error {
	$url = trim($url);
	if ($url === '' || $post_id <= 0) {
		return new WP_Error('rh_project_sideload_bad_args', __('Invalid URL or post ID.', 'rh-base-child'));
	}

	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$tmp = download_url($url);
	if (is_wp_error($tmp)) {
		return $tmp;
	}

	$path_for_name = (string) wp_parse_url($url, PHP_URL_PATH);
	$basename      = $path_for_name !== '' ? basename($path_for_name) : 'image.jpg';
	$basename      = sanitize_file_name($basename);
	if ($basename === '') {
		$basename = 'image.jpg';
	}
	$info = pathinfo($basename);
	$stem = isset($info['filename']) ? (string) $info['filename'] : 'image';
	$ext  = '';
	if (isset($info['extension']) && $info['extension'] !== '') {
		$ext = '.' . strtolower((string) $info['extension']);
	}

	$file_array = array(
		'name'     => 'rh-project-' . $post_id . '-' . $stem . $ext,
		'tmp_name' => $tmp,
	);

	$att_id = media_handle_sideload($file_array, $post_id, $desc);
	if (is_wp_error($att_id)) {
		if (is_string($tmp) && $tmp !== '' && file_exists($tmp)) {
			wp_delete_file($tmp);
		}
		return $att_id;
	}

	return (int) $att_id;
}
