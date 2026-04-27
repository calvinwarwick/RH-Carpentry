<?php
/**
 * About page template.
 *
 * Template Name: About Page
 * Template Post Type: page
 *
 * @package RH_Base_Child
 */

get_header();
?>

<section class="rh-inner-hero rh-inner-hero--about">
	<div class="rh-container">
		<p class="rh-inner-hero__eyebrow"><?php esc_html_e('About RH Carpentry', 'rh-base-child'); ?></p>
		<h1 class="rh-inner-hero__title"><?php the_title(); ?></h1>
		<p class="rh-inner-hero__lede">
			<?php esc_html_e('A reliable carpentry and construction team delivering high-quality work across Essex and surrounding areas.', 'rh-base-child'); ?>
		</p>
	</div>
</section>

<div class="rh-inner-page rh-inner-page--about">
	<div class="rh-container">
		<div class="rh-inner-grid">
			<article class="rh-inner-card">
				<h2><?php esc_html_e('Who we are', 'rh-base-child'); ?></h2>
				<p><?php esc_html_e('We combine practical site experience with strong project management to deliver tidy, safe and efficient work from first fix through to final handover.', 'rh-base-child'); ?></p>
				<p><?php esc_html_e('From one-off joinery packages to larger refurbishment and extension works, our team is focused on quality craftsmanship and clear communication.', 'rh-base-child'); ?></p>
			</article>

			<aside class="rh-inner-card rh-inner-card--checklist">
				<h2><?php esc_html_e('Why clients choose us', 'rh-base-child'); ?></h2>
				<ul>
					<li><?php esc_html_e('Experienced team for domestic and commercial carpentry', 'rh-base-child'); ?></li>
					<li><?php esc_html_e('Reliable scheduling and transparent updates', 'rh-base-child'); ?></li>
					<li><?php esc_html_e('Clean, safe and professional site standards', 'rh-base-child'); ?></li>
					<li><?php esc_html_e('Detail-focused finishing and snag-free handover', 'rh-base-child'); ?></li>
				</ul>
			</aside>
		</div>

		<section class="rh-inner-card rh-inner-card--content">
			<?php
			while (have_posts()) :
				the_post();
				if (trim((string) get_the_content()) !== '') :
					?>
					<div class="entry-content">
						<?php the_content(); ?>
					</div>
					<?php
				endif;
			endwhile;
			?>
		</section>
	</div>
</div>

<?php
get_footer();
