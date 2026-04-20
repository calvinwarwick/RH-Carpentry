<?php
/**
 * Theme setup.
 *
 * @package RH_Base
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
	exit;
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 */
function rh_base_setup(): void {
	load_theme_textdomain('rh-base', get_template_directory() . '/languages');

	global $content_width;
	if (! isset($content_width)) {
		$content_width = 1200;
	}

	add_theme_support('automatic-feed-links');
	add_theme_support('title-tag');
	add_theme_support('post-thumbnails');
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);
	add_theme_support('wp-block-styles');
	add_theme_support('align-wide');
	add_theme_support('responsive-embeds');
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 120,
			'width'       => 400,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);
	add_theme_support('customize-selective-refresh-widgets');
	add_theme_support('editor-styles');
	add_editor_style('build/main.css');
}
add_action('after_setup_theme', 'rh_base_setup');

/**
 * Register widget areas.
 */
function rh_base_widgets_init(): void {
	register_sidebar(
		array(
			'name'          => esc_html__('Sidebar', 'rh-base'),
			'id'            => 'sidebar-1',
			'description'   => esc_html__('Add widgets here.', 'rh-base'),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action('widgets_init', 'rh_base_widgets_init');
