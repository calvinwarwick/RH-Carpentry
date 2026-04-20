<?php
/**
 * No results.
 *
 * @package RH_Base
 */

if (! defined('ABSPATH')) {
	exit;
}
?>

<section class="no-results not-found">
	<header class="page-header">
		<h1 class="page-title"><?php esc_html_e('Nothing here', 'rh-base'); ?></h1>
	</header>
	<div class="page-content">
		<?php if (is_home() && current_user_can('publish_posts')) : ?>
			<p>
				<?php
				printf(
					wp_kses(
						/* translators: URL to new post */
						__('Ready to publish? <a href="%1$s">Create a post</a>.', 'rh-base'),
						array(
							'a' => array(
								'href' => array(),
							),
						)
					),
					esc_url(admin_url('post-new.php'))
				);
				?>
			</p>
		<?php elseif (is_search()) : ?>
			<p><?php esc_html_e('Try different keywords.', 'rh-base'); ?></p>
			<?php get_search_form(); ?>
		<?php else : ?>
			<p><?php esc_html_e('We could not find anything. Try a search.', 'rh-base'); ?></p>
			<?php get_search_form(); ?>
		<?php endif; ?>
	</div>
</section>
