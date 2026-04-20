<?php
/**
 * Single post content.
 *
 * @package RH_Base
 */

if (! defined('ABSPATH')) {
	exit;
}
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<?php the_title('<h1 class="entry-title">', '</h1>'); ?>
		<div class="entry-meta">
			<?php rh_base_posted_on(); ?>
			<?php rh_base_categories(); ?>
		</div>
	</header>

	<?php if (has_post_thumbnail()) : ?>
		<div class="post-thumbnail"><?php the_post_thumbnail('large'); ?></div>
	<?php endif; ?>

	<div class="entry-content">
		<?php
		the_content();
		wp_link_pages(
			array(
				'before' => '<div class="page-links">' . esc_html__('Pages:', 'rh-base'),
				'after'  => '</div>',
			)
		);
		?>
	</div>

	<footer class="entry-footer">
		<?php
		$tags = get_the_tag_list('', esc_html__(', ', 'rh-base'));
		if ($tags) {
			printf('<span class="tags-links">%s</span>', $tags); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		?>
	</footer>
</article>

<?php
if (comments_open() || get_comments_number()) {
	comments_template();
}
