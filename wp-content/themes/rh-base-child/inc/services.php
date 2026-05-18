<?php
/**
 * Service definitions (homepage bento + SEO landing pages).
 *
 * @package RH_Base_Child
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
	exit;
}

/**
 * All services: URL slug, image card slug, sector term slug(s), copy for seeding.
 *
 * @return array<int, array{label: string, slug: string, card_slug: string, bento: string, sectors: string[], intro: string, body: string}>
 */
function rh_carpentry_services(): array {
	return array(
		array(
			'label'     => __('Timber framed buildings', 'rh-base-child'),
			'slug'      => 'timber-framed-buildings',
			'card_slug' => 'timber',
			'bento'     => 'a',
			'sectors'   => array('new-builds'),
			'intro'     => __('We design and erect timber frame structures for new homes, extensions and commercial shells across Essex and East Anglia — from sole plates and panels through to wind-tight handover.', 'rh-base-child'),
			'body'      => __('Our carpentry teams work with architects, engineers and main contractors to deliver accurate, well-coordinated timber frames. We take responsibility for setting out, crane lifts where required, and weather protection during the build programme. Whether you need a full dwelling shell or a partial frame for an extension, we plan attendance to suit your site sequence and keep communication clear with other trades.', 'rh-base-child'),
		),
		array(
			'label'     => __('Full refurbishment', 'rh-base-child'),
			'slug'      => 'full-refurbishment',
			'card_slug' => 'refurbishment',
			'bento'     => 'b',
			'sectors'   => array('refurbishment'),
			'intro'     => __('Complete refurbishment packages for residential and commercial properties — structural carpentry, interiors, finishes and coordination with M&E and decorating trades.', 'rh-base-child'),
			'body'      => __('Refurbishment projects demand careful sequencing and respect for existing buildings. We strip back, repair and rebuild where needed, then deliver first and second fix carpentry, kitchens, storage and finishing joinery. Our site standards stay high throughout: protection of occupied areas, tidy working, and realistic programmes agreed with you from the outset.', 'rh-base-child'),
		),
		array(
			'label'     => __('Hand cut & trussed roofs', 'rh-base-child'),
			'slug'      => 'hand-cut-trussed-roofs',
			'card_slug' => 'roofs',
			'bento'     => 'c',
			'sectors'   => array('new-builds', 'refurbishment'),
			'intro'     => __('Traditional cut roofs and engineered truss installations for new builds, extensions and replacements — pitched, flat and complex geometry.', 'rh-base-child'),
			'body'      => __('Roof structures are at the heart of many of our projects. We fabricate and install hand-cut roofs where character and spans demand it, and work with truss manufacturers for cost-effective solutions on larger schemes. Our teams handle bracing, sarking, insulation zones and interfaces with rooflights and dormers, always aligned to current building regulations and your structural engineer’s specification.', 'rh-base-child'),
		),
		array(
			'label'     => __('Complete new build projects', 'rh-base-child'),
			'slug'      => 'complete-new-build-projects',
			'card_slug' => 'new-build',
			'bento'     => 'd',
			'sectors'   => array('new-builds'),
			'intro'     => __('End-to-end new build carpentry and construction packages — from groundworks coordination through timber frame, roofing, joinery and practical completion.', 'rh-base-child'),
			'body'      => __('For developers and private clients we act as a dependable main package partner: programming the build, managing subcontractors where appropriate, and maintaining quality on site. Our portfolio includes multiple dwellings, bespoke houses and light commercial units across Essex. You get one team accountable for carpentry standards, snagging and handover documentation.', 'rh-base-child'),
		),
		array(
			'label'     => __('Barn conversions', 'rh-base-child'),
			'slug'      => 'barn-conversions',
			'card_slug' => 'barn',
			'bento'     => 'e',
			'sectors'   => array('barn-conversions'),
			'intro'     => __('Sensitive conversion of agricultural and period barns into high-quality homes and hospitality spaces, preserving character while meeting modern performance standards.', 'rh-base-child'),
			'body'      => __('Barn conversions require sympathy for existing fabric and precision in new structure. We stabilise and repair oak frames, install insulated linings, floors and stairs, and deliver the joinery that makes converted spaces feel finished. We work with conservation officers and designers where listing or planning conditions apply, and keep clients informed at each stage.', 'rh-base-child'),
		),
		array(
			'label'     => __('General maintenance', 'rh-base-child'),
			'slug'      => 'general-maintenance',
			'card_slug' => 'maintenance',
			'bento'     => 'f',
			'sectors'   => array('refurbishment'),
			'intro'     => __('Responsive maintenance and minor works for homeowners, landlords and commercial property managers across north Essex.', 'rh-base-child'),
			'body'      => __('Not every job is a full build — we carry out repairs, adjustments, replacement joinery, door hanging, fencing and small structural fixes with the same professionalism as larger contracts. Clear pricing, reliable attendance and tidy completion make us a practical choice for ongoing property care.', 'rh-base-child'),
		),
		array(
			'label'     => __('Extensions & loft conversions', 'rh-base-child'),
			'slug'      => 'extensions-loft-conversions',
			'card_slug' => 'extensions',
			'bento'     => 'g',
			'sectors'   => array('new-builds', 'refurbishment'),
			'intro'     => __('Structural carpentry and build packages for single and two-storey extensions, loft conversions and garage conversions.', 'rh-base-child'),
			'body'      => __('We help homeowners add space with well-planned extensions and lofts: steelwork coordination, floor and roof structures, dormers, stairs and internal fit-out. We liaise with building control, keep neighbours in mind, and programme works to minimise disruption while you remain in the property where possible.', 'rh-base-child'),
		),
		array(
			'label'     => __('Bespoke joinery & fitted furniture', 'rh-base-child'),
			'slug'      => 'bespoke-joinery',
			'card_slug' => 'joinery',
			'bento'     => 'h',
			'sectors'   => array('refurbishment', 'hospitality'),
			'intro'     => __('Made-to-measure joinery — kitchens, wardrobes, staircases, panelling and commercial counters — designed and installed by experienced bench joiners.', 'rh-base-child'),
			'body'      => __('Our workshop and site teams produce bespoke pieces that fit your space exactly. We advise on materials, hardware and finishes, produce setting-out drawings where needed, and install with care for adjacent finishes. From one-off stair details to full kitchen schemes, craftsmanship is consistent and snagging is kept low.', 'rh-base-child'),
		),
		array(
			'label'     => __('Commercial fit-out & shopfitting', 'rh-base-child'),
			'slug'      => 'commercial-fit-out',
			'card_slug' => 'commercial',
			'bento'     => 'i',
			'sectors'   => array('hospitality', 'education'),
			'intro'     => __('Interior carpentry and fit-out for retail, hospitality, education and office environments — programmed to your opening date.', 'rh-base-child'),
			'body'      => __('Commercial clients need certainty on programme and compliance. We deliver partitions, counters, seating, storage and feature joinery to drawing, coordinating with M&E and other fit-out trades. Examples in our portfolio include schools, cafes and community buildings across Essex.', 'rh-base-child'),
		),
		array(
			'label'     => __('Fire door installation, maintenance & inspection', 'rh-base-child'),
			'slug'      => 'fire-door-installation',
			'card_slug' => 'fire-doors',
			'bento'     => 'j',
			'sectors'   => array('refurbishment', 'education', 'hospitality'),
			'intro'     => __('Certified fire door installation, inspection and maintenance for residential blocks, commercial premises and public buildings.', 'rh-base-child'),
			'body'      => __('Fire safety legislation places clear duties on building owners. Our team includes UK Fire Door Training approved installers, inspectors and maintainers. We supply and hang certified doorsets, check gaps, seals and closers, and provide documentation suitable for your fire risk assessment. Ask us for a survey or to join your planned maintenance regime.', 'rh-base-child'),
		),
	);
}

/**
 * Find one service by URL slug.
 *
 * @return array{label: string, slug: string, card_slug: string, bento: string, sectors: string[], intro: string, body: string}|null
 */
function rh_carpentry_service_by_slug(string $slug): ?array {
	$slug = sanitize_title($slug);
	foreach (rh_carpentry_services() as $service) {
		if ($service['slug'] === $slug) {
			return $service;
		}
	}
	return null;
}

/**
 * Permalink for a service landing page (page child of Services hub).
 */
function rh_carpentry_service_url(string $slug): string {
	$page = rh_carpentry_get_page_by_path('services/' . sanitize_title($slug));
	if ($page instanceof WP_Post) {
		return (string) get_permalink($page);
	}
	return trailingslashit(home_url('/services/' . sanitize_title($slug) . '/'));
}

