<?php
/**
 * Services bento inner markup (homepage + overlay).
 *
 * @package RH_Base_Child
 */

if (! defined('ABSPATH')) {
	exit;
}

$cta_contact       = isset($cta_contact) ? $cta_contact : rh_carpentry_contact_url();
$home_services     = isset($home_services) && is_array($home_services) ? $home_services : rh_carpentry_services();
$heading_id        = isset($heading_id) ? $heading_id : 'rh-home-work-heading';
$show_landing_link = ! isset($show_landing_link) || $show_landing_link;
$show_header       = isset($show_header) ? (bool) $show_header : true;
$hide_cta_buttons  = isset($hide_cta_buttons) && $hide_cta_buttons;
?>
<div class="rh-home-section__inner">
	<?php if ($show_header) : ?>
		<header class="rh-home-section__header rh-home-section__header--features rh-home-section__header--row">
			<div>
				<p class="rh-home-kicker">
					<span class="rh-home-kicker__line" aria-hidden="true"></span>
					<?php esc_html_e('What we offer', 'rh-base-child'); ?>
				</p>
				<h2 class="rh-home-heading rh-home-heading--section" id="<?php echo esc_attr($heading_id); ?>"><?php esc_html_e('Services', 'rh-base-child'); ?></h2>
			</div>
			<?php if (! $hide_cta_buttons) : ?>
				<?php if ($show_landing_link && function_exists('rh_carpentry_services_landing_url')) : ?>
					<a class="rh-hero-btn rh-hero-btn--muted" href="<?php echo esc_url(rh_carpentry_services_landing_url()); ?>"><?php esc_html_e('All services', 'rh-base-child'); ?></a>
				<?php endif; ?>
				<a class="rh-hero-btn rh-hero-btn--accent" href="<?php echo esc_url($cta_contact); ?>"><?php esc_html_e('Get in touch', 'rh-base-child'); ?></a>
			<?php endif; ?>
		</header>
	<?php endif; ?>
	<div class="rh-home-features rh-home-features--services" role="list">
		<?php foreach ($home_services as $service) : ?>
			<?php
			$service_bg_url = rh_carpentry_get_service_card_image_url($service['card_slug']);
			$service_url    = rh_carpentry_service_url($service['slug']);
			?>
			<article class="rh-home-feature rh-home-feature--service rh-home-service-bento--<?php echo esc_attr($service['bento']); ?>" role="listitem">
				<a class="rh-home-service-card__link" href="<?php echo esc_url($service_url); ?>">
					<span class="screen-reader-text"><?php echo esc_html($service['label']); ?></span>
					<span class="rh-home-service-card__cta" aria-hidden="true">
						<?php esc_html_e('Find out more', 'rh-base-child'); ?>
						<i class="fa-solid fa-chevron-right rh-home-service-card__cta-icon" aria-hidden="true"></i>
					</span>
					<div class="rh-home-service-card__bg" style="background-image: url('<?php echo esc_url($service_bg_url); ?>');"></div>
					<div class="rh-home-service-card__overlay" aria-hidden="true"></div>
					<h3 class="rh-home-feature__title"><?php echo esc_html($service['label']); ?></h3>
				</a>
			</article>
		<?php endforeach; ?>
	</div>
</div>
