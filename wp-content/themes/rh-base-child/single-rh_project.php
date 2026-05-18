<?php
/**
 * Single project.
 *
 * @package RH_Base_Child
 */

get_header();

$current_project_id = 0;
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
							<header class="rh-single-project__header" data-rh-fx-group data-rh-fx-stagger="160" data-rh-fx-base="500">
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
										echo '<p class="rh-single-project__sectors" data-rh-fx="wipe" data-rh-fx-tone="dark">';
										echo '<span class="rh-home-kicker__line" aria-hidden="true"></span>';
										echo '<span class="rh-single-project__sectors-text">' . implode(', ', $parts) . '</span></p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									}
								}
								?>
								<h1 class="rh-single-project__title" data-rh-fx="wipe" data-rh-fx-tone="dark"><?php the_title(); ?></h1>
							</header>

							<?php if ($slideshow_ids !== array()) : ?>
								<?php $n_slides = count($slideshow_ids); ?>
								<div
									class="rh-single-project-gallery rh-single-project-gallery--grid"
									data-count="<?php echo (int) $n_slides; ?>"
									data-rh-fx-group
									data-rh-fx-stagger="70"
									role="region"
									aria-label="<?php echo esc_attr(sprintf(
										/* translators: %s: project title */
										__('Photos: %s', 'rh-base-child'),
										get_the_title()
									)); ?>"
								>
									<?php
									foreach ($slideshow_ids as $si => $att_id) {
										$att_id = (int) $att_id;
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
											'<article class="rh-single-project-gallery__card rh-bento-cell" data-rh-project-index="%d" data-rh-fx="scale" aria-label="%s">',
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
												'loading'  => $si < 2 ? 'eager' : 'lazy',
												'decoding' => 'async',
												'sizes'    => '(max-width: 699px) 100vw, (max-width: 1099px) 50vw, 33vw',
												'alt'      => $alt,
											)
										);
										echo '</div></article>';
									}
									?>
								</div>
							<?php endif; ?>
						</div>

						<?php if ($has_body) : ?>
							<div class="rh-single-project__band rh-single-project__band--content">
								<div class="rh-single-project__content entry-content" data-rh-fx="fade">
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
if ($current_project_id > 0 && function_exists('rh_landing_render_projects_slider')) {
	$rh_slider_sectors = array();
	$rh_slider_terms   = get_the_terms($current_project_id, 'rh_project_sector');
	if ($rh_slider_terms && ! is_wp_error($rh_slider_terms)) {
		$rh_slider_sectors = wp_list_pluck($rh_slider_terms, 'slug');
	}
	rh_landing_render_projects_slider(
		$rh_slider_sectors,
		'rh-single-related-heading',
		$current_project_id,
		__('More projects', 'rh-base-child')
	);
}

if (function_exists('rh_landing_render_contact_band')) {
	rh_landing_render_contact_band();
}

get_footer();
