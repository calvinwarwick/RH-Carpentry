<?php
/**
 * Site footer.
 *
 * @package RH_Base
 */

if (! defined('ABSPATH')) {
	exit;
}
?>
<footer id="colophon" class="site-footer">
	<div class="rh-container site-footer__inner">
		<?php if (has_nav_menu('footer')) : ?>
			<nav class="footer-navigation" aria-label="<?php esc_attr_e('Footer', 'rh-base'); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'footer',
						'menu_id'        => 'footer-menu',
						'depth'          => 1,
						'container'      => false,
						'fallback_cb'    => false,
					)
				);
				?>
			</nav>
		<?php endif; ?>
		<p class="site-info">
			<?php
			printf(
				/* translators: 1: year, 2: site name */
				esc_html__('© %1$s %2$s', 'rh-base'),
				esc_html(gmdate('Y')),
				esc_html(get_bloginfo('name'))
			);
			?>
		</p>
	</div>
</footer>
