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
