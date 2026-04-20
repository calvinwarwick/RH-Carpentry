<?php
/**
 * Page template.
 *
 * @package RH_Base
 */

get_header();
?>

<div class="rh-container">
	<?php
	while (have_posts()) :
		the_post();
		get_template_part('template-parts/content', 'page');
	endwhile;
	?>
</div>

<?php
get_footer();
