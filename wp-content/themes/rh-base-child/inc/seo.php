<?php
/**
 * SEO: meta fallbacks, JSON-LD, llms.txt, sitemap fallback.
 *
 * @package RH_Base_Child
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
	exit;
}

/**
 * Business NAP + contact defaults.
 *
 * @return array{name: string, legal: string, phone: string, email: string, address: string, lat: float, lng: float}
 */
function rh_seo_business_details(): array {
	return array(
		'name'    => (string) get_bloginfo('name'),
		'legal'   => 'R H Carpenters (UK) Ltd',
		'phone'   => (string) apply_filters('rh_seo_phone', '01206250577'),
		'email'   => (string) apply_filters('rh_seo_email', 'info@rhcarpentersukltd.co.uk'),
		'address' => (string) apply_filters(
			'rh_carpentry_footer_address',
			'Bouverie, St. Mary\'s Road, Aingers Green, Gt Bentley, Colchester, Essex, CO7 8NN'
		),
		'lat'     => 51.862,
		'lng'     => 1.062,
	);
}

/**
 * Social profile URLs for schema sameAs.
 *
 * @return string[]
 */
function rh_seo_same_as_urls(): array {
	$urls = array();
	$fb = rh_carpentry_facebook_url();
	$ig = rh_carpentry_instagram_url();
	$li = (string) get_theme_mod('rh_social_linkedin', '');
	if ($fb !== '') {
		$urls[] = $fb;
	}
	if ($ig !== '') {
		$urls[] = $ig;
	}
	if ($li !== '') {
		$urls[] = $li;
	}
	return array_values(array_unique(array_filter($urls)));
}

/**
 * Meta description for a single post/page/CPT.
 */
function rh_seo_singular_meta_description(int $post_id): string {
	if ($post_id <= 0) {
		return '';
	}

	$custom = (string) get_post_meta($post_id, 'rh_meta_description', true);
	if ($custom !== '') {
		return $custom;
	}

	if (function_exists('rank_math')) {
		$rm = (string) get_post_meta($post_id, 'rank_math_description', true);
		if ($rm !== '') {
			return $rm;
		}
	}

	if (defined('WPSEO_VERSION')) {
		$yoast = (string) get_post_meta($post_id, '_yoast_wpseo_metadesc', true);
		if ($yoast !== '') {
			return $yoast;
		}
	}

	if (has_excerpt($post_id)) {
		return wp_strip_all_tags(get_the_excerpt($post_id));
	}

	$post = get_post($post_id);
	if (! $post instanceof WP_Post) {
		return '';
	}

	if ($post->post_type === 'rh_project') {
		return sprintf(
			/* translators: %s: project title */
			__('%s — carpentry and construction project by RH Carpentry, Essex.', 'rh-base-child'),
			get_the_title($post_id)
		);
	}

	if ($post->post_type === 'page') {
		$slug = $post->post_name;

		if (function_exists('rh_carpentry_service_by_slug')) {
			$service = rh_carpentry_service_by_slug($slug);
			if (is_array($service) && ! empty($service['intro'])) {
				return (string) $service['intro'];
			}
		}

		if (function_exists('rh_landing_page_hero')) {
			$hero = rh_landing_page_hero($slug);
			if (is_array($hero)) {
				$intro = trim((string) ($hero['intro'] ?? ''));
				if ($intro !== '') {
					return $intro;
				}
				$subtitle = trim((string) ($hero['subtitle'] ?? ''));
				if ($subtitle !== '') {
					return $subtitle;
				}
			}
		}

		if (function_exists('rh_carpentry_area_pages')) {
			foreach (rh_carpentry_area_pages() as $area) {
				if (($area['slug'] ?? '') === $slug && ! empty($area['intro'])) {
					return (string) $area['intro'];
				}
			}
		}
	}

	$content = wp_strip_all_tags((string) $post->post_content);
	if ($content !== '') {
		return wp_trim_words($content, 24, '…');
	}

	return sprintf(
		/* translators: %s: page or post title */
		__('%s — RH Carpentry & Construction, Essex.', 'rh-base-child'),
		get_the_title($post_id)
	);
}

/**
 * Meta description for current request (Rank Math / Yoast override via filter if present).
 */
function rh_seo_meta_description(): string {
	$desc = '';

	if (is_front_page()) {
		$desc = __('RH Carpentry & Construction — carpentry and complete build packages in Essex. Timber frame, roofing, refurbishments, barn conversions and fire door installation.', 'rh-base-child');
	} elseif (is_singular()) {
		$desc = rh_seo_singular_meta_description(get_queried_object_id());
	} elseif (is_post_type_archive('rh_project')) {
		$desc = __('Portfolio of carpentry and construction projects across Essex — new builds, refurbishments, barn conversions and commercial work.', 'rh-base-child');
	} elseif (is_post_type_archive('rh_insight')) {
		$desc = __('Insights on carpentry, construction and fire safety from RH Carpentry — Essex builders with 40+ years on site.', 'rh-base-child');
	} elseif (is_tax('rh_project_sector')) {
		$term = get_queried_object();
		if ($term instanceof WP_Term) {
			$desc = rh_seo_sector_meta_description($term);
		}
	} elseif (is_search()) {
		$desc = __('Search RH Carpentry — projects, services and articles about carpentry and construction in Essex.', 'rh-base-child');
	}

	$desc = (string) apply_filters('rh_seo_meta_description', $desc);
	if (strlen($desc) > 160) {
		$desc = wp_trim_words($desc, 24, '…');
	}
	return $desc;
}

/**
 * Sector taxonomy meta description.
 */
function rh_seo_sector_meta_description(WP_Term $term): string {
	$intro = rh_seo_sector_intro($term);
	if ($intro !== '') {
		return wp_trim_words($intro, 24, '…');
	}
	return sprintf(
		/* translators: %s: sector name */
		__('%s projects by RH Carpentry — carpentry and construction in Essex.', 'rh-base-child'),
		$term->name
	);
}

/**
 * Intro copy for sector archives.
 */
function rh_seo_sector_intro(WP_Term $term): string {
	$map = array(
		'new-builds'       => __('New build carpentry and complete construction packages — timber frame, roofing and finishing joinery for dwellings and light commercial schemes.', 'rh-base-child'),
		'refurbishment'    => __('Refurbishment and renovation projects for homes, community buildings and commercial premises across Essex.', 'rh-base-child'),
		'barn-conversions' => __('Barn and agricultural building conversions combining structural repair with high-quality residential and hospitality interiors.', 'rh-base-child'),
		'education'        => __('Education sector carpentry — school buildings, halls and fit-out delivered to programme with strong site safety standards.', 'rh-base-child'),
		'hospitality'      => __('Hospitality and leisure fit-out — cafes, venues and customer-facing spaces with durable, attractive joinery.', 'rh-base-child'),
	);
	return $map[ $term->slug ] ?? '';
}

/**
 * Output meta description when no SEO plugin provides one.
 */
function rh_seo_output_meta_description(): void {
	$desc = rh_seo_meta_description();
	if ($desc === '') {
		return;
	}

	if (function_exists('rank_math') || defined('WPSEO_VERSION')) {
		return;
	}

	printf(
		'<meta name="description" content="%s" />' . "\n",
		esc_attr($desc)
	);
}
add_action('wp_head', 'rh_seo_output_meta_description', 2);

/**
 * Feed theme descriptions into Rank Math when the SEO box is empty.
 *
 * @param string $description Rank Math description.
 */
function rh_seo_rank_math_description(string $description): string {
	if ($description !== '') {
		return $description;
	}
	return rh_seo_meta_description();
}
add_filter('rank_math/frontend/description', 'rh_seo_rank_math_description');

/**
 * Feed theme descriptions into Yoast when empty.
 *
 * @param string $description Yoast description.
 */
function rh_seo_yoast_metadesc(string $description): string {
	if ($description !== '') {
		return $description;
	}
	return rh_seo_meta_description();
}
add_filter('wpseo_metadesc', 'rh_seo_yoast_metadesc');

/**
 * Register llms.txt and fallback XML sitemap rewrites.
 */
function rh_seo_register_rewrites(): void {
	add_rewrite_rule('^llms\.txt$', 'index.php?rh_llms_txt=1', 'top');
	add_rewrite_rule('^rh-sitemap\.xml$', 'index.php?rh_sitemap_xml=1', 'top');
}
add_action('init', 'rh_seo_register_rewrites');

/**
 * Query vars.
 *
 * @param string[] $vars Vars.
 * @return string[]
 */
function rh_seo_query_vars(array $vars): array {
	$vars[] = 'rh_llms_txt';
	$vars[] = 'rh_sitemap_xml';
	return $vars;
}
add_filter('query_vars', 'rh_seo_query_vars');

/**
 * Serve llms.txt or rh-sitemap.xml.
 */
function rh_seo_template_redirect(): void {
	if ((int) get_query_var('rh_llms_txt') === 1) {
		header('Content-Type: text/plain; charset=utf-8');
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- plain text protocol
		echo rh_seo_build_llms_txt();
		exit;
	}
	if ((int) get_query_var('rh_sitemap_xml') === 1) {
		header('Content-Type: application/xml; charset=utf-8');
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- XML
		echo rh_seo_build_fallback_sitemap_xml();
		exit;
	}
}
add_action('template_redirect', 'rh_seo_template_redirect');

/**
 * Build llms.txt content.
 */
function rh_seo_build_llms_txt(): string {
	$biz     = rh_seo_business_details();
	$home    = trailingslashit(home_url('/'));
	$lines   = array();
	$lines[] = '# RH Carpentry & Construction';
	$lines[] = '> Carpentry and complete build packages in Essex, UK.';
	$lines[] = '';
	$lines[] = '## Business';
	$lines[] = '- Legal name: ' . $biz['legal'];
	$lines[] = '- Website: ' . $home;
	$lines[] = '- Phone: ' . $biz['phone'];
	$lines[] = '- Email: ' . $biz['email'];
	$lines[] = '- Address: ' . $biz['address'];
	$lines[] = '';
	$lines[] = '## Services';
	foreach (rh_carpentry_services() as $service) {
		$lines[] = '- ' . $service['label'] . ': ' . rh_carpentry_service_url($service['slug']);
	}
	$lines[] = '';
	$lines[] = '## Portfolio';
	$lines[] = '- Projects: ' . rh_carpentry_projects_archive_url();
	$lines[] = '';
	$lines[] = '## Areas';
	$lines[] = '- Areas we cover: ' . rh_carpentry_page_url('areas', '');
	foreach (rh_carpentry_area_pages() as $area) {
		$lines[] = '- ' . wp_strip_all_tags($area['title']) . ': ' . rh_carpentry_area_url($area['slug']);
	}
	$lines[] = '';
	$lines[] = '## Facts for assistants';
	$lines[] = '- Business information: ' . rh_carpentry_page_url('business', '');
	$lines[] = '- About: ' . rh_carpentry_about_landing_url();
	$lines[] = '- Contact: ' . rh_carpentry_contact_url();
	$lines[] = '- FAQ: ' . rh_carpentry_page_url('faq', '');
	$lines[] = '';
	$lines[] = '## Sitemap';
	$lines[] = '- XML (WordPress): ' . home_url('/wp-sitemap.xml');
	$lines[] = '- XML (fallback): ' . home_url('/rh-sitemap.xml');
	return implode("\n", $lines) . "\n";
}

/**
 * Fallback sitemap when core wp-sitemap.xml errors on host.
 */
function rh_seo_build_fallback_sitemap_xml(): string {
	$urls = array( trailingslashit(home_url('/')) );

	$pages = get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'fields'         => 'ids',
		)
	);
	foreach ($pages as $pid) {
		$urls[] = (string) get_permalink((int) $pid);
	}

	$projects = get_posts(
		array(
			'post_type'      => 'rh_project',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'fields'         => 'ids',
		)
	);
	foreach ($projects as $pid) {
		$urls[] = (string) get_permalink((int) $pid);
	}

	$terms = get_terms(
		array(
			'taxonomy'   => 'rh_project_sector',
			'hide_empty' => true,
		)
	);
	if (is_array($terms)) {
		foreach ($terms as $term) {
			if ($term instanceof WP_Term) {
				$link = get_term_link($term);
				if (! is_wp_error($link)) {
					$urls[] = (string) $link;
				}
			}
		}
	}

	$urls[] = rh_carpentry_projects_archive_url();

	if (post_type_exists('rh_insight')) {
		$insights = get_posts(
			array(
				'post_type'      => 'rh_insight',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'fields'         => 'ids',
			)
		);
		foreach ($insights as $pid) {
			$urls[] = (string) get_permalink((int) $pid);
		}
		$archive = get_post_type_archive_link('rh_insight');
		if (is_string($archive) && $archive !== '') {
			$urls[] = $archive;
		}
	}

	$urls = array_values(array_unique(array_filter($urls)));

	$xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
	$xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
	foreach ($urls as $loc) {
		$xml .= '  <url><loc>' . esc_url($loc) . '</loc></url>' . "\n";
	}
	$xml .= '</urlset>';
	return $xml;
}

/**
 * Add llms.txt hint to robots.txt.
 */
function rh_seo_robots_txt(string $output, bool $public): string {
	if (! $public) {
		return $output;
	}
	$output .= "\n# AI discovery\n";
	$output .= 'Sitemap: ' . esc_url(home_url('/rh-sitemap.xml')) . "\n";
	return $output;
}
add_filter('robots_txt', 'rh_seo_robots_txt', 10, 2);

/**
 * Breadcrumb items for current page.
 *
 * @return array<int, array{label: string, url: string}>
 */
function rh_seo_breadcrumbs(): array {
	$crumbs = array(
		array(
			'label' => __('Home', 'rh-base-child'),
			'url'   => home_url('/'),
		),
	);

	if (is_front_page()) {
		return array();
	}

	if (is_post_type_archive('rh_project')) {
		$crumbs[] = array(
			'label' => __('Projects', 'rh-base-child'),
			'url'   => '',
		);
		return $crumbs;
	}

	if (is_post_type_archive('rh_insight')) {
		$crumbs[] = array(
			'label' => __('Insights', 'rh-base-child'),
			'url'   => '',
		);
		return $crumbs;
	}

	if (is_singular('rh_project')) {
		$crumbs[] = array(
			'label' => __('Projects', 'rh-base-child'),
			'url'   => rh_carpentry_projects_archive_url(),
		);
		$crumbs[] = array(
			'label' => get_the_title(),
			'url'   => '',
		);
		return $crumbs;
	}

	if (is_tax('rh_project_sector')) {
		$term = get_queried_object();
		$crumbs[] = array(
			'label' => __('Projects', 'rh-base-child'),
			'url'   => rh_carpentry_projects_archive_url(),
		);
		if ($term instanceof WP_Term) {
			$crumbs[] = array(
				'label' => $term->name,
				'url'   => '',
			);
		}
		return $crumbs;
	}

	if (is_singular('rh_insight')) {
		$archive = get_post_type_archive_link('rh_insight');
		if (is_string($archive) && $archive !== '') {
			$crumbs[] = array(
				'label' => __('Insights', 'rh-base-child'),
				'url'   => $archive,
			);
		}
		$crumbs[] = array(
			'label' => get_the_title(),
			'url'   => '',
		);
		return $crumbs;
	}

	if (is_page()) {
		$post = get_queried_object();
		if ($post instanceof WP_Post && $post->post_parent > 0) {
			$ancestors = array_reverse(get_post_ancestors($post));
			foreach ($ancestors as $aid) {
				$crumbs[] = array(
					'label' => get_the_title((int) $aid),
					'url'   => (string) get_permalink((int) $aid),
				);
			}
		}
		$crumbs[] = array(
			'label' => get_the_title(),
			'url'   => '',
		);
		return $crumbs;
	}

	if (is_404()) {
		$crumbs[] = array(
			'label' => __('Page not found', 'rh-base-child'),
			'url'   => '',
		);
		return $crumbs;
	}

	return $crumbs;
}

/**
 * Render breadcrumb nav.
 */
function rh_seo_render_breadcrumbs(): void {
	$crumbs = rh_seo_breadcrumbs();
	if ($crumbs === array()) {
		return;
	}
	echo '<nav class="rh-breadcrumbs" aria-label="' . esc_attr__('Breadcrumb', 'rh-base-child') . '" data-rh-fx-group data-rh-fx-stagger="90" data-rh-fx-sync="hero-subtitle">';
	echo '<ol class="rh-breadcrumbs__list">';
	$last = count($crumbs) - 1;
	foreach ($crumbs as $i => $crumb) {
		echo '<li class="rh-breadcrumbs__item" data-rh-fx="fade" data-rh-fx-tone="dark">';
		if ($i < $last && $crumb['url'] !== '') {
			printf(
				'<a href="%s">%s</a>',
				esc_url($crumb['url']),
				esc_html($crumb['label'])
			);
		} else {
			echo '<span aria-current="page">' . esc_html($crumb['label']) . '</span>';
		}
		echo '</li>';
	}
	echo '</ol></nav>';
}

/**
 * JSON-LD graph for current page.
 *
 * @return array<string, mixed>
 */
function rh_seo_json_ld_graph(): array {
	$biz  = rh_seo_business_details();
	$graph = array();

	$local = array(
		'@type'       => array('HomeAndConstructionBusiness', 'LocalBusiness'),
		'@id'         => trailingslashit(home_url('/')) . '#organization',
		'name'        => $biz['name'],
		'legalName'   => $biz['legal'],
		'url'         => home_url('/'),
		'telephone'   => $biz['phone'],
		'email'       => $biz['email'],
		'description' => rh_seo_meta_description(),
		'address'     => array(
			'@type'           => 'PostalAddress',
			'streetAddress'   => $biz['address'],
			'addressLocality' => 'Colchester',
			'addressRegion'   => 'Essex',
			'postalCode'      => 'CO7 8NN',
			'addressCountry'  => 'GB',
		),
		'geo'         => array(
			'@type'     => 'GeoCoordinates',
			'latitude'  => $biz['lat'],
			'longitude' => $biz['lng'],
		),
		'areaServed'  => array(
			array(
				'@type' => 'AdministrativeArea',
				'name'  => 'Essex',
			),
			array(
				'@type' => 'City',
				'name'  => 'Colchester',
			),
		),
	);
	$same = rh_seo_same_as_urls();
	if ($same !== array()) {
		$local['sameAs'] = $same;
	}
	$graph[] = $local;

	if (is_front_page()) {
		$graph[] = array(
			'@type'           => 'WebSite',
			'@id'             => trailingslashit(home_url('/')) . '#website',
			'url'             => home_url('/'),
			'name'            => $biz['name'],
			'publisher'       => array('@id' => trailingslashit(home_url('/')) . '#organization'),
			'potentialAction' => array(
				'@type'       => 'SearchAction',
				'target'      => home_url('/?s={search_term_string}'),
				'query-input' => 'required name=search_term_string',
			),
		);
	}

	$crumbs = rh_seo_breadcrumbs();
	if ($crumbs !== array()) {
		$items = array();
		foreach ($crumbs as $i => $crumb) {
			$items[] = array(
				'@type'    => 'ListItem',
				'position' => $i + 1,
				'name'     => $crumb['label'],
				'item'     => $crumb['url'] !== '' ? $crumb['url'] : null,
			);
		}
		$graph[] = array(
			'@type'           => 'BreadcrumbList',
			'itemListElement' => $items,
		);
	}

	if (is_singular('rh_project')) {
		$graph[] = array(
			'@type'            => 'CreativeWork',
			'name'             => get_the_title(),
			'url'              => get_permalink(),
			'description'      => rh_seo_meta_description(),
			'creator'          => array('@id' => trailingslashit(home_url('/')) . '#organization'),
			'datePublished'    => get_the_date('c'),
			'dateModified'     => get_the_modified_date('c'),
		);
	}

	$faq_page = rh_carpentry_get_page_by_path('faq');
	if ($faq_page instanceof WP_Post && is_page($faq_page->ID)) {
		$faq = rh_seo_faq_entities();
		if ($faq !== array()) {
			$graph[] = array(
				'@type'      => 'FAQPage',
				'mainEntity' => $faq,
			);
		}
	}

	if (is_page()) {
		$slug = get_post_field('post_name', get_queried_object_id());
		$service = is_string($slug) ? rh_carpentry_service_by_slug($slug) : null;
		if ($service !== null) {
			$graph[] = array(
				'@type'       => 'Service',
				'name'        => $service['label'],
				'url'         => rh_carpentry_service_url($service['slug']),
				'description' => $service['intro'],
				'provider'    => array('@id' => trailingslashit(home_url('/')) . '#organization'),
				'areaServed'  => 'Essex, United Kingdom',
			);
		}
	}

	return array(
		'@context' => 'https://schema.org',
		'@graph'   => $graph,
	);
}

/**
 * FAQ entities for schema.
 *
 * @return array<int, array<string, mixed>>
 */
function rh_seo_faq_entities(): array {
	$entities = array();
	$page = rh_carpentry_get_page_by_path('faq');
	if (! $page instanceof WP_Post) {
		return $entities;
	}
	$content = $page->post_content;
	if (! preg_match_all('/<h2[^>]*>(.*?)<\/h2>\s*<p[^>]*>(.*?)<\/p>/is', $content, $matches, PREG_SET_ORDER)) {
		return $entities;
	}
	foreach ($matches as $m) {
		$entities[] = array(
			'@type'          => 'Question',
			'name'           => wp_strip_all_tags($m[1]),
			'acceptedAnswer' => array(
				'@type' => 'Answer',
				'text'  => wp_strip_all_tags($m[2]),
			),
		);
	}
	return $entities;
}

/**
 * Print JSON-LD in head.
 */
function rh_seo_output_json_ld(): void {
	$data = rh_seo_json_ld_graph();
	if (empty($data['@graph'])) {
		return;
	}
	echo '<script type="application/ld+json">';
	echo wp_json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
	echo '</script>' . "\n";
}
add_action('wp_head', 'rh_seo_output_json_ld', 20);

/**
 * Flush rewrites when SEO module version changes.
 */
function rh_seo_maybe_flush_rewrites(): void {
	$version = '1';
	if (get_option('rh_seo_rewrite_version') === $version) {
		return;
	}
	flush_rewrite_rules(false);
	update_option('rh_seo_rewrite_version', $version);
}
add_action('init', 'rh_seo_maybe_flush_rewrites', 1000);
