<?php
/**
 * Client logo marquee (homepage + landing pages).
 *
 * @package RH_Base_Child
 *
 * @var array<int, array{url: string, alt: string}> $client_logos
 * @var float  $marquee_secs
 * @var string $cta_contact
 * @var string $heading_id
 */

if (! defined('ABSPATH')) {
	exit;
}

$client_logos = isset($client_logos) && is_array($client_logos) ? $client_logos : (function_exists('rh_carpentry_client_logos_items') ? rh_carpentry_client_logos_items() : array());
$cta_contact  = isset($cta_contact) ? (string) $cta_contact : rh_carpentry_contact_url();
$heading_id   = isset($heading_id) ? (string) $heading_id : 'rh-home-clients-heading';
if ($heading_id === '') {
	$heading_id = 'rh-home-clients-heading';
}

$marquee_secs = isset($marquee_secs) ? (float) $marquee_secs : 0;
if ($marquee_secs <= 0) {
	$marquee_secs = count($client_logos) > 0
		? max(48, min(220, count($client_logos) * 5.25))
		: 80;
}

if ($client_logos === array()) {
	if (! current_user_can('edit_theme_options')) {
		return;
	}
	?>
	<section class="rh-home-section rh-home-section--clients rh-home-section--clients-empty" aria-labelledby="<?php echo esc_attr($heading_id); ?>-empty">
		<div class="rh-home-clients-container">
			<header class="rh-home-section__header rh-home-section__header--clients rh-home-section__header--row" data-rh-fx-group data-rh-fx-stagger="140">
				<div>
					<p class="rh-home-kicker" data-rh-fx="wipe" data-rh-fx-tone="dark">
						<span class="rh-home-kicker__line" aria-hidden="true"></span>
						<?php esc_html_e('Over 1,000+ Happy customers', 'rh-base-child'); ?>
					</p>
					<h2 class="rh-home-heading rh-home-heading--section" id="<?php echo esc_attr($heading_id); ?>-empty" data-rh-fx="wipe" data-rh-fx-tone="dark"><?php esc_html_e('Our Clients', 'rh-base-child'); ?></h2>
				</div>
				<a class="rh-hero-btn rh-hero-btn--accent" href="<?php echo esc_url($cta_contact); ?>" data-rh-fx="fade"><?php esc_html_e('Get in touch', 'rh-base-child'); ?></a>
			</header>
			<div class="rh-client-marquee-panel">
				<p class="rh-client-marquee__hint">
					<?php esc_html_e('Add client logos under Appearance -> Customize -> Client logos (marquee).', 'rh-base-child'); ?>
				</p>
			</div>
		</div>
	</section>
	<?php
	return;
}
?>
<section class="rh-home-section rh-home-section--clients" aria-labelledby="<?php echo esc_attr($heading_id); ?>">
	<div class="rh-home-clients-container">
		<header class="rh-home-section__header rh-home-section__header--clients rh-home-section__header--row" data-rh-fx-group data-rh-fx-stagger="140">
			<div>
				<p class="rh-home-kicker" data-rh-fx="wipe" data-rh-fx-tone="dark">
					<span class="rh-home-kicker__line" aria-hidden="true"></span>
					<?php esc_html_e('Over 1,000+ Happy customers', 'rh-base-child'); ?>
				</p>
				<h2 class="rh-home-heading rh-home-heading--section" id="<?php echo esc_attr($heading_id); ?>" data-rh-fx="wipe" data-rh-fx-tone="dark"><?php esc_html_e('Our Clients', 'rh-base-child'); ?></h2>
			</div>
			<a class="rh-hero-btn rh-hero-btn--accent" href="<?php echo esc_url($cta_contact); ?>" data-rh-fx="fade"><?php esc_html_e('Get in touch', 'rh-base-child'); ?></a>
		</header>
		<div class="rh-client-marquee-panel" data-rh-fx="fade" data-rh-fx-tone="dark">
			<div class="rh-client-marquee">
				<div class="rh-client-marquee__mask">
					<div
						class="rh-client-marquee__track"
						style="<?php echo esc_attr('--rh-marquee-duration: ' . (string) round($marquee_secs, 1) . 's'); ?>"
					>
						<?php foreach (array(false, true) as $is_duplicate) : ?>
						<ul class="rh-client-marquee__row"<?php echo $is_duplicate ? ' aria-hidden="true"' : ''; ?>>
							<?php foreach ($client_logos as $logo) : ?>
							<li class="rh-client-marquee__item">
								<img
									class="rh-client-marquee__img"
									src="<?php echo esc_url($logo['url']); ?>"
									alt="<?php echo esc_attr($logo['alt']); ?>"
									loading="lazy"
									decoding="async"
									width="180"
									height="90"
								/>
							</li>
							<?php endforeach; ?>
						</ul>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
