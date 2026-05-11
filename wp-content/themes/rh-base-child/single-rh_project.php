<?php
/**
 * Single project.
 *
 * @package RH_Base_Child
 */

get_header();

$current_project_id = 0;
$other_projects     = array();
?>

<div class="rh-single-project">
	<?php
	while (have_posts()) :
		the_post();
		$current_project_id = (int) get_the_ID();
		$slideshow_ids = function_exists('rh_project_get_slideshow_attachment_ids')
			? rh_project_get_slideshow_attachment_ids((int) get_the_ID())
			: array();
		$slideshow_ids = array_values(
			array_filter(
				$slideshow_ids,
				static function ($id) {
					return $id > 0 && wp_attachment_is_image((int) $id);
				}
			)
		);
		$terms = get_the_terms(get_the_ID(), 'rh_project_sector');
		ob_start();
		the_content();
		$content_html = ob_get_clean();
		$has_body      = trim(wp_strip_all_tags($content_html)) !== '';
		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class('rh-single-project__article'); ?>>
			<section
				class="rh-single-project__shell"
				aria-label="<?php echo esc_attr(sprintf(
					/* translators: %s: project title */
					__('Project: %s', 'rh-base-child'),
					get_the_title()
				)); ?>"
			>
				<div class="rh-clients-hero rh-testimonials-hero rh-single-project-hero">
					<div class="rh-clients-hero__bg" aria-hidden="true"></div>
					<div class="rh-clients-hero__overlay" aria-hidden="true"></div>
					<div class="rh-clients-hero__inner">
						<div class="rh-single-project__band rh-single-project__band--lead">
							<header class="rh-single-project__header">
								<?php
								if ($terms && ! is_wp_error($terms)) {
									$parts = array();
									foreach ($terms as $t) {
										$link = get_term_link($t);
										if (is_wp_error($link)) {
											continue;
										}
										$parts[] = '<a href="' . esc_url($link) . '">' . esc_html($t->name) . '</a>';
									}
									if ($parts !== array()) {
										echo '<p class="rh-single-project__sectors">';
										echo '<span class="rh-home-kicker__line" aria-hidden="true"></span>';
										echo '<span class="rh-single-project__sectors-text">' . implode(', ', $parts) . '</span></p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									}
								}
								?>
								<h1 class="rh-single-project__title"><?php the_title(); ?></h1>
							</header>

							<?php if ($slideshow_ids !== array()) : ?>
								<?php
								$n_slides = count($slideshow_ids);
								$multi    = $n_slides > 1;
								?>
								<div
									class="rh-home-projects-carousel rh-single-project-gallery"
									data-rh-projects-carousel
									data-interval="3000"
									data-at-start="true"
									data-at-end="<?php echo esc_attr($n_slides <= 1 ? 'true' : 'false'); ?>"
									role="region"
									aria-roledescription="<?php echo esc_attr__('Carousel', 'rh-base-child'); ?>"
									aria-label="<?php echo esc_attr(sprintf(
										/* translators: %s: project title */
										__('Photos: %s', 'rh-base-child'),
										get_the_title()
									)); ?>"
								>
									<div class="rh-home-projects-carousel__viewport" tabindex="0">
										<div class="rh-home-projects-carousel__track" role="list">
											<?php
											foreach ($slideshow_ids as $si => $att_id) {
												$att_id = (int) $att_id;
												$active = 0 === $si;
												$alt    = (string) get_post_meta($att_id, '_wp_attachment_image_alt', true);
												if ($alt === '') {
													$alt = get_the_title();
												}
												$slide_label = sprintf(
													/* translators: 1: image number, 2: total */
													__('Image %1$d of %2$d', 'rh-base-child'),
													$si + 1,
													$n_slides
												);
												printf(
													'<article class="rh-single-project-gallery__card rh-bento-cell%s" role="listitem" data-rh-project-slide data-rh-project-index="%d" aria-label="%s">',
													$active ? ' is-active' : '',
													$si,
													esc_attr($slide_label)
												);
												echo '<div class="rh-single-project-gallery__media">';
												echo wp_get_attachment_image(
													$att_id,
													'large',
													false,
													array(
														'class'    => 'rh-single-project-gallery__img',
														'loading'  => 0 === $si ? 'eager' : 'lazy',
														'decoding' => 'async',
														'sizes'    => '(max-width: 899px) 100vw, min(1600px, 96vw)',
														'alt'      => $alt,
													)
												);
												echo '</div><span class="rh-single-project-gallery__overlay" aria-hidden="true"></span></article>';
											}
											?>
										</div>
									</div>
									<div class="rh-home-projects-carousel__bottom-bar">
										<?php if ($multi) : ?>
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
														stroke-linecap="round"
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
								</div>
							<?php endif; ?>
						</div>

						<?php if ($has_body) : ?>
							<div class="rh-single-project__band rh-single-project__band--content">
								<div class="rh-single-project__content entry-content">
									<?php echo $content_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</div>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</section>
		</article>
	<?php endwhile; ?>
</div>

<?php
if ($current_project_id > 0 && function_exists('rh_project_get_other_projects')) {
	$other_projects = rh_project_get_other_projects($current_project_id);
}
if ($other_projects !== array()) :
	$other_count = count($other_projects);
	$other_multi = $other_count > 1;
	?>
	<section class="rh-single-related rh-single-related--band" aria-labelledby="rh-single-related-heading">
		<div class="rh-single-project__shell">
			<div class="rh-clients-hero rh-testimonials-hero rh-single-project-hero">
				<div class="rh-clients-hero__bg" aria-hidden="true"></div>
				<div class="rh-clients-hero__overlay" aria-hidden="true"></div>
				<div class="rh-clients-hero__inner">
					<header class="rh-single-related__intro">
						<p class="rh-home-kicker">
							<span class="rh-home-kicker__line" aria-hidden="true"></span>
							<?php esc_html_e('Browse more of our recent work', 'rh-base-child'); ?>
						</p>
						<h2 id="rh-single-related-heading" class="rh-home-heading rh-home-heading--section">
							<?php esc_html_e('More projects', 'rh-base-child'); ?>
						</h2>
					</header>
					<div
						class="rh-home-projects-carousel rh-single-related-carousel"
						data-rh-projects-carousel
						data-interval="5000"
						data-at-start="true"
						data-at-end="<?php echo esc_attr($other_count <= 1 ? 'true' : 'false'); ?>"
						role="region"
						aria-roledescription="<?php echo esc_attr__('Carousel', 'rh-base-child'); ?>"
						aria-label="<?php echo esc_attr__('Other projects', 'rh-base-child'); ?>"
					>
						<div class="rh-home-projects-carousel__viewport" tabindex="0">
							<div class="rh-home-projects-carousel__track" role="list">
								<?php
								foreach ($other_projects as $oi => $rel_post) {
									if (! $rel_post instanceof WP_Post) {
										continue;
									}
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
									$is_active = 0 === $oi;
									?>
									<article
										class="rh-home-project-card rh-bento-cell<?php echo $is_active ? ' is-active' : ''; ?>"
										id="<?php echo esc_attr('rh-single-related-project-' . $oi); ?>"
										role="listitem"
										data-rh-project-slide
										data-rh-project-index="<?php echo (int) $oi; ?>"
										data-rh-project-url="<?php echo esc_url(is_string($rel_url) ? $rel_url : ''); ?>"
										aria-label="<?php
										echo esc_attr(
											sprintf(
												/* translators: 1: project title, 2: slide number, 3: total slides */
												__('%1$s — project %2$d of %3$d', 'rh-base-child'),
												get_the_title($rel_post),
												$oi + 1,
												$other_count
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
						<div class="rh-home-projects-carousel__bottom-bar">
							<?php if ($other_multi) : ?>
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
											stroke-linecap="round"
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
					</div>
				</div>
			</div>
		</div>
	</section>
	<?php
endif;

get_footer();
