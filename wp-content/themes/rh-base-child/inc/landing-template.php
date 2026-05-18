<?php
/**
 * Helpers for SEO landing page template.
 *
 * @package RH_Base_Child
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
	exit;
}

/**
 * Kicker, title and intro for hub pages that use the projects-style archive header.
 *
 * @return array{kicker: string, title: string, subtitle: string, intro: string}|null
 */
function rh_landing_page_hero(string $slug): ?array {
	$heroes = array(
		'about'    => array(
			'pre_title' => __('Who we are', 'rh-base-child'),
			'title'     => __('About us', 'rh-base-child'),
			'subtitle'  => __('Over 40 years delivering carpentry and complete build packages across Essex and East Anglia.', 'rh-base-child'),
			'intro'     => __('Our team covers timber framing, roofing, refurbishments, barn conversions, bespoke joinery, commercial fit-out and certified fire door installation. Projects range from single extensions to multi-dwelling schemes and education buildings.', 'rh-base-child'),
			'split'     => true,
			'show_cta'  => true,
		),
		'services' => array(
			'pre_title' => __('What we offer', 'rh-base-child'),
			'title'     => __('Services', 'rh-base-child'),
			'subtitle'  => __('Full carpentry and complete build packages for residential and commercial clients.', 'rh-base-child'),
			'intro'     => __('Select a service below to learn more, or view our project portfolio for examples of recent work.', 'rh-base-child'),
			'split'     => false,
		),
		'contact'  => array(
			'kicker'   => __('Get in touch', 'rh-base-child'),
			'title'    => __('Contact us', 'rh-base-child'),
			'subtitle' => __('Tell us about your project — we aim to respond within one working day.', 'rh-base-child'),
			'intro'    => '',
		),
	);

	if (! isset($heroes[ $slug ])) {
		return null;
	}

	$hero = $heroes[ $slug ];
	if (! empty($hero['split']) && empty($hero['image_url'])) {
		$hero['image_url'] = rh_landing_hero_image_url();
	}

	return $hero;
}

/**
 * Ensure hub pages use the SEO landing template (archive hero + projects margins).
 */
function rh_carpentry_landing_template_path(): string {
	return get_stylesheet_directory() . '/page-templates/template-landing.php';
}

/**
 * @param string $template Current template path.
 */
function rh_carpentry_force_landing_template(string $template): string {
	if (! is_page()) {
		return $template;
	}
	$post = get_queried_object();
	if (! $post instanceof WP_Post) {
		return $template;
	}
	$use_landing = in_array( $post->post_name, array( 'about', 'services', 'areas' ), true );
	if (! $use_landing && rh_landing_page_hero( $post->post_name ) !== null) {
		$use_landing = true;
	}
	if (! $use_landing && rh_carpentry_service_by_slug( $post->post_name ) !== null) {
		$use_landing = true;
	}
	if (! $use_landing) {
		return $template;
	}
	$landing = rh_carpentry_landing_template_path();
	return is_readable( $landing ) ? $landing : $template;
}
add_filter( 'template_include', 'rh_carpentry_force_landing_template', 20 );

/**
 * Use clean service names in the browser tab (no " | RH Carpentry Essex").
 *
 * @param string[] $parts Document title parts.
 * @return string[]
 */
function rh_landing_document_title_parts(array $parts): array {
	if (! is_page() || is_admin()) {
		return $parts;
	}
	$post = get_queried_object();
	if (! $post instanceof WP_Post) {
		return $parts;
	}
	$service = rh_carpentry_service_by_slug( $post->post_name );
	if ($service !== null) {
		$parts['title'] = $service['label'];
	}
	return $parts;
}
add_filter( 'document_title_parts', 'rh_landing_document_title_parts', 20 );

/**
 * Hero image for inner-page split headers (same as homepage hero background).
 */
function rh_landing_hero_image_url(): string {
	return function_exists('rh_carpentry_get_hero_background_url') ? rh_carpentry_get_hero_background_url() : '';
}

/**
 * Split hero for a single service page.
 *
 * @param array{label: string, slug: string, card_slug: string, sectors: string[], intro: string, body: string} $service Service.
 * @return array{pre_title: string, title: string, subtitle: string, intro: string, image_url: string, split: bool}
 */
function rh_landing_service_hero(array $service): array {
	return array(
		'pre_title' => __('Our services', 'rh-base-child'),
		'title'     => $service['label'],
		'subtitle'  => '',
		'intro'     => $service['intro'],
		'split'     => false,
	);
}

function rh_landing_render_page_archive_header(array $hero): void {
	$title = isset($hero['title']) ? (string) $hero['title'] : '';
	if ($title === '') {
		return;
	}

	$template = ! empty($hero['split'])
		? get_stylesheet_directory() . '/template-parts/seo/page-hero-split.php'
		: get_stylesheet_directory() . '/template-parts/seo/page-archive-header.php';

	if (! is_readable($template)) {
		return;
	}

	// Set variables in this scope — load_template() uses EXTR_SKIP, so WordPress's
	// global $title would otherwise prevent the hero title from being passed through.
	$pre_title = (string) ($hero['pre_title'] ?? $hero['kicker'] ?? '');
	$kicker    = (string) ($hero['kicker'] ?? $hero['pre_title'] ?? '');
	$subtitle  = (string) ($hero['subtitle'] ?? '');
	$intro     = (string) ($hero['intro'] ?? '');
	$image_url = (string) ($hero['image_url'] ?? '');
	$show_cta  = ! empty($hero['show_cta']);

	include $template;
}

/**
 * Primary landing-page CTAs (contact + projects).
 */
function rh_landing_render_page_cta(): void {
	?>
	<div class="rh-hero-actions rh-page-hero-split__actions">
		<a class="rh-hero-btn rh-hero-btn--accent" href="<?php echo esc_url(rh_carpentry_contact_url()); ?>">
			<?php esc_html_e('Get in touch', 'rh-base-child'); ?>
		</a>
		<a class="rh-hero-btn rh-hero-btn--muted" href="<?php echo esc_url(rh_carpentry_projects_archive_url()); ?>">
			<?php esc_html_e('View projects', 'rh-base-child'); ?>
		</a>
	</div>
	<?php
}

/**
 * Remove the first paragraph from page HTML (used when intro is shown in the archive header).
 */
function rh_landing_content_without_lede(string $html): string {
	if (trim($html) === '') {
		return '';
	}
	$stripped = preg_replace('/\s*<p[^>]*>.*?<\/p>\s*/is', '', $html, 1);
	return is_string($stripped) ? $stripped : $html;
}

/**
 * Services bento grid on /services/ hub (matches homepage “What we offer” section).
 */
function rh_landing_render_services_grid(): void {
	?>
	<section
		class="rh-home-section rh-home-section--features rh-landing-services-grid"
		aria-label="<?php esc_attr_e('Our services', 'rh-base-child'); ?>"
	>
			<?php
			rh_include_template_part(
				'template-parts/home/services-section-inner.php',
				array(
					'heading_id'        => 'rh-landing-services-bento-heading',
					'show_header'       => false,
					'show_landing_link' => false,
					'hide_cta_buttons'  => true,
				)
			);
			?>
	</section>
	<?php
}

/**
 * Areas grid on /areas/ hub.
 */
function rh_landing_render_areas_grid(): void {
	echo '<ul class="rh-landing-grid" role="list">';
	foreach (rh_carpentry_area_pages() as $area) {
		printf(
			'<li class="rh-landing-grid__item"><a href="%s">%s</a></li>',
			esc_url(rh_carpentry_area_url($area['slug'])),
			esc_html($area['title'])
		);
	}
	echo '</ul>';
}

/**
 * Query projects for the landing-page carousel.
 *
 * @param string[] $sector_slugs      Optional sector term slugs; empty = all projects.
 * @param int      $per_page          Max posts.
 * @param int      $exclude_post_id   Optional project ID to omit (e.g. current single).
 * @return WP_Post[]
 */
function rh_landing_query_projects_for_slider(array $sector_slugs, int $per_page = 12, int $exclude_post_id = 0): array {
	if (! post_type_exists('rh_project')) {
		return array();
	}

	$per_page = max(1, min(24, $per_page));
	$args     = array(
		'post_type'           => 'rh_project',
		'posts_per_page'      => $per_page,
		'post_status'         => 'publish',
		'orderby'             => 'date',
		'order'               => 'DESC',
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	);

	if ($exclude_post_id > 0) {
		$args['post__not_in'] = array($exclude_post_id);
	}

	$sector_slugs = array_values(
		array_filter(
			array_map('sanitize_title', $sector_slugs),
			static function (string $slug): bool {
				return $slug !== '';
			}
		)
	);

	if ($sector_slugs !== array()) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'rh_project_sector',
				'field'    => 'slug',
				'terms'    => $sector_slugs,
			),
		);
	}

	$q = new WP_Query($args);
	$posts = $q->have_posts() ? $q->posts : array();
	wp_reset_postdata();

	if ($posts === array() && $sector_slugs !== array()) {
		return rh_landing_query_projects_for_slider(array(), $per_page, $exclude_post_id);
	}

	return $posts;
}

/**
 * Testimonials band (homepage markup).
 *
 * @param string $heading_id Unique heading id.
 */
function rh_landing_render_testimonials_section(string $heading_id = 'rh-home-testimonials-heading'): void {
	rh_include_template_part(
		'template-parts/home/testimonials-section.php',
		array(
			'cta_contact' => rh_carpentry_contact_url(),
			'heading_id'  => $heading_id,
		)
	);
}

/**
 * Client logo marquee (homepage markup).
 *
 * @param string $heading_id Unique heading id.
 */
function rh_landing_render_clients_section(string $heading_id = 'rh-home-clients-heading'): void {
	rh_include_template_part(
		'template-parts/home/clients-section.php',
		array(
			'cta_contact' => rh_carpentry_contact_url(),
			'heading_id'  => $heading_id,
		)
	);
}

/**
 * Inline contact form band (same fields/styling as the contact overlay).
 */
function rh_landing_render_contact_band(): void {
	rh_include_template_part('template-parts/seo/contact-section-band');
}

/**
 * Projects carousel band (homepage-style slider).
 *
 * @param string[] $sector_slugs      Optional sector term slugs; empty = all projects.
 * @param string   $heading_id        Unique heading id for this band.
 * @param int      $exclude_post_id   Optional project ID to omit.
 * @param string   $title             Section heading override.
 */
function rh_landing_render_projects_slider(
	array $sector_slugs,
	string $heading_id = 'rh-landing-projects-heading',
	int $exclude_post_id = 0,
	string $title = ''
): void {
	$projects = rh_landing_query_projects_for_slider($sector_slugs, 12, $exclude_post_id);
	if ($projects === array()) {
		return;
	}

	if ($title === '') {
		$title = __('Projects', 'rh-base-child');
	}

	rh_include_template_part(
		'template-parts/projects/projects-carousel-band.php',
		array(
			'projects'            => $projects,
			'kicker'              => __('Portfolio', 'rh-base-child'),
			'title'               => $title,
			'heading_id'          => $heading_id,
			'carousel_aria_label' => __('Featured projects', 'rh-base-child'),
		)
	);
}
