<?php
/**
 * Services section overlay (#services) — all pages.
 *
 * @package RH_Base_Child
 */

if (! defined('ABSPATH')) {
	exit;
}

$show_landing_link = ! is_page('services');
?>
<div
	class="rh-section-overlay rh-section-overlay--services"
	data-rh-section-overlay="services"
	role="dialog"
	aria-modal="true"
	aria-labelledby="rh-overlay-services-heading"
	aria-hidden="true"
>
	<div class="rh-section-overlay__backdrop" data-rh-section-overlay-close aria-hidden="true"></div>
	<div class="rh-section-overlay__panel rh-section-overlay__panel--wide">
		<header class="rh-section-overlay__bar">
			<button type="button" class="rh-section-overlay__close" data-rh-section-overlay-close aria-label="<?php esc_attr_e('Close', 'rh-base-child'); ?>">
				<span class="rh-section-overlay__close-icon" aria-hidden="true"></span>
			</button>
		</header>
		<div class="rh-section-overlay__scroll rh-home-section rh-home-section--features">
			<?php
			get_template_part(
				'template-parts/home/services-section-inner',
				null,
				array(
					'heading_id'        => 'rh-overlay-services-heading',
					'show_landing_link' => $show_landing_link,
				)
			);
			?>
		</div>
	</div>
</div>
