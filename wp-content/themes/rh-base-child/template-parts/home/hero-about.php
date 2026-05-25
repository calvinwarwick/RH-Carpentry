<?php
/**
 * About section — same structure as the home hero, distinct warm editorial styling.
 *
 * @package RH_Base_Child
 */

if (! defined('ABSPATH')) {
	exit;
}

$bg_url       = rh_carpentry_get_hero_background_url();
$cta_contact  = get_theme_mod('rh_cta_contact_url', '');
$cta_projects = get_theme_mod('rh_cta_projects_url', '');

if (! $cta_contact) {
	$cta_contact = home_url('/contact/');
}
if (! $cta_projects) {
	$cta_projects = home_url('/projects/');
}
?>

<div class="rh-hero-about">
	<div class="rh-hero-about__bg" style="background-image: url('<?php echo esc_url($bg_url); ?>');"></div>
	<div class="rh-hero-about__grain" aria-hidden="true"></div>
	<div class="rh-hero-about__overlay" aria-hidden="true"></div>

	<div class="rh-hero-about__inner">
		<header class="rh-hero-header rh-hero-about__masthead">
			<a class="rh-hero-logo" href="<?php echo esc_url(home_url('/')); ?>">
				<img
					class="rh-hero-logo__img rh-hero-about__logo"
					src="<?php echo esc_url(get_stylesheet_directory_uri() . '/assets/images/rh-logo-hero.png'); ?>"
					width="598"
					height="129"
					alt="<?php echo esc_attr(get_bloginfo('name')); ?>"
					decoding="async"
					loading="lazy"
				/>
			</a>

			<button type="button" class="rh-hero-nav-toggle" aria-controls="rh-about-menu" aria-expanded="false" data-rh-hero-nav-toggle>
				<span class="screen-reader-text"><?php esc_html_e('Menu', 'rh-base-child'); ?></span>
				<span class="rh-hero-nav-toggle__bar" aria-hidden="true"></span>
				<span class="rh-hero-nav-toggle__bar" aria-hidden="true"></span>
				<span class="rh-hero-nav-toggle__bar" aria-hidden="true"></span>
			</button>

			<nav class="rh-hero-nav" id="rh-about-menu" aria-label="<?php esc_attr_e('Primary', 'rh-base-child'); ?>" data-rh-hero-nav>
				<a class="rh-hero-nav__home" href="<?php echo esc_url(home_url('/')); ?>">
					<span class="screen-reader-text"><?php esc_html_e('Home', 'rh-base-child'); ?></span>
					<i class="fa-solid fa-house" aria-hidden="true"></i>
				</a>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'menu_id'        => 'rh-about-primary-menu',
						'container'      => false,
						'menu_class'     => 'rh-hero-nav__menu',
						'fallback_cb'    => 'rh_carpentry_about_fallback_menu',
					)
				);
				?>
			</nav>
		</header>

		<div class="rh-hero-body rh-hero-about__body">
			<div class="rh-hero-copy rh-hero-about__copy">
				<p class="rh-hero-about__eyebrow" id="rh-about-heading"><?php esc_html_e('About us', 'rh-base-child'); ?></p>
				<h2 class="rh-hero-about__title"><?php esc_html_e('Craft, care, and four decades on site.', 'rh-base-child'); ?></h2>
				<p class="rh-hero-lede rh-hero-about__lede">
					<?php esc_html_e('We are a close-knit team of joiners and site specialists who treat every project as a long-term partnership—from first sketch to the last fitted hinge.', 'rh-base-child'); ?>
				</p>
				<div class="rh-hero-actions">
					<a class="rh-hero-btn rh-hero-about__btn rh-hero-about__btn--outline" href="<?php echo esc_url($cta_contact); ?>"><?php esc_html_e('Get in touch', 'rh-base-child'); ?></a>
					<a class="rh-hero-btn rh-hero-about__btn rh-hero-about__btn--solid" href="<?php echo esc_url($cta_projects); ?>"><?php esc_html_e('View projects', 'rh-base-child'); ?></a>
				</div>
			</div>

			<aside class="rh-hero-aside rh-hero-about__aside" aria-label="<?php esc_attr_e('Get in touch', 'rh-base-child'); ?>">
				<p class="rh-hero-kicker rh-hero-kicker--right rh-hero-about__aside-kicker">
					<span class="rh-hero-kicker__line" aria-hidden="true"></span>
					<?php esc_html_e('Get in touch', 'rh-base-child'); ?>
				</p>
				<?php rh_carpentry_render_hero_contact_icons('rh-hero-about__social'); ?>
			</aside>
		</div>
	</div>
</div>
