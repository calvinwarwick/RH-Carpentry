<?php
/**
 * Use a static page as the site front (Reading settings).
 *
 * @package RH_Base_Child
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
	exit;
}

/**
 * Ensure a published page exists (create if missing).
 *
 * @return int Page ID, or 0 on failure.
 */
function rh_carpentry_ensure_page(string $slug, string $title, string $template = ''): int {
	$page = get_page_by_path($slug, OBJECT, 'page');
	if ($page instanceof WP_Post) {
		if ($page->post_status !== 'publish') {
			wp_update_post(
				array(
					'ID'          => $page->ID,
					'post_status' => 'publish',
				)
			);
		}
		$page_id = (int) $page->ID;
	} else {
		$page_id = (int) wp_insert_post(
			array(
				'post_title'   => $title,
				'post_name'    => $slug,
				'post_status'  => 'publish',
				'post_type'    => 'page',
				'post_content' => '',
			),
			true
		);
		if ($page_id <= 0 || is_wp_error($page_id)) {
			return 0;
		}
	}

	if ($template !== '') {
		update_post_meta($page_id, '_wp_page_template', $template);
	}

	return $page_id;
}

/**
 * Ensure key top-level pages exist and are published.
 */
function rh_carpentry_ensure_core_pages(): void {
	rh_carpentry_ensure_page('about', __('About', 'rh-base-child'), 'page-about.php');
	rh_carpentry_ensure_page('services', __('Services', 'rh-base-child'), 'page-services.php');
}

/**
 * Ensure a published Home page exists and set it as the static front page.
 */
function rh_carpentry_apply_static_front_page(): void {
	$page_id = rh_carpentry_ensure_page('home', __('Home', 'rh-base-child'));
	if ($page_id <= 0) {
		return;
	}

	rh_carpentry_ensure_core_pages();

	update_option('show_on_front', 'page');
	update_option('page_on_front', $page_id);
}

/**
 * @param string       $new_stylesheet New theme stylesheet slug.
 * @param WP_Theme|false $old_theme      Previous theme object.
 */
function rh_carpentry_after_child_theme_activated(string $new_stylesheet, $old_theme): void {
	if ($new_stylesheet !== 'rh-base-child') {
		return;
	}

	if (get_option('show_on_front') === 'page') {
		$id = (int) get_option('page_on_front');
		if ($id > 0) {
			$post = get_post($id);
			if ($post instanceof WP_Post && $post->post_status === 'publish') {
				return;
			}
		}
	}

	rh_carpentry_apply_static_front_page();
}
add_action('after_switch_theme', 'rh_carpentry_after_child_theme_activated', 10, 2);

/**
 * One-time migration when the child theme is already active (e.g. after deploy).
 */
function rh_carpentry_maybe_static_home_admin_migrate(): void {
	if (wp_get_theme()->get_stylesheet() !== 'rh-base-child') {
		return;
	}

	if (! current_user_can('manage_options')) {
		return;
	}

	if (get_option('rh_carpentry_static_home_ready', false)) {
		return;
	}

	if (get_option('show_on_front') === 'page') {
		$id = (int) get_option('page_on_front');
		if ($id > 0) {
			$post = get_post($id);
			if ($post instanceof WP_Post && $post->post_status === 'publish') {
				rh_carpentry_ensure_core_pages();
				update_option('rh_carpentry_static_home_ready', true, true);
				return;
			}
		}
	}

	rh_carpentry_apply_static_front_page();
	update_option('rh_carpentry_static_home_ready', true, true);
}
add_action('admin_init', 'rh_carpentry_maybe_static_home_admin_migrate', 5);
