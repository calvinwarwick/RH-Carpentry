<?php
/**
 * About section overlay (#about) — all pages.
 *
 * @package RH_Base_Child
 */

if (! defined('ABSPATH')) {
	exit;
}

$rh_fire_credentials_dir  = get_stylesheet_directory() . '/assets/images/credentials/';
$rh_fire_credentials_base = get_stylesheet_directory_uri() . '/assets/images/credentials/';
$rh_fire_credentials       = array();
$rh_uk_fire_door_logo_id   = (int) get_theme_mod('rh_uk_fire_door_logo_id', 0);
$rh_uk_fire_door_alt       = __('UK Fire Door Training — Approved Installer, Inspector and Maintainer', 'rh-base-child');

if ($rh_uk_fire_door_logo_id > 0 && wp_attachment_is_image($rh_uk_fire_door_logo_id)) {
	$rh_fire_credentials[] = array(
		'attachment_id' => $rh_uk_fire_door_logo_id,
		'alt'           => $rh_uk_fire_door_alt,
	);
} elseif (is_readable($rh_fire_credentials_dir . 'uk-fire-door-training.png')) {
	$rh_fire_credentials[] = array(
		'file' => 'uk-fire-door-training.png',
		'alt'  => $rh_uk_fire_door_alt,
	);
}
if (is_readable($rh_fire_credentials_dir . 'firequal-logo.png')) {
	$rh_fire_credentials[] = array(
		'file' => 'firequal-logo.png',
		'alt'  => __('FireQual', 'rh-base-child'),
	);
}

$about_section_image_id = (int) get_theme_mod('rh_about_section_image_id', 0);
?>
<div
	class="rh-section-overlay rh-section-overlay--about"
	data-rh-section-overlay="about"
	role="dialog"
	aria-modal="true"
	aria-labelledby="rh-overlay-about-heading"
	aria-hidden="true"
>
	<div class="rh-section-overlay__backdrop" data-rh-section-overlay-close aria-hidden="true"></div>
	<div class="rh-section-overlay__panel">
		<header class="rh-section-overlay__bar">
			<button type="button" class="rh-section-overlay__close" data-rh-section-overlay-close aria-label="<?php esc_attr_e('Close', 'rh-base-child'); ?>">
				<span class="rh-section-overlay__close-icon" aria-hidden="true"></span>
			</button>
		</header>
		<div class="rh-section-overlay__scroll rh-home-section rh-home-section--about">
			<?php
			get_template_part(
				'template-parts/home/about-section-inner',
				null,
				array(
					'heading_id'             => 'rh-overlay-about-heading',
					'about_section_image_id' => $about_section_image_id,
					'rh_fire_credentials'    => $rh_fire_credentials,
					'rh_fire_credentials_dir'  => $rh_fire_credentials_dir,
					'rh_fire_credentials_base' => $rh_fire_credentials_base,
				)
			);
			?>
		</div>
	</div>
</div>
