<?php
/**
 * Testimonials carousel band (homepage + landing pages).
 *
 * @package RH_Base_Child
 *
 * @var array<int, array{quote: string, name: string, role: string, company: string}> $testimonials
 * @var string $cta_contact
 * @var string $heading_id
 */

if (! defined('ABSPATH')) {
	exit;
}

$testimonials = isset($testimonials) && is_array($testimonials) ? $testimonials : (function_exists('rh_carpentry_testimonials') ? rh_carpentry_testimonials() : array());
if ($testimonials === array()) {
	return;
}

$cta_contact = isset($cta_contact) ? (string) $cta_contact : rh_carpentry_contact_url();
$heading_id  = isset($heading_id) ? (string) $heading_id : 'rh-home-testimonials-heading';
if ($heading_id === '') {
	$heading_id = 'rh-home-testimonials-heading';
}

$testimonial_count = count($testimonials);
?>
<section class="rh-home-section rh-home-section--testimonials" aria-labelledby="<?php echo esc_attr($heading_id); ?>">
	<div class="rh-clients-hero rh-testimonials-hero">
		<div class="rh-clients-hero__bg" aria-hidden="true"></div>
		<div class="rh-clients-hero__overlay" aria-hidden="true"></div>
		<div class="rh-clients-hero__inner">
			<header class="rh-home-section__header rh-home-section__header--clients rh-home-section__header--testimonials rh-home-section__header--row" data-rh-fx-group data-rh-fx-stagger="140">
				<div>
					<p class="rh-home-kicker" data-rh-fx="wipe" data-rh-fx-tone="light">
						<span class="rh-home-kicker__line" aria-hidden="true"></span>
						<?php esc_html_e('Customer feedback', 'rh-base-child'); ?>
					</p>
					<h2 class="rh-home-heading rh-home-heading--section" id="<?php echo esc_attr($heading_id); ?>" data-rh-fx="wipe" data-rh-fx-tone="light"><?php esc_html_e('Testimonials', 'rh-base-child'); ?></h2>
				</div>
				<a class="rh-hero-btn rh-hero-btn--muted" href="<?php echo esc_url($cta_contact); ?>" data-rh-fx="fade"><?php esc_html_e('Get in touch', 'rh-base-child'); ?></a>
			</header>
			<div
				class="rh-home-testimonials-carousel"
				data-rh-testimonials-carousel
				data-interval="5000"
				data-at-start="true"
				data-at-end="<?php echo esc_attr($testimonial_count <= 1 ? 'true' : 'false'); ?>"
				role="region"
				aria-roledescription="<?php echo esc_attr(__('Carousel', 'rh-base-child')); ?>"
				aria-label="<?php echo esc_attr(__('Customer testimonials', 'rh-base-child')); ?>"
			>
				<div class="rh-home-testimonials-carousel__viewport-shell">
					<div class="rh-home-testimonials-carousel__viewport" tabindex="0">
						<div class="rh-home-testimonials-carousel__track" role="list">
						<?php foreach ($testimonials as $ti => $testimonial) : ?>
							<article
								class="rh-home-testimonial rh-bento-cell<?php echo 0 === $ti ? ' is-active' : ''; ?>"
								id="<?php echo esc_attr($heading_id . '-slide-' . $ti); ?>"
								role="listitem"
								data-rh-testimonial-slide
								data-rh-testimonial-index="<?php echo (int) $ti; ?>"
								aria-label="<?php
								echo esc_attr(
									sprintf(
										/* translators: 1: current slide number, 2: total slides */
										__('Testimonial %1$d of %2$d', 'rh-base-child'),
										$ti + 1,
										$testimonial_count
									)
								);
								?>"
							>
								<blockquote class="rh-home-testimonial__quote">
									<p><?php echo esc_html($testimonial['quote']); ?></p>
								</blockquote>
								<footer class="rh-home-testimonial__footer">
									<p class="rh-home-testimonial__name"><?php echo esc_html($testimonial['name']); ?></p>
									<p class="rh-home-testimonial__meta">
										<?php
										printf(
											/* translators: 1: role, 2: company */
											esc_html__('%1$s, %2$s', 'rh-base-child'),
											esc_html($testimonial['role']),
											esc_html($testimonial['company'])
										);
										?>
									</p>
								</footer>
							</article>
						<?php endforeach; ?>
						</div>
					</div>
				</div>
				<div class="rh-home-testimonials-carousel__bottom-bar">
					<?php if ($testimonial_count > 1) : ?>
						<button
							type="button"
							class="rh-home-testimonials-carousel__pause"
							data-rh-testimonial-autoplay-toggle
							data-label-pause="<?php echo esc_attr(__('Pause automatic slideshow', 'rh-base-child')); ?>"
							data-label-play="<?php echo esc_attr(__('Play automatic slideshow', 'rh-base-child')); ?>"
							aria-pressed="false"
							aria-label="<?php echo esc_attr(__('Pause automatic slideshow', 'rh-base-child')); ?>"
						>
							<svg class="rh-home-testimonials-carousel__pause-svg" viewBox="0 0 40 40" aria-hidden="true" focusable="false">
								<circle class="rh-home-testimonials-carousel__pause-track" cx="20" cy="20" r="17" fill="none" stroke="rgba(255,255,255,0.22)" stroke-width="2" />
								<circle
									class="rh-home-testimonials-carousel__pause-progress"
									cx="20"
									cy="20"
									r="17"
									fill="none"
									stroke="rgba(255,255,255,0.92)"
									stroke-width="2"
									stroke-dasharray="106.814"
									stroke-dashoffset="106.814"
									stroke-linecap="round"
									transform="rotate(-90 20 20)"
								/>
							</svg>
							<span class="rh-home-testimonials-carousel__pause-icon-wrap">
								<i class="fa-solid fa-pause" aria-hidden="true"></i>
							</span>
						</button>
					<?php endif; ?>
					<div class="rh-home-testimonials-carousel__arrows">
						<button
							type="button"
							class="rh-home-testimonials-carousel__arrow"
							data-rh-testimonial-prev
							aria-label="<?php echo esc_attr(__('Previous testimonial', 'rh-base-child')); ?>"
						>
							<i class="fa-solid fa-chevron-left" aria-hidden="true"></i>
						</button>
						<button
							type="button"
							class="rh-home-testimonials-carousel__arrow"
							data-rh-testimonial-next
							aria-label="<?php echo esc_attr(__('Next testimonial', 'rh-base-child')); ?>"
						>
							<i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
