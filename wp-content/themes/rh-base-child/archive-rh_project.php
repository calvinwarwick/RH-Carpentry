<?php
/**
 * Projects archive.
 *
 * @package RH_Base_Child
 */

get_header();
?>

<div class="rh-archive-projects">
	<div class="rh-archive-projects__inner">
		<header class="rh-archive-projects__header">
			<p class="rh-home-kicker rh-archive-projects__kicker">
				<span class="rh-home-kicker__line" aria-hidden="true"></span>
				<?php esc_html_e('Portfolio', 'rh-base-child'); ?>
			</p>
			<h1 class="page-title rh-home-heading rh-home-heading--section">
				<?php post_type_archive_title(); ?>
			</h1>
			<p class="rh-archive-projects__intro">
				<?php esc_html_e('A selection of our carpentry and construction work across sectors.', 'rh-base-child'); ?>
			</p>
		</header>

		<?php if (have_posts()) : ?>
			<ul class="rh-archive-projects__grid">
				<?php
				while (have_posts()) :
					the_post();
					get_template_part('template-parts/content', 'rh_project');
				endwhile;
				?>
			</ul>
			<?php
			if (function_exists('rh_base_the_posts_navigation')) {
				rh_base_the_posts_navigation();
			} else {
				the_posts_pagination();
			}
			?>
		<?php else : ?>
			<p class="rh-archive-projects__empty"><?php esc_html_e('No projects published yet.', 'rh-base-child'); ?></p>
		<?php endif; ?>
	</div>
</div>

<?php
get_footer();
