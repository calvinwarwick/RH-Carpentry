<?php
/**
 * Projects archive.
 *
 * @package RH_Base_Child
 */

get_header();

global $wp_query;
$rh_total_pages = isset($wp_query->max_num_pages) ? (int) $wp_query->max_num_pages : 1;
$rh_total_found = isset($wp_query->found_posts) ? (int) $wp_query->found_posts : 0;
$rh_per_page    = function_exists('rh_project_archive_per_page') ? rh_project_archive_per_page() : 12;
$rh_rest_url    = rest_url('rh/v1/projects');
?>

<div class="rh-archive-projects">
	<div class="rh-archive-projects__inner">
		<header class="rh-archive-projects__header" data-rh-fx-group data-rh-fx-stagger="140" data-rh-fx-base="500">
			<p class="rh-home-kicker rh-archive-projects__kicker" data-rh-fx="wipe" data-rh-fx-tone="dark">
				<span class="rh-home-kicker__line" aria-hidden="true"></span>
				<?php esc_html_e('Portfolio', 'rh-base-child'); ?>
			</p>
			<h1 class="page-title rh-home-heading rh-home-heading--section" data-rh-fx="wipe" data-rh-fx-tone="dark">
				<?php post_type_archive_title(); ?>
			</h1>
			<p class="rh-archive-projects__intro" data-rh-fx="wipe" data-rh-fx-tone="dark">
				<?php esc_html_e('A selection of our carpentry and construction work across sectors.', 'rh-base-child'); ?>
			</p>
		</header>

		<?php if (have_posts()) : ?>
			<div
				class="rh-archive-projects__grid rh-archive-projects__bento"
				role="list"
				data-rh-archive-loader
				data-rh-fx-group
				data-rh-fx-stagger="70"
				data-page="1"
				data-total-pages="<?php echo (int) $rh_total_pages; ?>"
				data-per-page="<?php echo (int) $rh_per_page; ?>"
				data-rest-url="<?php echo esc_url($rh_rest_url); ?>"
				data-total-found="<?php echo (int) $rh_total_found; ?>"
			>
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

			<?php if ($rh_total_pages > 1) : ?>
				<div
					class="rh-archive-projects__sentinel"
					data-rh-archive-sentinel
					aria-hidden="true"
				></div>
				<div class="rh-archive-projects__status" data-rh-archive-status role="status" aria-live="polite">
					<span class="rh-archive-projects__spinner" aria-hidden="true"></span>
					<span class="rh-archive-projects__status-text">
						<?php esc_html_e('Loading more projects…', 'rh-base-child'); ?>
					</span>
				</div>
				<noscript>
					<nav class="rh-archive-projects__pagination" aria-label="<?php esc_attr_e('Projects pagination', 'rh-base-child'); ?>">
						<?php
						the_posts_pagination(
							array(
								'mid_size'  => 1,
								'prev_text' => __('Previous', 'rh-base-child'),
								'next_text' => __('Next', 'rh-base-child'),
							)
						);
						?>
					</nav>
				</noscript>
			<?php endif; ?>
		<?php else : ?>
			<p class="rh-archive-projects__empty"><?php esc_html_e('No projects published yet.', 'rh-base-child'); ?></p>
		<?php endif; ?>
	</div>
</div>

<?php
get_footer();
