<?php
/**
 * Navigation menus.
 *
 * @package RH_Base
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
	exit;
}

/**
 * Register navigation menus.
 */
function rh_base_register_menus(): void {
	register_nav_menus(
		array(
			'primary' => esc_html__('Primary menu', 'rh-base'),
			'footer'  => esc_html__('Footer menu', 'rh-base'),
		)
	);
}
add_action('init', 'rh_base_register_menus');
