<?php
/**
 * Search results.
 *
 * @package RH_Base
 */

get_header();
?>

<div class="rh-container">
	<?php if (have_posts()) : ?>
		<header class="page-header">
			<h1 class="page-title">
				<?php
				printf(
					/* translators: %s: search query */
					esc_html__('Search results for: %s', 'rh-base'),
					'<span>' . get_search_query() . '</span>'
				);
				?>
			</h1>
		</header>
		<?php
		while (have_posts()) :
			the_post();
			get_template_part('template-parts/content', 'search');
		endwhile;
		rh_base_the_posts_navigation();
	else :
		get_template_part('template-parts/content', 'none');
	endif;
	?>
</div>

<?php
get_footer();
