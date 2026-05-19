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
$rh_fire_credentials       = function_exists('rh_carpentry_fire_credentials') ? rh_carpentry_fire_credentials() : array();
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
