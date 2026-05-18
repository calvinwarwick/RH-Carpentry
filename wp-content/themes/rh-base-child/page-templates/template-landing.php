<?php
/**
 * Template Name: SEO landing page
 * Template Post Type: page
 *
 * @package RH_Base_Child
 */

get_header();

$rh_landing_post    = get_queried_object();
$rh_is_services_hub = $rh_landing_post instanceof WP_Post && $rh_landing_post->post_name === 'services';
$rh_is_about_page   = $rh_landing_post instanceof WP_Post && $rh_landing_post->post_name === 'about';
$rh_slug            = $rh_landing_post instanceof WP_Post ? (string) $rh_landing_post->post_name : '';
$rh_service         = is_string( $rh_slug ) && $rh_slug !== '' ? rh_carpentry_service_by_slug( $rh_slug ) : null;
$rh_hero            = $rh_service !== null ? rh_landing_service_hero( $rh_service ) : ( $rh_slug !== '' ? rh_landing_page_hero( $rh_slug ) : null );
$rh_archive_header  = $rh_hero !== null;
$rh_shell_class     = 'rh-archive-projects';
if ( $rh_is_services_hub ) {
	$rh_shell_class .= ' rh-archive-projects--services-hub';
}
if ( $rh_is_about_page ) {
	$rh_shell_class .= ' rh-archive-projects--about';
}
if ( $rh_service !== null ) {
	$rh_shell_class .= ' rh-archive-projects--service-single';
}
?>

<div class="<?php echo esc_attr( $rh_shell_class ); ?>">
	<?php
	while ( have_posts() ) :
		the_post();
		$slug = get_post_field( 'post_name', get_the_ID() );
		?>
		<div class="rh-archive-projects__inner">
			<?php if ( $rh_archive_header ) : ?>
				<?php rh_landing_render_page_archive_header( $rh_hero ); ?>
				<?php if ( trim( get_the_content() ) !== '' && ! $rh_is_services_hub && ! $rh_is_about_page ) : ?>
					<div class="rh-landing__content entry-content rh-landing__content--below-archive-header">
						<?php echo apply_filters( 'the_content', rh_landing_content_without_lede( (string) get_the_content() ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
				<?php endif; ?>
			<?php else : ?>
				<header class="rh-landing__header">
					<h1 class="rh-landing__title"><?php the_title(); ?></h1>
				</header>
				<div class="rh-landing__content entry-content">
					<?php the_content(); ?>
				</div>
			<?php endif; ?>

			<?php if ( $rh_is_services_hub ) : ?>
				<?php rh_landing_render_services_grid(); ?>
			<?php else : ?>
				<?php if ( is_string( $slug ) && $slug === 'areas' ) : ?>
					<?php rh_landing_render_areas_grid(); ?>
				<?php endif; ?>
				<?php if ( $rh_service === null && ! $rh_is_about_page ) : ?>
					<footer class="rh-landing__cta">
						<?php rh_landing_render_page_cta(); ?>
					</footer>
				<?php endif; ?>
			<?php endif; ?>
		</div>
		<?php if ( $rh_is_about_page ) : ?>
			<?php rh_landing_render_testimonials_section( 'rh-about-testimonials-heading' ); ?>
			<?php rh_landing_render_clients_section( 'rh-about-clients-heading' ); ?>
		<?php endif; ?>
		<?php if ( $rh_is_about_page || $rh_service !== null ) : ?>
			<?php rh_landing_render_contact_band(); ?>
		<?php endif; ?>
		<?php if ( $rh_is_about_page ) : ?>
			<?php rh_landing_render_projects_slider( array(), 'rh-about-projects-heading' ); ?>
		<?php elseif ( $rh_is_services_hub ) : ?>
			<?php rh_landing_render_projects_slider( array(), 'rh-services-projects-heading' ); ?>
		<?php elseif ( $rh_service !== null ) : ?>
			<?php rh_landing_render_projects_slider( $rh_service['sectors'], 'rh-service-projects-heading' ); ?>
		<?php endif; ?>
		<?php
	endwhile;
	?>
</div>

<?php
get_footer();
