<?php
/**
 * Services page template.
 *
 * Template Name: Services Page
 * Template Post Type: page
 *
 * @package RH_Base_Child
 */

get_header();
?>

<section class="rh-inner-hero rh-inner-hero--services">
	<div class="rh-container">
		<p class="rh-inner-hero__eyebrow"><?php esc_html_e('What we do', 'rh-base-child'); ?></p>
		<h1 class="rh-inner-hero__title"><?php the_title(); ?></h1>
		<p class="rh-inner-hero__lede">
			<?php esc_html_e('Comprehensive carpentry and construction services for residential and commercial projects.', 'rh-base-child'); ?>
		</p>
	</div>
</section>

<div class="rh-inner-page rh-inner-page--services">
	<div class="rh-container">
		<section class="rh-services-grid" aria-label="<?php esc_attr_e('Service list', 'rh-base-child'); ?>">
			<article class="rh-inner-card rh-service-card">
				<h2><?php esc_html_e('Structural Carpentry', 'rh-base-child'); ?></h2>
				<p><?php esc_html_e('First fix carpentry, timber framing, roof structures and all core structural timber works.', 'rh-base-child'); ?></p>
			</article>
			<article class="rh-inner-card rh-service-card">
				<h2><?php esc_html_e('Joinery & Interiors', 'rh-base-child'); ?></h2>
				<p><?php esc_html_e('Second fix, bespoke joinery, fitted furniture, doors, skirting and detailed interior timber finishes.', 'rh-base-child'); ?></p>
			</article>
			<article class="rh-inner-card rh-service-card">
				<h2><?php esc_html_e('Extensions & Refurbishment', 'rh-base-child'); ?></h2>
				<p><?php esc_html_e('End-to-end support for extensions, alterations and refurbishment packages with coordinated site delivery.', 'rh-base-child'); ?></p>
			</article>
			<article class="rh-inner-card rh-service-card">
				<h2><?php esc_html_e('Commercial Carpentry', 'rh-base-child'); ?></h2>
				<p><?php esc_html_e('Reliable teams for commercial fit-outs, maintenance and planned works across multiple project types.', 'rh-base-child'); ?></p>
			</article>
		</section>

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
