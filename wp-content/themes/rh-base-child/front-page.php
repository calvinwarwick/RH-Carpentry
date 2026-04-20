<?php
/**
 * Front page — hero in header; built-in sections + optional page content.
 *
 * @package RH_Base_Child
 */

get_header();

if (is_page()) {
	while (have_posts()) :
		the_post();
		get_template_part('template-parts/home/home-sections');
		$content = get_post_field('post_content', get_the_ID());
		if ($content !== '') {
			?>
			<section class="rh-home-section rh-home-section--editor" aria-label="<?php esc_attr_e('Page content', 'rh-base-child'); ?>">
				<div class="rh-home-section__inner rh-home-editor entry-content">
					<?php the_content(); ?>
				</div>
			</section>
			<?php
		}
	endwhile;
} else {
	?>
	<section class="rh-home-section rh-home-section--intro">
		<div class="rh-home-section__inner">
			<?php
			if (have_posts()) :
				while (have_posts()) :
					the_post();
					get_template_part('template-parts/content', get_post_type());
				endwhile;
				if (function_exists('rh_base_the_posts_navigation')) {
					rh_base_the_posts_navigation();
				} else {
					the_posts_pagination();
				}
			else :
				get_template_part('template-parts/content', 'none');
			endif;
			?>
		</div>
	</section>
	<?php
}

get_footer();
