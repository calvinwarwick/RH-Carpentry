<?php
/**
 * SEO landing page seeding and URL helpers.
 *
 * @package RH_Base_Child
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
	exit;
}

/**
 * Get a published page by path (supports nested paths e.g. services/timber-framed-buildings).
 */
function rh_carpentry_get_page_by_path(string $path): ?WP_Post {
	$path = trim($path, '/');
	if ($path === '') {
		return null;
	}
	$page = get_page_by_path($path, OBJECT, 'page');
	if ($page instanceof WP_Post && $page->post_status === 'publish') {
		return $page;
	}
	return null;
}

/**
 * URL for a page if it exists, else homepage fragment fallback.
 *
 * @param string $path     Page path without leading slash (e.g. about, services/timber-framed-buildings).
 * @param string $fragment Homepage fragment if page missing (e.g. about, services, contact).
 */
function rh_carpentry_page_url(string $path, string $fragment = ''): string {
	$page = rh_carpentry_get_page_by_path($path);
	if ($page instanceof WP_Post) {
		return (string) get_permalink($page);
	}
	if ($fragment !== '') {
		return rh_carpentry_home_section_url($fragment);
	}
	return home_url('/' . trim($path, '/') . '/');
}

/**
 * Page template slug used for seeded SEO pages.
 */
function rh_carpentry_landing_page_template(): string {
	return 'page-templates/template-landing.php';
}

/**
 * Create or update one page.
 *
 * @param array{path: string, title: string, content: string, parent?: int, template?: string, meta_description?: string} $args Page args.
 */
function rh_carpentry_upsert_page(array $args): int {
	$path = trim($args['path'], '/');
	$existing = rh_carpentry_get_page_by_path($path);
	$parent_id = isset($args['parent']) ? (int) $args['parent'] : 0;
	$template  = $args['template'] ?? rh_carpentry_landing_page_template();

	$parts = explode('/', $path);
	$post_name = (string) end($parts);

	$postarr = array(
		'post_title'   => $args['title'],
		'post_name'    => $post_name,
		'post_content' => $args['content'],
		'post_status'  => 'publish',
		'post_type'    => 'page',
		'post_parent'  => $parent_id,
	);

	if ($existing instanceof WP_Post) {
		$postarr['ID'] = (int) $existing->ID;
		$page_id       = wp_update_post($postarr, true);
	} else {
		$page_id = wp_insert_post($postarr, true);
	}

	if (is_wp_error($page_id) || ! is_int($page_id) || $page_id <= 0) {
		return 0;
	}

	update_post_meta($page_id, '_wp_page_template', $template);

	if (! empty($args['meta_description'])) {
		$meta_desc = sanitize_text_field($args['meta_description']);
		update_post_meta($page_id, 'rh_meta_description', $meta_desc);
		update_post_meta($page_id, 'rank_math_description', $meta_desc);
	}

	return $page_id;
}

/**
 * Build Gutenberg-friendly page content from paragraphs.
 *
 * @param string[] $paragraphs Paragraphs of plain text.
 */
function rh_carpentry_page_content_html(array $paragraphs): string {
	$html = '';
	foreach ($paragraphs as $p) {
		$p = trim($p);
		if ($p === '') {
			continue;
		}
		$html .= '<p>' . esc_html($p) . '</p>' . "\n";
	}
	return $html;
}

/**
 * Seed all SEO pages (idempotent).
 *
 * @return array{created: int, updated: int}
 */
function rh_carpentry_seed_seo_pages(): array {
	$stats = array(
		'created' => 0,
		'updated' => 0,
	);

	$about_content = rh_carpentry_page_content_html(
		array(
			__('Our team covers timber framing, roofing, refurbishments, barn conversions, bespoke joinery, commercial fit-out and certified fire door installation. Projects range from single extensions to multi-dwelling schemes and education buildings.', 'rh-base-child'),
		)
	);

	$about_id = rh_carpentry_get_page_by_path('about') ? 1 : 0;
	$about_new = rh_carpentry_upsert_page(
		array(
			'path'             => 'about',
			'title'            => __('About RH Carpentry', 'rh-base-child'),
			'content'          => $about_content,
			'meta_description' => __('About R H Carpenters (UK) Ltd — 40+ years delivering carpentry and complete build packages across Essex.', 'rh-base-child'),
		)
	);
	if ($about_new > 0) {
		$about_id === 0 ? $stats['created']++ : $stats['updated']++;
	}

	$services_hub = rh_carpentry_page_content_html(
		array(
			__('We provide a full carpentry service or complete build package for residential and commercial clients. Select a service below to learn more, or view our project portfolio for examples of recent work.', 'rh-base-child'),
		)
	);
	$services_id = rh_carpentry_upsert_page(
		array(
			'path'             => 'services',
			'title'            => __('Our services', 'rh-base-child'),
			'content'          => $services_hub,
			'meta_description' => __('Carpentry and construction services in Essex — timber frame, roofing, refurbishments, barn conversions, joinery, commercial fit-out and fire doors.', 'rh-base-child'),
		)
	);
	if ($services_id > 0) {
		$stats['created']++;
	}

	foreach (rh_carpentry_services() as $service) {
		$content = rh_carpentry_page_content_html(
			array(
				$service['intro'],
				$service['body'],
				__('Contact us to discuss your project, programme and budget. We are happy to visit site or work from your drawings and specifications.', 'rh-base-child'),
			)
		);
		$desc = sprintf(
			/* translators: %s: service name */
			__('%s in Essex — RH Carpentry & Construction. Free consultation.', 'rh-base-child'),
			$service['label']
		);
		$pid = rh_carpentry_upsert_page(
			array(
				'path'             => 'services/' . $service['slug'],
				'title'            => $service['label'],
				'content'          => $content,
				'parent'           => $services_id,
				'meta_description' => $desc,
			)
		);
		if ($pid > 0) {
			$stats['created']++;
		}
	}

	$contact_content = rh_carpentry_page_content_html(
		array(
			__('Use the form below to tell us about your project. We aim to respond within one working day. You can also call or email using the details in the footer.', 'rh-base-child'),
			__('For urgent site issues on active contracts, please call the mobile numbers listed on our correspondence.', 'rh-base-child'),
		)
	);
	$contact_id = rh_carpentry_upsert_page(
		array(
			'path'             => 'contact',
			'title'            => __('Contact us', 'rh-base-child'),
			'content'          => $contact_content,
			'template'         => 'page-contact.php',
			'meta_description' => __('Contact RH Carpentry — carpentry and build packages in Essex. Request a quote or site visit.', 'rh-base-child'),
		)
	);
	if ($contact_id > 0) {
		$stats['created']++;
	}

	$faq_items = array(
		array(
			'q' => __('What areas do you cover?', 'rh-base-child'),
			'a' => __('We are based in Great Bentley, Essex, and mainly work across north Essex, Tendring and the Suffolk border. Larger projects may be considered further afield — contact us to discuss.', 'rh-base-child'),
		),
		array(
			'q' => __('Do you offer complete build packages?', 'rh-base-child'),
			'a' => __('Yes. We can manage the full build sequence or work as a carpentry subcontractor to your main contractor, depending on project size and your preferred contract structure.', 'rh-base-child'),
		),
		array(
			'q' => __('Are you certified for fire door work?', 'rh-base-child'),
			'a' => __('Our team includes UK Fire Door Training approved installers, inspectors and maintainers. We supply, install and maintain fire doorsets with documentation suitable for your fire risk assessment.', 'rh-base-child'),
		),
		array(
			'q' => __('How do I get a quote?', 'rh-base-child'),
			'a' => __('Send drawings or a brief description via our contact form, email or phone. We will arrange a site visit where needed and provide a clear quotation.', 'rh-base-child'),
		),
	);
	$faq_html = '';
	foreach ($faq_items as $item) {
		$faq_html .= '<h2>' . esc_html($item['q']) . '</h2>' . "\n";
		$faq_html .= '<p>' . esc_html($item['a']) . '</p>' . "\n";
	}
	rh_carpentry_upsert_page(
		array(
			'path'             => 'faq',
			'title'            => __('Frequently asked questions', 'rh-base-child'),
			'content'          => $faq_html,
			'meta_description' => __('FAQ — RH Carpentry services, areas covered, fire doors and how to get a quote in Essex.', 'rh-base-child'),
		)
	);

	rh_carpentry_upsert_page(
		array(
			'path'             => 'privacy-policy',
			'title'            => __('Privacy policy', 'rh-base-child'),
			'content'          => rh_carpentry_page_content_html(
				array(
					__('R H Carpenters (UK) Ltd respects your privacy. Information submitted via our contact form is used only to respond to your enquiry and manage our business relationship. We do not sell your data to third parties.', 'rh-base-child'),
					__('For questions about how we handle personal data, contact us using the details on our contact page.', 'rh-base-child'),
				)
			),
			'meta_description' => __('Privacy policy for RH Carpentry & Construction (R H Carpenters UK Ltd).', 'rh-base-child'),
		)
	);

	$business_html = rh_carpentry_page_content_html(
		array(
			__('Legal name: R H Carpenters (UK) Ltd', 'rh-base-child'),
			__('Trading as: RH Carpentry & Construction', 'rh-base-child'),
			__('Website: https://rhcarpentry.uk/', 'rh-base-child'),
			__('Telephone: 01206 250577', 'rh-base-child'),
			__('Email: info@rhcarpentersukltd.co.uk', 'rh-base-child'),
			__('Address: Bouverie, St Mary\'s Road, Aingers Green, Great Bentley, Colchester, Essex, CO7 8NN, United Kingdom', 'rh-base-child'),
			__('Services: Timber framed buildings; full refurbishment; hand cut and trussed roofs; complete new build projects; barn conversions; general maintenance; extensions and loft conversions; bespoke joinery; commercial fit-out; fire door installation, maintenance and inspection.', 'rh-base-child'),
			__('Service area: Primarily north Essex, Colchester, Tendring and East Anglia.', 'rh-base-child'),
			__('Accreditations: CITB, CHAS, FSB; UK Fire Door Training approved installer.', 'rh-base-child'),
		)
	);
	rh_carpentry_upsert_page(
		array(
			'path'             => 'business',
			'title'            => __('Business information', 'rh-base-child'),
			'content'          => $business_html,
			'meta_description' => __('Structured business facts for RH Carpentry — services, contact details and service area in Essex, UK.', 'rh-base-child'),
		)
	);

	$areas_parent = rh_carpentry_upsert_page(
		array(
			'path'             => 'areas',
			'title'            => __('Areas we cover', 'rh-base-child'),
			'content'          => rh_carpentry_page_content_html(
				array(
					__('We undertake carpentry and construction work across Essex and East Anglia. Browse the areas below or contact us to confirm coverage for your site.', 'rh-base-child'),
				)
			),
			'meta_description' => __('Areas served by RH Carpentry — Colchester, Tendring, Clacton, Great Bentley and wider Essex.', 'rh-base-child'),
		)
	);

	foreach (rh_carpentry_area_pages() as $area) {
		rh_carpentry_upsert_page(
			array(
				'path'             => 'areas/' . $area['slug'],
				'title'            => $area['title'],
				'content'          => rh_carpentry_page_content_html(array($area['intro'], $area['body'])),
				'parent'           => $areas_parent,
				'meta_description' => wp_strip_all_tags($area['intro']),
			)
		);
	}

	update_option('rh_seo_pages_seeded_version', '1', true);

	return $stats;
}

/**
 * Seed on admin when theme active (once).
 */
function rh_carpentry_maybe_seed_seo_pages(): void {
	if (! current_user_can('manage_options')) {
		return;
	}
	if (get_option('rh_seo_pages_seeded_version') === '1') {
		return;
	}
	rh_carpentry_seed_seo_pages();
}
add_action('admin_init', 'rh_carpentry_maybe_seed_seo_pages', 20);

/**
 * Seed SEO pages when child theme is activated.
 */
function rh_carpentry_seed_seo_on_theme_switch(string $stylesheet): void {
	if ($stylesheet !== 'rh-base-child') {
		return;
	}
	if (get_option('rh_seo_pages_seeded_version') === '1') {
		return;
	}
	rh_carpentry_seed_seo_pages();
	do_action('rh_seo_pages_seeded');
}
add_action('after_switch_theme', 'rh_carpentry_seed_seo_on_theme_switch', 15);

/**
 * Assign primary menu items to real pages when available.
 */
function rh_carpentry_sync_primary_menu(): void {
	$locations = get_nav_menu_locations();
	if (! isset($locations['primary']) || (int) $locations['primary'] <= 0) {
		return;
	}

	$menu_id = (int) $locations['primary'];
	$items   = wp_get_nav_menu_items($menu_id);
	if (! is_array($items)) {
		return;
	}

	$targets = array(
		'/about/'    => rh_carpentry_about_page_url(),
		'/services/' => rh_carpentry_services_landing_url(),
		'/contact/'  => rh_carpentry_contact_url(),
		'/projects/' => rh_carpentry_projects_archive_url(),
	);

	foreach ($items as $item) {
		if (! $item instanceof WP_Post || ! isset($item->url)) {
			continue;
		}
		$path = '/' . trim((string) wp_parse_url($item->url, PHP_URL_PATH), '/') . '/';
		if (isset($targets[ $path ])) {
			$new_url = $targets[ $path ];
			if ($new_url !== $item->url) {
				wp_update_nav_menu_item(
					$menu_id,
					(int) $item->ID,
					array(
						'menu-item-title'  => $item->title,
						'menu-item-url'    => $new_url,
						'menu-item-status' => 'publish',
					)
				);
			}
		}
	}
}

/**
 * Run menu sync after seeding.
 */
function rh_carpentry_after_seo_seed(): void {
	rh_carpentry_sync_primary_menu();
}
add_action('rh_seo_pages_seeded', 'rh_carpentry_after_seo_seed');
