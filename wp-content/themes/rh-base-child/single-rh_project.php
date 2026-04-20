<?php
/**
 * Single project.
 *
 * @package RH_Base_Child
 */

get_header();
?>

<div class="rh-single-project rh-container rh-container--narrow">
	<?php
	while (have_posts()) :
		the_post();
		$terms = get_the_terms(get_the_ID(), 'rh_project_sector');
		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class('rh-single-project__article'); ?>>
			<header class="rh-single-project__header">
				<?php
				if ($terms && ! is_wp_error($terms)) {
					$parts = array();
					foreach ($terms as $t) {
						$link = get_term_link($t);
						if (is_wp_error($link)) {
							continue;
						}
						$parts[] = '<a href="' . esc_url($link) . '">' . esc_html($t->name) . '</a>';
					}
					if ($parts !== array()) {
						echo '<p class="rh-single-project__sectors">' . implode(', ', $parts) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}
				}
				?>
				<h1 class="rh-single-project__title"><?php the_title(); ?></h1>
			</header>

			<?php if (has_post_thumbnail()) : ?>
				<div class="rh-single-project__hero">
					<?php the_post_thumbnail('large', array('class' => 'rh-single-project__image')); ?>
				</div>
			<?php endif; ?>

			<div class="rh-single-project__content entry-content">
				<?php the_content(); ?>
			</div>
		</article>
	<?php endwhile; ?>
</div>

<?php
get_footer();
