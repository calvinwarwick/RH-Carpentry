<?php
/**
 * Site header / primary navigation.
 *
 * @package RH_Base
 */

if (! defined('ABSPATH')) {
	exit;
}
?>
<header id="masthead" class="site-header">
	<div class="rh-container site-header__inner">
		<div class="site-branding">
			<?php if (has_custom_logo()) : ?>
				<div class="site-logo"><?php the_custom_logo(); ?></div>
			<?php else : ?>
				<p class="site-title">
					<a href="<?php echo esc_url(home_url('/')); ?>" rel="home"><?php bloginfo('name'); ?></a>
				</p>
				<?php
				$description = get_bloginfo('description', 'display');
				if ($description || is_customize_preview()) :
					?>
					<p class="site-description"><?php echo esc_html($description); ?></p>
				<?php endif; ?>
			<?php endif; ?>
		</div>

		<button type="button" class="menu-toggle" aria-controls="primary-menu" aria-expanded="false" data-rh-base-menu-toggle>
			<span class="screen-reader-text"><?php esc_html_e('Menu', 'rh-base'); ?></span>
			<span class="menu-toggle__bar" aria-hidden="true"></span>
			<span class="menu-toggle__bar" aria-hidden="true"></span>
			<span class="menu-toggle__bar" aria-hidden="true"></span>
		</button>

		<nav id="site-navigation" class="main-navigation" aria-label="<?php esc_attr_e('Primary', 'rh-base'); ?>" data-rh-base-menu>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'menu_id'        => 'primary-menu',
					'container'      => false,
					'fallback_cb'    => false,
				)
			);
			?>
		</nav>
	</div>
</header>
