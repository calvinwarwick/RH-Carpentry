<?php
/**
 * Single insight article.
 *
 * @package RH_Base_Child
 */

get_header();
?>

<div class="rh-archive-projects rh-archive-projects--insight-single">
	<div class="rh-archive-projects__inner">
		<?php
		while ( have_posts() ) :
			the_post();
			$rh_hero = array(
				'kicker'   => __( 'Insights', 'rh-base-child' ),
				'title'    => get_the_title(),
				'subtitle' => get_the_date(),
				'intro'    => '',
			);
			?>
			<article>
				<?php rh_landing_render_page_archive_header( $rh_hero ); ?>
				<div class="rh-landing__content entry-content">
					<?php the_content(); ?>
				</div>
			</article>
			<footer class="rh-landing__cta">
				<a class="rh-hero-btn rh-hero-btn--accent" href="<?php echo esc_url( rh_carpentry_contact_url() ); ?>"><?php esc_html_e( 'Get in touch', 'rh-base-child' ); ?></a>
			</footer>
			<?php
		endwhile;
		?>
	</div>
</div>

<?php
get_footer();
