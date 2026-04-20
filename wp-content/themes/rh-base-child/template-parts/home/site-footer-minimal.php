<?php
/**
 * Minimal footer on the home frame.
 *
 * @package RH_Base_Child
 */

if (! defined('ABSPATH')) {
	exit;
}
?>

<footer class="rh-hero-footer">
	<div class="rh-hero-footer__inner">
		<p class="rh-hero-footer__copy">
			<?php
			printf(
				/* translators: 1: year, 2: site name */
				esc_html__('© %1$s %2$s', 'rh-base-child'),
				esc_html(gmdate('Y')),
				esc_html(get_bloginfo('name'))
			);
			?>
		</p>
	</div>
</footer>
