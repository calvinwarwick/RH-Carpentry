<?php
/**
 * Customizer settings for RH Carpentry home hero.
 *
 * @package RH_Base_Child
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
	exit;
}

/** Default Facebook profile URL (Customizer + theme_mod fallback). */
const RH_CARPENTRY_DEFAULT_SOCIAL_FACEBOOK = 'https://www.facebook.com/profile.php?id=61587190835676#';

/** Default Instagram profile URL (Customizer + theme_mod fallback). */
const RH_CARPENTRY_DEFAULT_SOCIAL_INSTAGRAM = 'https://www.instagram.com/rhcarpentersukltd/';

/**
 * Register customizer controls.
 *
 * @param WP_Customize_Manager $wp_customize Customizer instance.
 */
function rh_carpentry_customize_register(WP_Customize_Manager $wp_customize): void {
	$wp_customize->add_section(
		'rh_carpentry_hero',
		array(
			'title'    => __('Home hero', 'rh-base-child'),
			'priority' => 35,
		)
	);

	$wp_customize->add_setting(
		'rh_hero_background_id',
		array(
			'default'           => 0,
			'sanitize_callback' => 'absint',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Media_Control(
			$wp_customize,
			'rh_hero_background_id',
			array(
				'label'     => __('Hero background image', 'rh-base-child'),
				'section'   => 'rh_carpentry_hero',
				'mime_type' => 'image',
			)
		)
	);

	$wp_customize->add_setting(
		'rh_contact_phone',
		array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'rh_contact_phone',
		array(
			'label'       => __('Phone number (display)', 'rh-base-child'),
			'description' => __('A tap-to-call link is built automatically from this number.', 'rh-base-child'),
			'section'     => 'rh_carpentry_hero',
			'type'        => 'text',
		)
	);

	$wp_customize->add_setting(
		'rh_contact_email',
		array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_email',
		)
	);

	$wp_customize->add_control(
		'rh_contact_email',
		array(
			'label'       => __('Email address (display)', 'rh-base-child'),
			'description' => __('Shown in the contact block; a mailto link is built automatically when this is a valid email.', 'rh-base-child'),
			'section'     => 'rh_carpentry_hero',
			'type'        => 'text',
		)
	);

	$wp_customize->add_setting(
		'rh_social_facebook',
		array(
			'default'           => RH_CARPENTRY_DEFAULT_SOCIAL_FACEBOOK,
			'sanitize_callback' => 'esc_url_raw',
		)
	);

	$wp_customize->add_control(
		'rh_social_facebook',
		array(
			'label'   => __('Facebook URL', 'rh-base-child'),
			'section' => 'rh_carpentry_hero',
			'type'    => 'url',
		)
	);

	$wp_customize->add_setting(
		'rh_social_instagram',
		array(
			'default'           => RH_CARPENTRY_DEFAULT_SOCIAL_INSTAGRAM,
			'sanitize_callback' => 'esc_url_raw',
		)
	);

	$wp_customize->add_control(
		'rh_social_instagram',
		array(
			'label'   => __('Instagram URL', 'rh-base-child'),
			'section' => 'rh_carpentry_hero',
			'type'    => 'url',
		)
	);

	$wp_customize->add_setting(
		'rh_social_linkedin',
		array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
		)
	);

	$wp_customize->add_control(
		'rh_social_linkedin',
		array(
			'label'   => __('LinkedIn URL', 'rh-base-child'),
			'section' => 'rh_carpentry_hero',
			'type'    => 'url',
		)
	);

	$wp_customize->add_setting(
		'rh_hero_title',
		array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'rh_hero_title',
		array(
			'label'   => __('Hero headline (optional)', 'rh-base-child'),
			'section' => 'rh_carpentry_hero',
			'type'    => 'text',
		)
	);

	$wp_customize->add_setting(
		'rh_hero_lede',
		array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_textarea_field',
		)
	);

	$wp_customize->add_control(
		'rh_hero_lede',
		array(
			'label'       => __('Hero introduction (optional)', 'rh-base-child'),
			'description' => __('Leave empty for the default paragraph.', 'rh-base-child'),
			'section'     => 'rh_carpentry_hero',
			'type'        => 'textarea',
		)
	);

	$wp_customize->add_section(
		'rh_carpentry_service_cards',
		array(
			'title'       => __('Service card images', 'rh-base-child'),
			'description' => __('Background photos for each card in the Services bento (What we offer). Optional files: child theme folder assets/images/services/ using timber.jpg, refurbishment.jpg, roofs.jpg, new-build.jpg, barn.jpg, maintenance.jpg, extensions.jpg, joinery.jpg, commercial.jpg, fire-doors.jpg (or .webp / .png).', 'rh-base-child'),
			'priority'    => 34,
		)
	);

	$service_card_labels = array(
		'timber'        => __('Timber framed buildings', 'rh-base-child'),
		'refurbishment' => __('Full refurbishment', 'rh-base-child'),
		'roofs'         => __('Hand cut & trussed roofs', 'rh-base-child'),
		'new_build'     => __('Complete new build projects', 'rh-base-child'),
		'barn'          => __('Barn conversions', 'rh-base-child'),
		'maintenance'   => __('General maintenance', 'rh-base-child'),
		'extensions'    => __('Extensions & loft conversions', 'rh-base-child'),
		'joinery'       => __('Bespoke joinery & fitted furniture', 'rh-base-child'),
		'commercial'    => __('Commercial fit-out & shopfitting', 'rh-base-child'),
		'fire_doors'    => __('Fire door installation, maintenance & inspection', 'rh-base-child'),
	);

	foreach ($service_card_labels as $mod_suffix => $card_label) {
		$key = 'rh_service_card_' . $mod_suffix . '_id';
		$wp_customize->add_setting(
			$key,
			array(
				'default'           => 0,
				'sanitize_callback' => 'absint',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Media_Control(
				$wp_customize,
				$key,
				array(
					'label'       => $card_label,
					'description' => __('Optional. Falls back to a matching file in assets/images/services/, then the hero image.', 'rh-base-child'),
					'section'     => 'rh_carpentry_service_cards',
					'mime_type'   => 'image',
				)
			)
		);
	}

	$wp_customize->add_section(
		'rh_carpentry_about_home',
		array(
			'title'       => __('Homepage about', 'rh-base-child'),
			'description' => __('Image shown beside the About us block on the front page.', 'rh-base-child'),
			'priority'    => 36,
		)
	);

	$wp_customize->add_setting(
		'rh_about_section_image_id',
		array(
			'default'           => 0,
			'sanitize_callback' => 'absint',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Media_Control(
			$wp_customize,
			'rh_about_section_image_id',
			array(
				'label'       => __('About section image', 'rh-base-child'),
				'description' => __('Optional. If not set, the hero background image is used.', 'rh-base-child'),
				'section'     => 'rh_carpentry_about_home',
				'mime_type'   => 'image',
			)
		)
	);

	$wp_customize->add_section(
		'rh_carpentry_clients',
		array(
			'title'       => __('Client logos (marquee)', 'rh-base-child'),
			'description' => __('Upload client or partner logos for a scrolling row on the homepage. If all slots are empty, the theme uses the client logos from the legacy About us page (rhcarpentersukltd.co.uk/about-us/).', 'rh-base-child'),
			'priority'    => 37,
		)
	);

	$client_logo_max = (int) apply_filters('rh_carpentry_client_logo_customizer_slots', 30);
	if ($client_logo_max < 1) {
		$client_logo_max = 30;
	}

	for ($i = 1; $i <= $client_logo_max; $i++) {
		$key = 'rh_client_logo_' . $i . '_id';
		$wp_customize->add_setting(
			$key,
			array(
				'default'           => 0,
				'sanitize_callback' => 'absint',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Media_Control(
				$wp_customize,
				$key,
				array(
					/* translators: %d: logo slot number */
					'label'     => sprintf(__('Client logo %d', 'rh-base-child'), $i),
					'section'   => 'rh_carpentry_clients',
					'mime_type' => 'image',
				)
			)
		);
	}
}
add_action('customize_register', 'rh_carpentry_customize_register');

/**
 * Hero background URL: Media Library attachment (Customizer), else bundled theme JPEG.
 *
 * Default image is imported into Media on first request via inc/hero-media.php.
 */
function rh_carpentry_get_hero_background_url(): string {
	$id = (int) get_theme_mod('rh_hero_background_id', 0);
	if ($id > 0) {
		$url = wp_get_attachment_image_url($id, 'full');
		if ($url) {
			return $url;
		}
	}

	return get_stylesheet_directory_uri() . '/assets/images/hero-default.jpg';
}

/**
 * About section image URL: Customizer attachment, else same fallback as hero.
 */
function rh_carpentry_get_about_section_image_url(): string {
	$id = (int) get_theme_mod('rh_about_section_image_id', 0);
	if ($id > 0) {
		$url = wp_get_attachment_image_url($id, 'large');
		if ($url) {
			return $url;
		}
	}

	return rh_carpentry_get_hero_background_url();
}

/**
 * Service bento card background URL: Customizer attachment per slug, else theme file, else hero image.
 *
 * @param string $slug File basename without extension (e.g. timber, new-build). Maps to theme mod rh_service_card_{suffix}_id where hyphens become underscores.
 */
function rh_carpentry_get_service_card_image_url(string $slug): string {
	$slug = strtolower(preg_replace('/[^a-z0-9-]/', '', $slug));
	if ($slug === '') {
		return rh_carpentry_get_hero_background_url();
	}

	$mod_suffix = str_replace('-', '_', $slug);
	$id         = (int) get_theme_mod('rh_service_card_' . $mod_suffix . '_id', 0);
	if ($id > 0) {
		$url = wp_get_attachment_image_url($id, 'large');
		if ($url) {
			return $url;
		}
	}

	$dir = get_stylesheet_directory() . '/assets/images/services/';
	$uri = get_stylesheet_directory_uri() . '/assets/images/services/';
	foreach (array('webp', 'jpg', 'jpeg', 'png') as $ext) {
		$file = $dir . $slug . '.' . $ext;
		if (is_readable($file)) {
			return $uri . $slug . '.' . $ext;
		}
	}

	return rh_carpentry_get_hero_background_url();
}
