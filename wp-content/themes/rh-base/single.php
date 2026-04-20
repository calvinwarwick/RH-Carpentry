<?php
/**
 * Single post.
 *
 * @package RH_Base
 */

get_header();
?>

<div class="rh-container rh-container--narrow">
	<?php
	while (have_posts()) :
		the_post();
		get_template_part('template-parts/content', 'single');
	endwhile;
	?>
</div>

<?php
get_footer();
