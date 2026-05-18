<?php
/**
 * Project sector taxonomy archive.
 *
 * @package RH_Base_Child
 */

get_header();

$term = get_queried_object();
$intro = $term instanceof WP_Term ? rh_seo_sector_intro($term) : '';
?>

<div class="rh-archive-projects rh-archive-projects--sector">
	<div class="rh-archive-projects__inner">
		<header class="rh-archive-projects__header">
			<p class="rh-home-kicker rh-archive-projects__kicker">
				<span class="rh-home-kicker__line" aria-hidden="true"></span>
				<?php esc_html_e('Portfolio', 'rh-base-child'); ?>
			</p>
			<h1 class="page-title rh-home-heading rh-home-heading--section">
				<?php
				if ($term instanceof WP_Term) {
					printf(
						/* translators: %s: sector name */
						esc_html__('%s projects', 'rh-base-child'),
						esc_html($term->name)
					);
				}
				?>
			</h1>
			<?php if ($intro !== '') : ?>
				<p class="rh-archive-projects__intro"><?php echo esc_html($intro); ?></p>
			<?php endif; ?>
		</header>

		<?php if (have_posts()) : ?>
			<div class="rh-archive-projects__grid rh-archive-projects__bento" role="list">
				<?php
				$rh_idx = 0;
				while (have_posts()) :
					the_post();
					if (function_exists('rh_project_render_bento_card')) {
						echo rh_project_render_bento_card(get_post(), $rh_idx); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}
					$rh_idx++;
				endwhile;
				?>
			</div>
			<?php
			the_posts_pagination(
				array(
					'mid_size'  => 1,
					'prev_text' => __('Previous', 'rh-base-child'),
					'next_text' => __('Next', 'rh-base-child'),
				)
			);
			?>
		<?php else : ?>
			<p class="rh-archive-projects__empty"><?php esc_html_e('No projects in this sector yet.', 'rh-base-child'); ?></p>
		<?php endif; ?>

		<p class="rh-archive-projects__back">
			<a href="<?php echo esc_url(rh_carpentry_projects_archive_url()); ?>"><?php esc_html_e('All projects', 'rh-base-child'); ?></a>
		</p>
	</div>
</div>

<?php
get_footer();
