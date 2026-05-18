<?php
/**
 * Inline contact section (hero photo + overlay wash, same form as contact dialog).
 *
 * @package RH_Base_Child
 */

if (! defined('ABSPATH')) {
	exit;
}

$heading_id = isset($heading_id) ? (string) $heading_id : 'rh-contact-band-heading';
$bg_url     = function_exists('rh_carpentry_get_hero_background_url') ? rh_carpentry_get_hero_background_url() : '';
?>
<section class="rh-contact-section-band" aria-labelledby="<?php echo esc_attr($heading_id); ?>">
		<div class="rh-hero-home rh-contact-section-band__hero">
			<?php if ($bg_url !== '') : ?>
				<div class="rh-hero-home__bg" style="background-image: url('<?php echo esc_url($bg_url); ?>');" aria-hidden="true"></div>
			<?php else : ?>
				<div class="rh-hero-home__bg rh-contact-section-band__bg--fallback" aria-hidden="true"></div>
			<?php endif; ?>
			<div class="rh-hero-home__overlay" aria-hidden="true"></div>
			<div class="rh-hero-home__inner rh-contact-section-band__inner">
				<div class="rh-contact-section-band__body">
					<p class="rh-hero-kicker rh-contact-overlay__kicker">
						<span class="rh-hero-kicker__line" aria-hidden="true"></span>
						<?php esc_html_e('Start your project now', 'rh-base-child'); ?>
					</p>
					<h2 class="rh-hero-title rh-contact-overlay__title" id="<?php echo esc_attr($heading_id); ?>">
						<?php esc_html_e('Get in touch', 'rh-base-child'); ?>
					</h2>
					<p class="rh-contact-overlay__intro">
						<?php esc_html_e('Tell us a little about your project and we will get back to you shortly.', 'rh-base-child'); ?>
					</p>
					<?php
					rh_include_template_part(
						'template-parts/seo/contact-form.php',
						array(
							'field_id_prefix' => 'rh-contact-band',
							'wrapper_class'   => 'rh-contact-section-band__form',
						)
					);
					?>
				</div>
			</div>
		</div>
</section>
