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
			$item->url = rh_carpentry_home_section_url('about');
			continue;
		}
		if ($path === '/services/') {
			$item->url = rh_carpentry_home_section_url('services');
			continue;
		}
		if ($path === '/contact/') {
			$item->url = rh_carpentry_home_section_url('contact');
			continue;
		}
		$proj_path = (string) wp_parse_url($projects, PHP_URL_PATH);
		$proj_path = '/' . trim($proj_path, '/') . '/';
		if ($path === $proj_path || $path === '/projects/') {
			$item->url = rh_carpentry_home_section_url('projects');
			continue;
		}

		$frag = (string) wp_parse_url($url, PHP_URL_FRAGMENT);
		$legacy_sections = array(
			'rh-home-section-about'    => 'about',
			'rh-home-section-services' => 'services',
			'rh-home-section-projects' => 'projects',
			'rh-home-about-heading'    => 'about',
			'rh-home-work-heading'     => 'services',
			'rh-home-projects-heading' => 'projects',
		);
		if (isset($legacy_sections[ $frag ])) {
			$item->url = rh_carpentry_home_section_url($legacy_sections[ $frag ]);
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
			'url'   => rh_carpentry_home_section_url('about'),
		),
		array(
			'label' => __('Services', 'rh-base-child'),
			'url'   => rh_carpentry_home_section_url('services'),
		),
		array(
			'label' => __('Projects', 'rh-base-child'),
			'url'   => rh_carpentry_home_section_url('projects'),
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

	$rh_brand_surfaces = is_front_page()
		|| is_post_type_archive('rh_project')
		|| is_singular('rh_project');

	if ($rh_brand_surfaces) {
		$home_hero_css_path = get_stylesheet_directory() . '/assets/css/home-hero.css';
		wp_enqueue_style(
			'rh-carpentry-home-hero',
			get_stylesheet_directory_uri() . '/assets/css/home-hero.css',
			array('rh-base-child-style', 'rh-carpentry-fonts', 'font-awesome-6'),
			file_exists($home_hero_css_path) ? (string) filemtime($home_hero_css_path) : wp_get_theme()->get('Version')
		);
	}

	$site_footer_css_path = get_stylesheet_directory() . '/assets/css/site-footer.css';
	wp_enqueue_style(
		'rh-carpentry-site-footer',
		get_stylesheet_directory_uri() . '/assets/css/site-footer.css',
		array('rh-base-child-style', 'rh-carpentry-fonts', 'font-awesome-6'),
		file_exists($site_footer_css_path) ? (string) filemtime($site_footer_css_path) : wp_get_theme()->get('Version')
	);

	if (is_front_page()) {
		$home_hero_js_path = get_stylesheet_directory() . '/assets/js/home-hero.js';
		wp_enqueue_script(
			'rh-carpentry-home-hero',
			get_stylesheet_directory_uri() . '/assets/js/home-hero.js',
			array(),
			file_exists($home_hero_js_path) ? (string) filemtime($home_hero_js_path) : wp_get_theme()->get('Version'),
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
	}

	return $classes;
}
add_filter('body_class', 'rh_carpentry_body_class');

/**
 * Contact page URL for “Get in touch” CTAs (not customizable).
 */
function rh_carpentry_contact_page_url(): string {
	$url = home_url('/contact/');
	/**
	 * Filter the fixed contact page URL used for hero and home CTAs.
	 *
	 * @param string $url Default contact page URL.
	 */
	return (string) apply_filters('rh_carpentry_contact_page_url', $url);
}

/**
 * Handle homepage full-screen contact form (admin-post.php).
 */
function rh_carpentry_handle_home_contact_submit(): void {
	if (! isset($_POST['rh_home_contact_nonce']) || ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['rh_home_contact_nonce'])), 'rh_home_contact')) {
		wp_safe_redirect(esc_url_raw(add_query_arg('contact', 'invalid', trailingslashit(home_url('/'))) . '#contact'));
		exit;
	}

	$name    = sanitize_text_field(wp_unslash($_POST['rh_contact_name'] ?? ''));
	$email   = sanitize_email(wp_unslash($_POST['rh_contact_email'] ?? ''));
	$phone   = sanitize_text_field(wp_unslash($_POST['rh_contact_phone'] ?? ''));
	$message = sanitize_textarea_field(wp_unslash($_POST['rh_contact_message'] ?? ''));

	if ($name === '' || ! is_email($email) || $message === '') {
		wp_safe_redirect(esc_url_raw(add_query_arg('contact', 'required', trailingslashit(home_url('/'))) . '#contact'));
		exit;
	}

	$admin = (string) get_option('admin_email');
	$subj  = sprintf(
		/* translators: %s: site name */
		__('[%s] Website contact form', 'rh-base-child'),
		wp_specialchars_decode((string) get_bloginfo('name'), ENT_QUOTES)
	);
	$body = "Name: {$name}\nEmail: {$email}\nPhone: {$phone}\n\n{$message}\n";
	wp_mail($admin, $subj, $body, array('Reply-To: ' . $email));

	wp_safe_redirect(esc_url_raw(add_query_arg('contact', 'sent', trailingslashit(home_url('/'))) . '#contact'));
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
 * Example portfolio carousel cards: bundled 1200×675 JPEGs (landscape) under assets/images/home-projects/.
 *
 * Appended after real rh_project posts on the front page so the strip always has sample work to show.
 *
 * @return array<int, array{id: int, title: string, url: string, image_id: int, image_url?: string, badges: string[]}>
 */
function rh_carpentry_home_projects_example_cards(): array {
	$base_uri = get_stylesheet_directory_uri() . '/assets/images/home-projects/';
	$base_dir = get_stylesheet_directory() . '/assets/images/home-projects/';
	$archive  = rh_carpentry_projects_archive_url();

	$titles = array(
		__( 'Oak kitchen & utility fit-out', 'rh-base-child' ),
		__( 'Loft conversion — dormers & stairs', 'rh-base-child' ),
		__( 'Timber frame extension shell', 'rh-base-child' ),
		__( 'Bespoke wardrobes & panelling', 'rh-base-child' ),
		__( 'Full house renovation — phase one', 'rh-base-child' ),
		__( 'Garden room & cedar cladding', 'rh-base-child' ),
		__( 'Commercial shopfront & internals', 'rh-base-child' ),
		__( 'Roof structure & velux package', 'rh-base-child' ),
		__( 'Open-plan knock-through & steels', 'rh-base-child' ),
		__( 'New-build second fix package', 'rh-base-child' ),
	);

	$badges_by_card = array(
		array(
			__( 'Joinery', 'rh-base-child' ),
			__( 'Kitchens', 'rh-base-child' ),
			__( 'Refurbishment', 'rh-base-child' ),
		),
		array(
			__( 'Extensions', 'rh-base-child' ),
			__( 'Roofs', 'rh-base-child' ),
			__( 'Stairs', 'rh-base-child' ),
		),
		array(
			__( 'Timber frame', 'rh-base-child' ),
			__( 'Extensions', 'rh-base-child' ),
			__( 'First fix', 'rh-base-child' ),
		),
		array(
			__( 'Joinery', 'rh-base-child' ),
			__( 'Fitted furniture', 'rh-base-child' ),
			__( 'Interiors', 'rh-base-child' ),
		),
		array(
			__( 'Refurbishment', 'rh-base-child' ),
			__( 'Maintenance', 'rh-base-child' ),
			__( 'Finishing', 'rh-base-child' ),
		),
		array(
			__( 'Extensions', 'rh-base-child' ),
			__( 'Cladding', 'rh-base-child' ),
			__( 'Garden rooms', 'rh-base-child' ),
		),
		array(
			__( 'Commercial', 'rh-base-child' ),
			__( 'Fit-out', 'rh-base-child' ),
			__( 'Shopfitting', 'rh-base-child' ),
		),
		array(
			__( 'Roofs', 'rh-base-child' ),
			__( 'Structural', 'rh-base-child' ),
			__( 'Velux', 'rh-base-child' ),
		),
		array(
			__( 'Refurbishment', 'rh-base-child' ),
			__( 'Structural', 'rh-base-child' ),
			__( 'Open plan', 'rh-base-child' ),
		),
		array(
			__( 'New build', 'rh-base-child' ),
			__( 'Second fix', 'rh-base-child' ),
			__( 'Joinery', 'rh-base-child' ),
		),
	);

	$out = array();
	for ( $i = 1; $i <= 10; $i++ ) {
		$file = 'ex-' . $i . '.jpg';
		$path = $base_dir . $file;
		if ( ! is_readable( $path ) ) {
			continue;
		}
		$out[] = array(
			'id'        => 0,
			'title'     => $titles[ $i - 1 ],
			'url'       => $archive,
			'image_id'  => 0,
			'image_url' => $base_uri . $file,
			'badges'    => isset( $badges_by_card[ $i - 1 ] ) ? $badges_by_card[ $i - 1 ] : array(),
		);
	}

	return $out;
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
			'url'   => rh_carpentry_home_section_url('about'),
		),
		array(
			'label' => __('Services', 'rh-base-child'),
			'url'   => rh_carpentry_home_section_url('services'),
		),
		array(
			'label' => __('Projects', 'rh-base-child'),
			'url'   => rh_carpentry_home_section_url('projects'),
		),
		array(
			'label' => __('Contact', 'rh-base-child'),
			'url'   => rh_carpentry_home_section_url('contact'),
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
