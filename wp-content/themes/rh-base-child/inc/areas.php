<?php
/**
 * Service area landing page definitions.
 *
 * @package RH_Base_Child
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
	exit;
}

/**
 * Area pages for local SEO (unique intros — expand in WP admin as needed).
 *
 * @return array<int, array{slug: string, title: string, intro: string, body: string}>
 */
function rh_carpentry_area_pages(): array {
	return array(
		array(
			'slug'  => 'colchester',
			'title' => __('Carpentry & construction in Colchester', 'rh-base-child'),
			'intro' => __('Based near Great Bentley, we regularly work in Colchester and surrounding villages on new builds, refurbishments and commercial carpentry.', 'rh-base-child'),
			'body'  => __('From residential extensions to education and community projects, our teams know Colchester sites and local planning contexts. We coordinate with your architect or main contractor and keep programmes realistic. View our portfolio for Colchester-area work including commercial refurbishments, or contact us to discuss your project.', 'rh-base-child'),
		),
		array(
			'slug'  => 'clacton-on-sea',
			'title' => __('Carpenters in Clacton & the north Essex coast', 'rh-base-child'),
			'intro' => __('We undertake new build, refurbishment and maintenance work along the Tendring coast including Clacton, Frinton and nearby communities.', 'rh-base-child'),
			'body'  => __('Coastal properties need robust detailing and reliable attendance. Whether you are renovating a seaside home or delivering a small commercial fit-out, we provide carpentry and complete build packages with clear communication and tidy site standards.', 'rh-base-child'),
		),
		array(
			'slug'  => 'great-bentley',
			'title' => __('Builders & carpenters in Great Bentley', 'rh-base-child'),
			'intro' => __('R H Carpenters (UK) Ltd is headquartered in Great Bentley — local knowledge and fast response across the Tendring peninsula.', 'rh-base-child'),
			'body'  => __('Our yard and office are on Bouverie, St Mary\'s Road, Aingers Green. Clients in Great Bentley and neighbouring parishes benefit from short travel times and long-standing relationships with local suppliers. We welcome enquiries for domestic and contractor-led projects.', 'rh-base-child'),
		),
		array(
			'slug'  => 'tendring',
			'title' => __('Carpentry services in the Tendring district', 'rh-base-child'),
			'intro' => __('Serving the Tendring district with timber framing, roofing, refurbishments and fire door services.', 'rh-base-child'),
			'body'  => __('Tendring covers a wide mix of housing, agricultural buildings and coastal settlements. Our portfolio includes barn conversions, new dwellings and school works across the district. Tell us about your site and we will advise on scope and programme.', 'rh-base-child'),
		),
		array(
			'slug'  => 'essex',
			'title' => __('Essex carpentry & complete build packages', 'rh-base-child'),
			'intro' => __('Essex-wide carpentry and construction from an established team with over 40 years on site.', 'rh-base-child'),
			'body'  => __('While most of our work is in north Essex and the Suffolk border, we take on larger schemes further afield when the programme suits. Services include complete new builds, hand-cut roofs, commercial fit-out and certified fire door installation. Use our project archive to see the breadth of work we deliver.', 'rh-base-child'),
		),
		array(
			'slug'  => 'langham',
			'title' => __('Carpentry & new builds in Langham', 'rh-base-child'),
			'intro' => __('Local experience on bespoke homes and rural sites around Langham and north Essex.', 'rh-base-child'),
			'body'  => __('We have delivered new build carpentry packages in Langham and similar villages — timber frame, roofing and finishing joinery coordinated to a high standard. Contact us for quotes on extensions, new houses or barn conversions in the area.', 'rh-base-child'),
		),
		array(
			'slug'  => 'manningtree',
			'title' => __('Carpentry near Manningtree & the Stour valley', 'rh-base-child'),
			'intro' => __('Projects along the Essex–Suffolk border including Manningtree, Dedham and surrounding parishes.', 'rh-base-child'),
			'body'  => __('The Stour valley combines period properties and new development. We provide refurbishment, bespoke joinery and structural carpentry with respect for conservation settings where required.', 'rh-base-child'),
		),
		array(
			'slug'  => 'harwich',
			'title' => __('Construction & carpentry in Harwich', 'rh-base-child'),
			'intro' => __('Refurbishment, maintenance and fit-out for homes and businesses in Harwich and the Dovercourt area.', 'rh-base-child'),
			'body'  => __('Harwich projects often involve tight access and occupied buildings. We plan deliveries and working hours carefully and keep neighbours informed. Ask about our maintenance and commercial services as well as larger build packages.', 'rh-base-child'),
		),
	);
}

/**
 * Area page URL.
 */
function rh_carpentry_area_url(string $slug): string {
	return rh_carpentry_page_url('areas/' . sanitize_title($slug), 'areas');
}
