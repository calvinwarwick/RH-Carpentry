<?php
/**
 * Comments template.
 *
 * @package RH_Base
 */

if (! defined('ABSPATH')) {
	exit;
}

if (post_password_required()) {
	return;
}
?>

<div id="comments" class="comments-area">
	<?php if (have_comments()) : ?>
		<h2 class="comments-title">
			<?php
			$count = get_comments_number();
			if ('1' === $count) {
				esc_html_e('One comment', 'rh-base');
			} else {
				printf(
					/* translators: %s: comment count */
					esc_html(_n('%s comment', '%s comments', (int) $count, 'rh-base')),
					number_format_i18n($count)
				);
			}
			?>
		</h2>

		<ol class="comment-list">
			<?php
			wp_list_comments(
				array(
					'style'      => 'ol',
					'short_ping' => true,
				)
			);
			?>
		</ol>

		<?php the_comments_navigation(); ?>
	<?php endif; ?>

	<?php if (! comments_open() && get_comments_number() && post_type_supports(get_post_type(), 'comments')) : ?>
		<p class="no-comments"><?php esc_html_e('Comments are closed.', 'rh-base'); ?></p>
	<?php endif; ?>

	<?php comment_form(); ?>
</div>
