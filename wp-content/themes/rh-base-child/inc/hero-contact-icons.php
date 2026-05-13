<?php
/**
 * Home hero “Get in touch” icon row (bottom-right aside).
 *
 * @package RH_Base_Child
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
	exit;
}

/**
 * Echo phone, Facebook, and Instagram icons (left-to-right in the row).
 *
 * @param string $extra_classes Optional extra classes on the wrapper (space-separated).
 */
function rh_carpentry_render_hero_contact_icons(string $extra_classes = ''): void {
	$phone_label = (string) get_theme_mod('rh_contact_phone', '');
	$phone_href  = rh_carpentry_tel_href_from_display($phone_label);

	$mobile_label = (string) get_theme_mod('rh_contact_mobile', '');
	$mobile_href  = rh_carpentry_tel_href_from_display($mobile_label);

	$facebook  = (string) get_theme_mod('rh_social_facebook', '');
	$instagram = (string) get_theme_mod('rh_social_instagram', '');
	if ($facebook === '') {
		$facebook = RH_CARPENTRY_DEFAULT_SOCIAL_FACEBOOK;
	}
	if ($instagram === '') {
		$instagram = RH_CARPENTRY_DEFAULT_SOCIAL_INSTAGRAM;
	}

	$phone_item = null;
	if ($phone_href !== '') {
		$phone_item = array(
			'href'     => $phone_href,
			'label'    => __('Phone', 'rh-base-child'),
			'icon'     => 'fa-solid fa-phone',
			'title'    => $phone_label,
			'external' => false,
		);
	}

	$mobile_item = null;
	if ($mobile_href !== '') {
		$mobile_item = array(
			'href'     => $mobile_href,
			'label'    => __('Mobile', 'rh-base-child'),
			'icon'     => 'fa-solid fa-mobile-screen',
			'title'    => $mobile_label,
			'external' => false,
		);
	}

	$fb_item = null;
	if ($facebook !== '') {
		$fb_item = array(
			'href'     => $facebook,
			'label'    => __('Facebook', 'rh-base-child'),
			'icon'     => 'fa-brands fa-facebook-f',
			'title'    => '',
			'external' => true,
		);
	}

	$ig_item = null;
	if ($instagram !== '') {
		$ig_item = array(
			'href'     => $instagram,
			'label'    => __('Instagram', 'rh-base-child'),
			'icon'     => 'fa-brands fa-instagram',
			'title'    => '',
			'external' => true,
		);
	}

	$items = array();
	$items[] = array(
		'href'     => rh_carpentry_home_section_url('contact'),
		'label'    => __('Open contact form', 'rh-base-child'),
		'icon'     => 'fa-solid fa-envelope',
		'title'    => '',
		'external' => false,
	);
	if ($phone_item) {
		$items[] = $phone_item;
	}
	if ($mobile_item) {
		$items[] = $mobile_item;
	}
	if ($fb_item) {
		$items[] = $fb_item;
	}
	if ($ig_item) {
		$items[] = $ig_item;
	}

	$classes = trim('rh-hero-social ' . $extra_classes);

	echo '<div class="' . esc_attr($classes) . '">';
	foreach ($items as $item) {
		$rel_target = $item['external'] ? ' rel="noopener noreferrer" target="_blank"' : '';
		$title_attr = ($item['title'] ?? '') !== '' ? ' title="' . esc_attr((string) $item['title']) . '"' : '';
		printf(
			'<a class="rh-hero-icon-btn" href="%s"%s%s><span class="screen-reader-text">%s</span><i class="%s" aria-hidden="true"></i></a>',
			esc_url($item['href']),
			$title_attr,
			$rel_target,
			esc_html($item['label']),
			esc_attr($item['icon'])
		);
	}
	echo '</div>';
}

/**
 * Footer contact row: same items as hero + LinkedIn (markup matches rh_carpentry_render_hero_contact_icons).
 *
 * @return array<int, array{href: string, icon: string, label: string, title: string, external: bool}>
 */
function rh_carpentry_get_footer_contact_icon_items(): array {
	$phone_label = (string) get_theme_mod('rh_contact_phone', '');
	$phone_href  = rh_carpentry_tel_href_from_display($phone_label);

	$mobile_label = (string) get_theme_mod('rh_contact_mobile', '');
	$mobile_href  = rh_carpentry_tel_href_from_display($mobile_label);

	$facebook  = (string) get_theme_mod('rh_social_facebook', '');
	$instagram = (string) get_theme_mod('rh_social_instagram', '');
	if ($facebook === '') {
		$facebook = RH_CARPENTRY_DEFAULT_SOCIAL_FACEBOOK;
	}
	if ($instagram === '') {
		$instagram = RH_CARPENTRY_DEFAULT_SOCIAL_INSTAGRAM;
	}
	$linkedin  = (string) get_theme_mod('rh_social_linkedin', '');

	$items = array();

	if ($phone_href !== '') {
		$items[] = array(
			'href'     => $phone_href,
			'icon'     => 'fa-solid fa-phone',
			'label'    => __('Phone', 'rh-base-child'),
			'title'    => $phone_label,
			'external' => false,
		);
	}

	if ($mobile_href !== '') {
		$items[] = array(
			'href'     => $mobile_href,
			'icon'     => 'fa-solid fa-mobile-screen',
			'label'    => __('Mobile', 'rh-base-child'),
			'title'    => $mobile_label,
			'external' => false,
		);
	}

	if ($facebook !== '') {
		$items[] = array(
			'href'     => $facebook,
			'icon'     => 'fa-brands fa-facebook-f',
			'label'    => __('Facebook', 'rh-base-child'),
			'title'    => '',
			'external' => true,
		);
	}

	if ($instagram !== '') {
		$items[] = array(
			'href'     => $instagram,
			'icon'     => 'fa-brands fa-instagram',
			'label'    => __('Instagram', 'rh-base-child'),
			'title'    => '',
			'external' => true,
		);
	}

	if ($linkedin !== '') {
		$items[] = array(
			'href'     => $linkedin,
			'icon'     => 'fa-brands fa-linkedin-in',
			'label'    => __('LinkedIn', 'rh-base-child'),
			'title'    => '',
			'external' => true,
		);
	}

	return $items;
}

/**
 * Footer: same icon buttons as the hero (no visible captions).
 */
function rh_carpentry_render_footer_contact_icons(): void {
	$items = rh_carpentry_get_footer_contact_icon_items();
	if ($items === array()) {
		return;
	}

	echo '<div class="rh-hero-social rh-site-footer__hero-social">';
	foreach ($items as $item) {
		$rel_target = $item['external'] ? ' rel="noopener noreferrer" target="_blank"' : '';
		$title_attr = ($item['title'] ?? '') !== '' ? ' title="' . esc_attr((string) $item['title']) . '"' : '';
		printf(
			'<a class="rh-hero-icon-btn" href="%s"%s%s><span class="screen-reader-text">%s</span><i class="%s" aria-hidden="true"></i></a>',
			esc_url($item['href']),
			$title_attr,
			$rel_target,
			esc_html($item['label']),
			esc_attr($item['icon'])
		);
	}
	echo '</div>';
}
