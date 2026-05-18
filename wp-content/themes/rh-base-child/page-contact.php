<?php
/**
 * Contact page template.
 *
 * @package RH_Base_Child
 */

get_header();

$rh_hero = rh_landing_page_hero( 'contact' );
?>

<div class="rh-archive-projects rh-archive-projects--contact">
	<div class="rh-archive-projects__inner">
		<?php
		while ( have_posts() ) :
			the_post();
			if ( $rh_hero !== null ) {
				rh_landing_render_page_archive_header( $rh_hero );
			}
			?>
			<div class="rh-landing__content entry-content">
				<?php the_content(); ?>
			</div>
			<?php get_template_part( 'template-parts/seo/contact-form' ); ?>
			<?php
		endwhile;
		?>
	</div>
</div>

<?php
get_footer();
