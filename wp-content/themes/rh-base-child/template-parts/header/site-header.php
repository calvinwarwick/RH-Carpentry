<?php
/**
 * Site header — compact hero-themed top bar (inner pages).
 *
 * @package RH_Base_Child
 */

if (! defined('ABSPATH')) {
	exit;
}

$hero_logo_path = get_stylesheet_directory() . '/assets/images/rh-logo-hero.png';
$hero_logo_url  = get_stylesheet_directory_uri() . '/assets/images/rh-logo-hero.png';
$hero_logo_ver  = file_exists($hero_logo_path) ? (string) filemtime($hero_logo_path) : wp_get_theme()->get('Version');
$hero_logo_src  = add_query_arg('v', $hero_logo_ver, $hero_logo_url);
?>
<header id="masthead" class="site-header rh-site-top-bar" role="banner">
	<div class="rh-site-top-bar__shell">
		<div class="rh-site-top-bar__bg" aria-hidden="true"></div>
		<div class="rh-site-top-bar__overlay" aria-hidden="true"></div>
		<div class="rh-site-top-bar__inner">
			<div class="rh-hero-header rh-site-top-bar__header">
				<a class="rh-hero-logo" href="<?php echo esc_url(home_url('/')); ?>">
					<img
						class="rh-hero-logo__img"
						src="<?php echo esc_url($hero_logo_src); ?>"
						width="598"
						height="127"
						alt="<?php echo esc_attr(get_bloginfo('name')); ?>"
						decoding="async"
					/>
				</a>

				<button
					type="button"
					class="rh-hero-nav-toggle"
					aria-controls="rh-site-top-nav"
					aria-expanded="false"
					data-rh-hero-nav-toggle
				>
					<span class="screen-reader-text"><?php esc_html_e('Menu', 'rh-base-child'); ?></span>
					<span class="rh-hero-nav-toggle__bar" aria-hidden="true"></span>
					<span class="rh-hero-nav-toggle__bar" aria-hidden="true"></span>
					<span class="rh-hero-nav-toggle__bar" aria-hidden="true"></span>
				</button>

				<nav
					class="rh-hero-nav"
					id="rh-site-top-nav"
					aria-label="<?php esc_attr_e('Primary', 'rh-base-child'); ?>"
					data-rh-hero-nav
				>
					<?php
					$rh_home_icon_item = sprintf(
						'<li class="menu-item rh-hero-nav__home-item"><a class="rh-hero-nav__home" href="%1$s"><span class="screen-reader-text">%2$s</span><i class="fa-solid fa-house" aria-hidden="true"></i></a></li>',
						esc_url(home_url('/')),
						esc_html__('Home', 'rh-base-child')
					);
					wp_nav_menu(
						array(
							'theme_location' => 'primary',
							'menu_id'        => 'rh-site-top-primary-menu',
							'container'      => false,
							'menu_class'     => 'rh-hero-nav__menu',
							'fallback_cb'    => 'rh_carpentry_site_top_fallback_menu',
							'items_wrap'     => '<ul id="%1$s" class="%2$s">' . $rh_home_icon_item . '%3$s</ul>',
						)
					);
					?>
				</nav>
			</div>
		</div>
	</div>
</header>
