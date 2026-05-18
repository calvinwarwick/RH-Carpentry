<?php
/**
 * Insights archive.
 *
 * @package RH_Base_Child
 */

get_header();

$rh_hero = array(
	'kicker'   => __( 'Insights', 'rh-base-child' ),
	'title'    => post_type_archive_title( '', false ),
	'subtitle' => __( 'Guides and articles on carpentry, construction and fire safety in Essex.', 'rh-base-child' ),
	'intro'    => '',
);
?>

<div class="rh-archive-projects rh-archive-projects--insights">
	<div class="rh-archive-projects__inner">
		<?php rh_landing_render_page_archive_header( $rh_hero ); ?>

		<?php if ( have_posts() ) : ?>
			<ul class="rh-landing-grid rh-landing-grid--insights" role="list">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<li class="rh-landing-grid__item">
						<article>
							<h2 class="rh-landing-grid__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
							<?php if ( has_excerpt() ) : ?>
								<p><?php echo esc_html( wp_strip_all_tags( get_the_excerpt() ) ); ?></p>
							<?php endif; ?>
						</article>
					</li>
					<?php
				endwhile;
				?>
			</ul>
			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<p><?php esc_html_e( 'No articles published yet.', 'rh-base-child' ); ?></p>
		<?php endif; ?>
	</div>
</div>

<?php
get_footer();
