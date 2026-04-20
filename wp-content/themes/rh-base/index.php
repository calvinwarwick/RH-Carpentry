<?php
/**
 * Main template fallback.
 *
 * @package RH_Base
 */

get_header();
?>

<div class="rh-container">
	<?php if (have_posts()) : ?>
		<header class="page-header">
			<h1 class="page-title"><?php esc_html_e('Posts', 'rh-base'); ?></h1>
		</header>
		<?php
		while (have_posts()) :
			the_post();
			get_template_part('template-parts/content', get_post_type());
		endwhile;
		rh_base_the_posts_navigation();
	else :
		get_template_part('template-parts/content', 'none');
	endif;
	?>
</div>

<?php
get_footer();
