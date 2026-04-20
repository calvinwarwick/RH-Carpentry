<?php
/**
 * Template Name: With interactive (demo island)
 * Template Post Type: page
 *
 * Demonstrates loading a second JS bundle only on this template.
 *
 * @package RH_Base
 */

get_header();
?>

<div class="rh-container rh-container--narrow">
	<?php
	while (have_posts()) :
		the_post();
		get_template_part('template-parts/content', 'page');
	endwhile;
	?>
	<p class="rh-demo-island">
		<button type="button" class="rh-button rh-button--ghost" data-rh-base-demo><?php esc_html_e('Toggle demo state', 'rh-base'); ?></button>
		<span class="rh-demo-island__status" data-rh-base-demo-status aria-live="polite"></span>
	</p>
</div>

<?php
get_footer();
