<?php
/**
 * Insights blog post type for long-tail SEO content.
 *
 * @package RH_Base_Child
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
	exit;
}

/**
 * Register insights CPT.
 */
function rh_insight_register(): void {
	register_post_type(
		'rh_insight',
		array(
			'labels'       => array(
				'name'          => __('Insights', 'rh-base-child'),
				'singular_name' => __('Insight', 'rh-base-child'),
				'add_new_item'  => __('Add new article', 'rh-base-child'),
			),
			'public'       => true,
			'has_archive'  => 'insights',
			'menu_icon'    => 'dashicons-welcome-write-blog',
			'supports'     => array('title', 'editor', 'excerpt', 'thumbnail', 'revisions'),
			'show_in_rest' => true,
			'rewrite'      => array(
				'slug'       => 'insights',
				'with_front' => false,
			),
		)
	);
}
add_action('init', 'rh_insight_register', 5);

/**
 * Seed starter articles once.
 */
function rh_insight_seed_posts(): void {
	if (get_option('rh_insights_seeded') === '1') {
		return;
	}

	$posts = array(
		array(
			'slug'    => 'complete-build-package-essex',
			'title'   => __('What does a complete build package include?', 'rh-base-child'),
			'excerpt' => __('An overview of full build packages from R H Carpenters — scope, trades and what to expect on site in Essex.', 'rh-base-child'),
			'content' => rh_carpentry_page_content_html(
				array(
					__('A complete build package brings carpentry, structure and finishing trades under one coordinated programme. For homeowners and developers in Essex, that means fewer gaps between trades and a single team accountable for quality on site.', 'rh-base-child'),
					__('Typical scope includes setting out, timber frame or traditional structure, roofing, windows and doors, first and second fix carpentry, kitchens and decorating coordination. We agree milestones with you before work starts and keep communication clear throughout.', 'rh-base-child'),
					__('View our new build projects or contact us to discuss your drawings and programme.', 'rh-base-child'),
				)
			),
		),
		array(
			'slug'    => 'barn-conversion-planning-essex',
			'title'   => __('Barn conversion planning in Essex — what to expect', 'rh-base-child'),
			'excerpt' => __('Planning and build considerations for barn conversions in Essex, from structural surveys to weather-tight handover.', 'rh-base-child'),
			'content' => rh_carpentry_page_content_html(
				array(
					__('Barn conversions combine heritage structures with modern living standards. In Essex you may need planning permission, listed building consent or prior approval depending on the barn and location.', 'rh-base-child'),
					__('We work with your architect and structural engineer to stabilise frames, insulate and line walls, and deliver joinery that respects the character of the building. Our portfolio includes barn conversions across north Essex — see Holts Court and Kirby-le-Soken for examples.', 'rh-base-child'),
				)
			),
		),
		array(
			'slug'    => 'fire-door-standards-commercial',
			'title'   => __('Fire door installation standards for commercial buildings', 'rh-base-child'),
			'excerpt' => __('Why certified fire door installation matters and how RH Carpentry supports compliance in Essex.', 'rh-base-child'),
			'content' => rh_carpentry_page_content_html(
				array(
					__('Fire doors are life-safety components. Building owners must ensure doorsets are correctly specified, installed and maintained. Our team includes UK Fire Door Training approved installers and inspectors.', 'rh-base-child'),
					__('We check gaps, seals, closers and signage, and provide records suitable for your fire risk assessment. Contact us for a survey or to join a planned maintenance programme.', 'rh-base-child'),
				)
			),
		),
	);

	foreach ($posts as $row) {
		$existing = get_page_by_path($row['slug'], OBJECT, 'rh_insight');
		if ($existing instanceof WP_Post) {
			continue;
		}
		wp_insert_post(
			array(
				'post_name'    => $row['slug'],
				'post_title'   => $row['title'],
				'post_excerpt' => $row['excerpt'],
				'post_content' => $row['content'],
				'post_status'  => 'publish',
				'post_type'    => 'rh_insight',
			)
		);
	}

	update_option('rh_insights_seeded', '1', true);
}
add_action('admin_init', 'rh_insight_seed_posts', 25);
