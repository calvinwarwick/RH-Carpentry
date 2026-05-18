<?php
/**
 * Projects carousel band (homepage / single project / service pages).
 *
 * @package RH_Base_Child
 *
 * @var WP_Post[] $projects     Project posts to show.
 * @var string    $kicker       Kicker above heading.
 * @var string    $title        Section heading.
 * @var string    $heading_id   Heading element id.
 * @var string    $archive_url  Optional “view all” URL.
 * @var string    $carousel_aria_label Carousel aria-label.
 */

if (! defined('ABSPATH')) {
	exit;
}

$projects = isset($projects) && is_array($projects) ? $projects : array();
$projects = array_values(
	array_filter(
		$projects,
		static function ($post) {
			return $post instanceof WP_Post && $post->post_type === 'rh_project';
		}
	)
);

if ($projects === array()) {
	return;
}

$kicker              = isset($kicker) ? (string) $kicker : __('Portfolio', 'rh-base-child');
$title               = isset($title) ? (string) $title : __('Projects', 'rh-base-child');
$heading_id          = isset($heading_id) ? (string) $heading_id : 'rh-projects-carousel-heading';
$carousel_aria_label = isset($carousel_aria_label) ? (string) $carousel_aria_label : __('Featured projects', 'rh-base-child');
$archive_url         = isset($archive_url) ? (string) $archive_url : '';

if ($archive_url === '' && function_exists('rh_carpentry_projects_archive_url')) {
	$archive_url = rh_carpentry_projects_archive_url();
}
if ($archive_url === '') {
	$archive_url = (string) get_post_type_archive_link('rh_project');
}
if ($archive_url === '') {
	$archive_url = home_url('/projects/');
}

$project_count = count($projects);
$project_multi = $project_count > 1;
$is_bento      = $project_count < 5;
$card_prefix   = sanitize_html_class($heading_id !== '' ? $heading_id : 'rh-projects-carousel');
?>
<section
	class="rh-single-related rh-single-related--band rh-projects-carousel-band<?php echo $is_bento ? ' rh-single-related--bento' : ''; ?>"
	data-count="<?php echo (int) $project_count; ?>"
	aria-labelledby="<?php echo esc_attr($heading_id); ?>"
>
	<div class="rh-single-project__shell">
		<div class="rh-clients-hero rh-testimonials-hero rh-single-project-hero">
			<div class="rh-clients-hero__bg" aria-hidden="true"></div>
			<div class="rh-clients-hero__overlay" aria-hidden="true"></div>
			<div class="rh-clients-hero__inner">
				<header class="rh-single-related__intro rh-home-section__header--row" data-rh-fx-group data-rh-fx-stagger="140">
					<div>
						<p class="rh-home-kicker" data-rh-fx="wipe" data-rh-fx-tone="light">
							<span class="rh-home-kicker__line" aria-hidden="true"></span>
							<?php echo esc_html($kicker); ?>
						</p>
						<h2 id="<?php echo esc_attr($heading_id); ?>" class="rh-home-heading rh-home-heading--section" data-rh-fx="wipe" data-rh-fx-tone="light">
							<?php echo esc_html($title); ?>
						</h2>
					</div>
					<a class="rh-hero-btn rh-hero-btn--muted rh-single-related__cta" href="<?php echo esc_url($archive_url); ?>" data-rh-fx="fade">
						<?php esc_html_e('View all projects', 'rh-base-child'); ?>
					</a>
				</header>
				<div
					class="rh-single-related__cards-fx"
					data-rh-fx-group
					data-rh-fx-stagger="82"
					data-rh-fx-base="1080"
				>
					<div
						class="rh-home-projects-carousel rh-single-related-carousel<?php echo $is_bento ? ' rh-home-projects-carousel--bento' : ''; ?>"
						<?php if (! $is_bento) : ?>
						data-rh-projects-carousel
						data-interval="5000"
						data-at-start="true"
						data-at-end="<?php echo esc_attr($project_count <= 1 ? 'true' : 'false'); ?>"
						<?php endif; ?>
						data-count="<?php echo (int) $project_count; ?>"
						role="region"
						<?php if (! $is_bento) : ?>
						aria-roledescription="<?php echo esc_attr__('Carousel', 'rh-base-child'); ?>"
						<?php endif; ?>
						aria-label="<?php echo esc_attr($carousel_aria_label); ?>"
					>
						<div class="rh-home-projects-carousel__viewport" tabindex="0">
							<div class="rh-home-projects-carousel__track" role="list">
								<?php
								foreach ($projects as $pi => $rel_post) {
									$rel_id    = (int) $rel_post->ID;
									$rel_url   = get_permalink($rel_post);
									$thumb_id  = (int) get_post_thumbnail_id($rel_id);
									$bg_url    = $thumb_id > 0 ? wp_get_attachment_image_url($thumb_id, 'large') : '';
									$rel_terms = get_the_terms($rel_id, 'rh_project_sector');
									$badges    = array();
									if ($rel_terms && ! is_wp_error($rel_terms)) {
										foreach (array_slice($rel_terms, 0, 4) as $t) {
											$badges[] = (string) $t->name;
										}
									}
									$is_active = $is_bento || 0 === $pi;
									?>
									<article
										class="rh-home-project-card rh-bento-cell<?php echo $is_active ? ' is-active' : ''; ?>"
										id="<?php echo esc_attr($card_prefix . '-project-' . $pi); ?>"
										role="listitem"
										data-rh-fx="scale"
										<?php if (! $is_bento) : ?>
										data-rh-project-slide
										data-rh-project-index="<?php echo (int) $pi; ?>"
										<?php endif; ?>
										data-rh-project-url="<?php echo esc_url(is_string($rel_url) ? $rel_url : ''); ?>"
										aria-label="<?php
										echo esc_attr(
											sprintf(
												/* translators: 1: project title, 2: slide number, 3: total slides */
												__('%1$s — project %2$d of %3$d', 'rh-base-child'),
												get_the_title($rel_post),
												$pi + 1,
												$project_count
											)
										);
										?>"
									>
										<span class="rh-home-project-card__cta" aria-hidden="true">
											<?php esc_html_e('Find out more', 'rh-base-child'); ?>
											<i class="fa-solid fa-chevron-right rh-home-project-card__cta-icon" aria-hidden="true"></i>
										</span>
										<?php if ($bg_url !== '' && $bg_url !== false) : ?>
											<span class="rh-home-project-card__bg" style="background-image: url('<?php echo esc_url($bg_url); ?>');"></span>
										<?php else : ?>
											<span class="rh-home-project-card__bg rh-home-project-card__bg--placeholder" aria-hidden="true"></span>
										<?php endif; ?>
										<span class="rh-home-project-card__overlay" aria-hidden="true"></span>
										<div class="rh-home-project-card__text">
											<span class="rh-home-project-card__title"><?php echo esc_html(get_the_title($rel_post)); ?></span>
											<?php if ($badges !== array()) : ?>
												<ul class="rh-home-project-card__badges">
													<?php foreach ($badges as $badge_label) : ?>
														<li>
															<span class="rh-home-project-card__badge"><?php echo esc_html($badge_label); ?></span>
														</li>
													<?php endforeach; ?>
												</ul>
											<?php endif; ?>
										</div>
									</article>
									<?php
								}
								?>
							</div>
						</div>
						<?php if (! $is_bento) : ?>
						<div class="rh-home-projects-carousel__bottom-bar">
							<?php if ($project_multi) : ?>
								<button
									type="button"
									class="rh-home-projects-carousel__pause"
									data-rh-project-autoplay-toggle
									data-label-pause="<?php echo esc_attr(__('Pause automatic slideshow', 'rh-base-child')); ?>"
									data-label-play="<?php echo esc_attr(__('Play automatic slideshow', 'rh-base-child')); ?>"
									aria-pressed="false"
									aria-label="<?php echo esc_attr(__('Pause automatic slideshow', 'rh-base-child')); ?>"
								>
									<svg class="rh-home-projects-carousel__pause-svg" viewBox="0 0 40 40" aria-hidden="true" focusable="false">
										<circle class="rh-home-projects-carousel__pause-track" cx="20" cy="20" r="17" fill="none" stroke="rgba(255,255,255,0.22)" stroke-width="2" />
										<circle
											class="rh-home-projects-carousel__pause-progress"
											cx="20"
											cy="20"
											r="17"
											fill="none"
											stroke="rgba(255,255,255,0.92)"
											stroke-width="2"
											stroke-dasharray="106.814"
											stroke-dashoffset="106.814"
											transform="rotate(-90 20 20)"
										/>
									</svg>
									<span class="rh-home-projects-carousel__pause-icon-wrap">
										<i class="fa-solid fa-pause" aria-hidden="true"></i>
									</span>
								</button>
							<?php endif; ?>
							<div class="rh-home-projects-carousel__arrows">
								<button
									type="button"
									class="rh-home-projects-carousel__arrow"
									data-rh-project-prev
									aria-label="<?php echo esc_attr(__('Previous project', 'rh-base-child')); ?>"
								>
									<i class="fa-solid fa-chevron-left" aria-hidden="true"></i>
								</button>
								<button
									type="button"
									class="rh-home-projects-carousel__arrow"
									data-rh-project-next
									aria-label="<?php echo esc_attr(__('Next project', 'rh-base-child')); ?>"
								>
									<i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
								</button>
							</div>
						</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
