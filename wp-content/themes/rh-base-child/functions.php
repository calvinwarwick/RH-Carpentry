<?php
/**
 * RH Base child theme.
 *
 * @package RH_Base_Child
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
	exit;
}

require_once get_stylesheet_directory() . '/inc/customizer.php';
require_once get_stylesheet_directory() . '/inc/hero-media.php';
require_once get_stylesheet_directory() . '/inc/hero-contact-icons.php';
require_once get_stylesheet_directory() . '/inc/static-front-page.php';
require_once get_stylesheet_directory() . '/inc/projects.php';
require_once get_stylesheet_directory() . '/inc/resend-mail.php';
require_once get_stylesheet_directory() . '/inc/services.php';
require_once get_stylesheet_directory() . '/inc/areas.php';
require_once get_stylesheet_directory() . '/inc/seo-pages.php';

/**
 * Include a theme template part with variables in scope.
 *
 * WordPress load_template() on this host does not extract the $args array passed to
 * get_template_part(), so templates that rely on passed variables must use this helper.
 *
 * @param string               $relative_path Path under the child theme, e.g. template-parts/foo.php.
 * @param array<string, mixed> $args          Variables for the template.
 */
function rh_include_template_part(string $relative_path, array $args = array()): void {
	$file = get_stylesheet_directory() . '/' . ltrim($relative_path, '/');
	if (! is_readable($file)) {
		return;
	}
	if ($args !== array()) {
		// phpcs:ignore WordPress.PHP.DontExtract.extract_extract
		extract($args, EXTR_SKIP);
	}
	include $file;
}

require_once get_stylesheet_directory() . '/inc/landing-template.php';
require_once get_stylesheet_directory() . '/inc/seo.php';
require_once get_stylesheet_directory() . '/inc/insights.php';
require_once get_stylesheet_directory() . '/inc/section-overlays.php';
require_once get_stylesheet_directory() . '/inc/contact.php';
require_once get_stylesheet_directory() . '/inc/dev-environment.php';

/**
 * URL to a homepage section (fragment id without leading #, e.g. about, services, contact).
 */
function rh_carpentry_home_section_url(string $fragment_id): string {
	$fragment_id = ltrim($fragment_id, '#');
	return trailingslashit(home_url('/')) . '#' . $fragment_id;
}

/**
 * Point primary menu items at homepage sections when they target old About/Services/Projects URLs.
 *
 * @param WP_Post[] $sorted_menu_items Menu items.
 * @param stdClass  $args              Menu arguments.
 * @return WP_Post[]
 */
function rh_carpentry_primary_menu_home_sections(array $sorted_menu_items, $args): array {
	if (! isset($args->theme_location) || $args->theme_location !== 'primary') {
		return $sorted_menu_items;
	}

	$projects = trailingslashit(rh_carpentry_projects_archive_url());

	foreach ($sorted_menu_items as $item) {
		if (! isset($item->url) || ! is_string($item->url)) {
			continue;
		}
		$url  = $item->url;
		$path = (string) wp_parse_url($url, PHP_URL_PATH);
		$path = '/' . trim($path, '/') . '/';

		if ($path === '/about/') {
			$item->url = rh_carpentry_about_page_url();
			continue;
		}
		if ($path === '/services/') {
			$item->url = rh_carpentry_services_hub_url();
			continue;
		}
		if ($path === '/contact/') {
			$item->url = rh_carpentry_contact_url();
			continue;
		}
		$proj_path = (string) wp_parse_url($projects, PHP_URL_PATH);
		$proj_path = '/' . trim($proj_path, '/') . '/';
		if ($path === $proj_path || $path === '/projects/') {
			$item->url = rh_carpentry_projects_archive_url();
			continue;
		}

		$frag = (string) wp_parse_url($url, PHP_URL_FRAGMENT);
		$legacy_sections = array(
			'rh-home-section-about'    => 'about',
			'rh-home-section-services' => 'services',
			'rh-home-about-heading'    => 'about',
			'rh-home-work-heading'     => 'services',
		);
		if (isset($legacy_sections[ $frag ])) {
			$section = $legacy_sections[ $frag ];
			if ($section === 'services') {
				$item->url = rh_carpentry_services_hub_url();
			} else {
				$item->url = rh_carpentry_about_page_url();
			}
			continue;
		}
		if ($frag === 'rh-home-section-projects' || $frag === 'rh-home-projects-heading' || $frag === 'projects') {
			$item->url = rh_carpentry_projects_archive_url();
			continue;
		}
	}

	return $sorted_menu_items;
}
add_filter('wp_nav_menu_objects', 'rh_carpentry_primary_menu_home_sections', 10, 2);

/**
 * Default menu when no Primary menu is assigned.
 */
function rh_carpentry_hero_fallback_menu(): void {
	$items = array(
		array(
			'label' => __('About', 'rh-base-child'),
			'url'   => rh_carpentry_about_page_url(),
		),
		array(
			'label' => __('Services', 'rh-base-child'),
			'url'   => rh_carpentry_services_hub_url(),
		),
		array(
			'label' => __('Projects', 'rh-base-child'),
			'url'   => rh_carpentry_projects_archive_url(),
		),
	);

	echo '<ul id="rh-hero-primary-menu" class="rh-hero-nav__menu">';
	foreach ($items as $item) {
		printf(
			'<li><a href="%s">%s</a></li>',
			esc_url($item['url']),
			esc_html($item['label'])
		);
	}
	echo '</ul>';
}

/**
 * Primary menu fallback for inner-page top bar (distinct list id from hero home).
 */
function rh_carpentry_site_top_fallback_menu(): void {
	$items = array(
		array(
			'label' => __('About', 'rh-base-child'),
			'url'   => rh_carpentry_about_page_url(),
		),
		array(
			'label' => __('Services', 'rh-base-child'),
			'url'   => rh_carpentry_services_hub_url(),
		),
		array(
			'label' => __('Projects', 'rh-base-child'),
			'url'   => rh_carpentry_projects_archive_url(),
		),
	);

	printf(
		'<ul id="rh-site-top-primary-menu" class="rh-hero-nav__menu"><li class="menu-item rh-hero-nav__home-item"><a class="rh-hero-nav__home" href="%1$s"><span class="screen-reader-text">%2$s</span><i class="fa-solid fa-house" aria-hidden="true"></i></a></li>',
		esc_url(home_url('/')),
		esc_html__('Home', 'rh-base-child')
	);
	foreach ($items as $item) {
		printf(
			'<li><a href="%s">%s</a></li>',
			esc_url($item['url']),
			esc_html($item['label'])
		);
	}
	echo '</ul>';
}

/**
 * Enqueue child stylesheet after parent.
 */
function rh_base_child_enqueue_styles(): void {
	wp_enqueue_style(
		'rh-base-child-style',
		get_stylesheet_uri(),
		array('rh-base-style'),
		wp_get_theme()->get('Version')
	);

	wp_enqueue_style(
		'rh-carpentry-fonts',
		'https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,500;0,600;0,700;0,800;1,600;1,700;1,800&display=swap',
		array(),
		null
	);

	wp_enqueue_style(
		'font-awesome-6',
		'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css',
		array(),
		'6.5.2'
	);

	if (is_front_page()) {
		wp_enqueue_style(
			'rh-carpentry-custom-cursor',
			get_stylesheet_directory_uri() . '/assets/css/custom-cursor.css',
			array('rh-base-child-style'),
			wp_get_theme()->get('Version')
		);

		wp_enqueue_script(
			'rh-carpentry-custom-cursor',
			get_stylesheet_directory_uri() . '/assets/js/custom-cursor.js',
			array(),
			wp_get_theme()->get('Version'),
			true
		);
	}

	$home_hero_css_path = get_stylesheet_directory() . '/assets/css/home-hero.css';
	wp_enqueue_style(
		'rh-carpentry-home-hero',
		get_stylesheet_directory_uri() . '/assets/css/home-hero.css',
		array('rh-base-child-style', 'rh-carpentry-fonts', 'font-awesome-6'),
		file_exists($home_hero_css_path) ? (string) filemtime($home_hero_css_path) : wp_get_theme()->get('Version')
	);

	$site_footer_css_path = get_stylesheet_directory() . '/assets/css/site-footer.css';
	wp_enqueue_style(
		'rh-carpentry-site-footer',
		get_stylesheet_directory_uri() . '/assets/css/site-footer.css',
		array('rh-base-child-style', 'rh-carpentry-fonts', 'font-awesome-6'),
		file_exists($site_footer_css_path) ? (string) filemtime($site_footer_css_path) : wp_get_theme()->get('Version')
	);

	$rh_landing_surfaces = is_page() || is_singular('rh_insight') || is_singular('rh_project') || is_post_type_archive('rh_insight') || is_post_type_archive('rh_project') || is_tax('rh_project_sector') || (! is_front_page() && ! is_admin());
	if ($rh_landing_surfaces) {
		$landing_css = get_stylesheet_directory() . '/assets/css/landing-page.css';
		wp_enqueue_style(
			'rh-carpentry-landing',
			get_stylesheet_directory_uri() . '/assets/css/landing-page.css',
			array('rh-base-child-style'),
			file_exists($landing_css) ? (string) filemtime($landing_css) : wp_get_theme()->get('Version')
		);
	}

	if (! is_front_page()) {
		$site_top_bar_css_path = get_stylesheet_directory() . '/assets/css/site-top-bar.css';
		wp_enqueue_style(
			'rh-carpentry-site-top-bar',
			get_stylesheet_directory_uri() . '/assets/css/site-top-bar.css',
			array('rh-base-child-style', 'rh-carpentry-fonts'),
			file_exists($site_top_bar_css_path) ? (string) filemtime($site_top_bar_css_path) : wp_get_theme()->get('Version')
		);
	}

	$home_hero_js_path = get_stylesheet_directory() . '/assets/js/home-hero.js';
	wp_enqueue_script(
		'rh-carpentry-home-hero',
		get_stylesheet_directory_uri() . '/assets/js/home-hero.js',
		array(),
		file_exists($home_hero_js_path) ? (string) filemtime($home_hero_js_path) : wp_get_theme()->get('Version'),
		true
	);

	wp_localize_script(
		'rh-carpentry-home-hero',
		'rhContactForm',
		array(
			'ajaxUrl'  => admin_url('admin-ajax.php'),
			'action'   => 'rh_home_contact',
			'messages' => rh_carpentry_home_contact_messages(),
			'devMode'  => rh_carpentry_contact_form_dev_mode_enabled(),
		)
	);

	if (is_404()) {
		$error_404_css = get_stylesheet_directory() . '/assets/css/error-404.css';
		wp_enqueue_style(
			'rh-carpentry-error-404',
			get_stylesheet_directory_uri() . '/assets/css/error-404.css',
			array('rh-base-child-style', 'rh-carpentry-home-hero', 'rh-carpentry-fonts', 'font-awesome-6'),
			file_exists($error_404_css) ? (string) filemtime($error_404_css) : wp_get_theme()->get('Version')
		);

		$error_404_js = get_stylesheet_directory() . '/assets/js/error-404.js';
		wp_enqueue_script(
			'rh-carpentry-error-404',
			get_stylesheet_directory_uri() . '/assets/js/error-404.js',
			array(),
			file_exists($error_404_js) ? (string) filemtime($error_404_js) : wp_get_theme()->get('Version'),
			true
		);
	}

}
add_action('wp_enqueue_scripts', 'rh_base_child_enqueue_styles', 20);

/**
 * Body classes for home layout.
 *
 * @param string[] $classes Body classes.
 * @return string[]
 */
function rh_carpentry_body_class(array $classes): array {
	if (is_front_page()) {
		$classes[] = 'rh-carpentry-home';
	} else {
		$classes[] = 'rh-carpentry-inner';
	}

	return $classes;
}
add_filter('body_class', 'rh_carpentry_body_class');

/**
 * Contact page URL for “Get in touch” CTAs (not customizable).
 */
function rh_carpentry_contact_page_url(): string {
	/**
	 * Filter the contact URL used for hero and home CTAs.
	 *
	 * @param string $url Contact page or home #contact URL.
	 */
	return (string) apply_filters('rh_carpentry_contact_page_url', rh_carpentry_contact_url());
}

/**
 * User-facing messages for the homepage contact form.
 *
 * @return array<string, string>
 */
function rh_carpentry_home_contact_messages(): array {
	return array(
		'sent'     => __('Thank you — your message has been sent.', 'rh-base-child'),
		'required' => __('Please fill in your name, a valid email, and a message.', 'rh-base-child'),
		'invalid'  => __('Something went wrong. Please try again.', 'rh-base-child'),
		'failed'   => __('We could not send your message right now. Please try again or call us directly.', 'rh-base-child'),
	);
}

/**
 * Validate and send the homepage contact form.
 *
 * @return string Status key: sent, required, or failed.
 */
function rh_carpentry_process_home_contact_submission(): string {
	$name    = sanitize_text_field(wp_unslash($_POST['rh_contact_name'] ?? ''));
	$email   = sanitize_email(wp_unslash($_POST['rh_contact_email'] ?? ''));
	$phone   = sanitize_text_field(wp_unslash($_POST['rh_contact_phone'] ?? ''));
	$message = sanitize_textarea_field(wp_unslash($_POST['rh_contact_message'] ?? ''));

	if ($name === '' || ! is_email($email) || $message === '') {
		return 'required';
	}

	if (! rh_carpentry_send_contact_form_email($name, $email, $phone, $message)) {
		return 'failed';
	}

	return 'sent';
}

/**
 * JSON response for AJAX contact form submissions.
 *
 * @param string $status Status key from rh_carpentry_process_home_contact_submission() or invalid.
 */
function rh_carpentry_home_contact_ajax_respond(string $status): void {
	$messages = rh_carpentry_home_contact_messages();
	$message  = $messages[ $status ] ?? $messages['invalid'];

	if ($status === 'sent') {
		wp_send_json_success(
			array(
				'status'  => $status,
				'message' => $message,
			)
		);
	}

	wp_send_json_error(
		array(
			'status'  => $status,
			'message' => $message,
		)
	);
}

/**
 * AJAX handler for the homepage contact form.
 */
function rh_carpentry_ajax_home_contact_submit(): void {
	if (! check_ajax_referer('rh_home_contact', 'rh_home_contact_nonce', false)) {
		rh_carpentry_home_contact_ajax_respond('invalid');
	}

	rh_carpentry_home_contact_ajax_respond(rh_carpentry_process_home_contact_submission());
}
add_action('wp_ajax_rh_home_contact', 'rh_carpentry_ajax_home_contact_submit');
add_action('wp_ajax_nopriv_rh_home_contact', 'rh_carpentry_ajax_home_contact_submit');

/**
 * Non-AJAX fallback: homepage contact form via admin-post.php.
 */
function rh_carpentry_handle_home_contact_submit(): void {
	$return_base = rh_carpentry_contact_return_base_url();

	if (! isset($_POST['rh_home_contact_nonce']) || ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['rh_home_contact_nonce'])), 'rh_home_contact')) {
		wp_redirect(rh_carpentry_contact_overlay_url('invalid', $return_base), 302);
		exit;
	}

	$status = rh_carpentry_process_home_contact_submission();
	wp_redirect(rh_carpentry_contact_overlay_url($status, $return_base), 302);
	exit;
}
add_action('admin_post_nopriv_rh_home_contact', 'rh_carpentry_handle_home_contact_submit');
add_action('admin_post_rh_home_contact', 'rh_carpentry_handle_home_contact_submit');

/**
 * Projects archive URL: CPT archive when registered, else /projects/.
 */
function rh_carpentry_projects_archive_url(): string {
	if (post_type_exists('rh_project')) {
		$link = get_post_type_archive_link('rh_project');
		if (is_string($link) && $link !== '') {
			return $link;
		}
	}
	return home_url('/projects/');
}

/**
 * Build tel: href from a display phone string (digits only).
 */
function rh_carpentry_tel_href_from_display(string $display): string {
	$digits = preg_replace('/\D+/', '', $display);
	return $digits !== '' ? 'tel:' . $digits : '';
}

/**
 * Build mailto: href from an email field (must pass is_email).
 */
function rh_carpentry_mailto_href_from_email(string $email): string {
	$email = trim($email);
	return is_email($email) ? 'mailto:' . $email : '';
}

/**
 * Testimonial slides (homepage + About page).
 *
 * @return array<int, array{quote: string, name: string, role: string, company: string}>
 */
function rh_carpentry_testimonials(): array {
	return array(
		array(
			'quote'   => __('R H Carpenters kept the programme moving and delivered a first-class finish. The site ran cleanly and communication stayed clear throughout.', 'rh-base-child'),
			'name'    => __('Michael Thompson', 'rh-base-child'),
			'role'    => __('Site Manager', 'rh-base-child'),
			'company' => __('Regional Main Contractor', 'rh-base-child'),
		),
		array(
			'quote'   => __('From first fix through to final handover, every trade interaction was professional. The quality of workmanship has been excellent.', 'rh-base-child'),
			'name'    => __('Sarah Mitchell', 'rh-base-child'),
			'role'    => __('Project Lead', 'rh-base-child'),
			'company' => __('Residential Development', 'rh-base-child'),
		),
		array(
			'quote'   => __('They understood our brief immediately, solved issues early, and completed on time. A dependable team we would appoint again.', 'rh-base-child'),
			'name'    => __('David Chen', 'rh-base-child'),
			'role'    => __('Commercial Client', 'rh-base-child'),
			'company' => __('Essex', 'rh-base-child'),
		),
		array(
			'quote'   => __('Strong planning, reliable attendance, and real attention to detail. The joinery and finishing standards were consistently high.', 'rh-base-child'),
			'name'    => __('Rachel Owens', 'rh-base-child'),
			'role'    => __('Contracts Manager', 'rh-base-child'),
			'company' => __('Fit-Out Partner', 'rh-base-child'),
		),
		array(
			'quote'   => __('The team coordinated well with other trades and kept snagging to a minimum. Finish quality was exactly what we needed for a high-spec residential scheme.', 'rh-base-child'),
			'name'    => __('James Hartley', 'rh-base-child'),
			'role'    => __('Development Director', 'rh-base-child'),
			'company' => __('Private Developer', 'rh-base-child'),
		),
		array(
			'quote'   => __('Clear pricing, tidy site standards, and carpenters who actually turn up when they say they will. Refreshing to work with.', 'rh-base-child'),
			'name'    => __('Emma Patel', 'rh-base-child'),
			'role'    => __('Homeowner', 'rh-base-child'),
			'company' => __('Extension & loft', 'rh-base-child'),
		),
		array(
			'quote'   => __('They took ownership of the joinery package and pushed details forward before they became problems. Handover was straightforward.', 'rh-base-child'),
			'name'    => __('Tom Williams', 'rh-base-child'),
			'role'    => __('Site Agent', 'rh-base-child'),
			'company' => __('Regional Builder', 'rh-base-child'),
		),
		array(
			'quote'   => __('Excellent craftsmanship on bespoke storage and stair details. The client was delighted with the end result.', 'rh-base-child'),
			'name'    => __('Laura Brooks', 'rh-base-child'),
			'role'    => __('Interior Designer', 'rh-base-child'),
			'company' => __('Studio practice', 'rh-base-child'),
		),
		array(
			'quote'   => __('From structural work through to final decoration touch-ups, communication was steady and the programme stayed realistic.', 'rh-base-child'),
			'name'    => __('Mark Foster', 'rh-base-child'),
			'role'    => __('Project Manager', 'rh-base-child'),
			'company' => __('Commercial refurbishment', 'rh-base-child'),
		),
	);
}

/**
 * Bundled client logos from legacy About us page, same order as
 * https://rhcarpentersukltd.co.uk/about-us/ — files under assets/images/client-logos/about-*.
 *
 * @return array<int, array{url: string, alt: string}>
 */
function rh_carpentry_client_logos_bundled_defaults(): array {
	$base = get_stylesheet_directory_uri() . '/assets/images/client-logos/';
	$dir  = get_stylesheet_directory() . '/assets/images/client-logos/';
	$set  = array(
		array( 'file' => 'about-frame-7.png', 'alt' => __( 'Client partner', 'rh-base-child' ) ),
		array( 'file' => 'about-frame-7-1.png', 'alt' => __( 'Client partner', 'rh-base-child' ) ),
		array( 'file' => 'about-frame-7-2.png', 'alt' => __( 'Client partner', 'rh-base-child' ) ),
		array( 'file' => 'about-frame-7-3.png', 'alt' => __( 'Client partner', 'rh-base-child' ) ),
		array( 'file' => 'about-planet14.png', 'alt' => __( 'Planet 14 Homes', 'rh-base-child' ) ),
		array( 'file' => 'about-rose.png', 'alt' => __( 'Rose', 'rh-base-child' ) ),
		array( 'file' => 'about-scott.png', 'alt' => __( 'Scott', 'rh-base-child' ) ),
		array( 'file' => 'about-mersa.png', 'alt' => __( 'Mersa', 'rh-base-child' ) ),
		array( 'file' => 'about-evers.png', 'alt' => __( 'Evers', 'rh-base-child' ) ),
		array( 'file' => 'about-ci-logo.png', 'alt' => __( 'Client logo', 'rh-base-child' ) ),
		array( 'file' => 'about-mab.png', 'alt' => __( 'MAB', 'rh-base-child' ) ),
		array( 'file' => 'about-ark.png', 'alt' => __( 'Ark', 'rh-base-child' ) ),
		array( 'file' => 'about-phelan.png', 'alt' => __( 'Phelan', 'rh-base-child' ) ),
		array( 'file' => 'about-marden.png', 'alt' => __( 'Marden', 'rh-base-child' ) ),
		array( 'file' => 'about-salvation-army.png', 'alt' => __( 'Salvation Army', 'rh-base-child' ) ),
		array( 'file' => 'about-shipp.jpg', 'alt' => __( 'Shipp', 'rh-base-child' ) ),
		array( 'file' => 'about-tgl.jpg', 'alt' => __( 'TGL Properties Ltd', 'rh-base-child' ) ),
		array( 'file' => 'about-french-seh.png', 'alt' => __( 'French SEH Construction', 'rh-base-child' ) ),
	);
	$out = array();
	foreach ( $set as $row ) {
		$path = $dir . $row['file'];
		if ( is_readable( $path ) ) {
			$out[] = array(
				'url' => $base . $row['file'],
				'alt' => $row['alt'],
			);
		}
	}
	return $out;
}

/**
 * Client logos for homepage marquee (Customizer media IDs, else bundled About-us client strip).
 *
 * @return array<int, array{url: string, alt: string}>
 */
function rh_carpentry_client_logos_items(): array {
	$items = array();
	$max   = (int) apply_filters('rh_carpentry_client_logo_customizer_slots', 30);
	if ($max < 1) {
		$max = 30;
	}
	for ($i = 1; $i <= $max; $i++) {
		$id = (int) get_theme_mod('rh_client_logo_' . $i . '_id', 0);
		if ($id <= 0) {
			continue;
		}
		$url = wp_get_attachment_image_url($id, 'medium');
		if (! is_string($url) || $url === '') {
			continue;
		}
		$alt = (string) get_post_meta($id, '_wp_attachment_image_alt', true);
		$items[] = array(
			'url' => $url,
			'alt' => $alt !== '' ? $alt : __('Client logo', 'rh-base-child'),
		);
	}

	if ($items !== array()) {
		return $items;
	}

	$bundled = rh_carpentry_client_logos_bundled_defaults();
	if ($bundled === array()) {
		return array();
	}

	$n              = count($bundled);
	$default_repeat = $n >= 12 ? 1 : ($n >= 6 ? 2 : 4);
	$repeat         = (int) apply_filters('rh_carpentry_client_logos_bundled_repeat', $default_repeat);
	$repeat         = max(1, min(12, $repeat));
	for ($t = 0; $t < $repeat; $t++) {
		foreach ($bundled as $row) {
			$items[] = $row;
		}
	}

	return $items;
}

/**
 * Footer primary menu when no menu is assigned to the Primary location.
 */
function rh_carpentry_footer_menu_fallback(): void {
	$items = array(
		array(
			'label' => __('Home', 'rh-base-child'),
			'url'   => home_url('/'),
		),
		array(
			'label' => __('About', 'rh-base-child'),
			'url'   => rh_carpentry_about_page_url(),
		),
		array(
			'label' => __('Services', 'rh-base-child'),
			'url'   => rh_carpentry_services_hub_url(),
		),
		array(
			'label' => __('Projects', 'rh-base-child'),
			'url'   => rh_carpentry_projects_archive_url(),
		),
		array(
			'label' => __('Contact', 'rh-base-child'),
			'url'   => rh_carpentry_contact_url(),
		),
	);

	echo '<ul id="rh-footer-primary-menu" class="rh-site-footer__menu">';
	foreach ($items as $item) {
		printf(
			'<li><a href="%s">%s</a></li>',
			esc_url($item['url']),
			esc_html($item['label'])
		);
	}
	echo '</ul>';
}
