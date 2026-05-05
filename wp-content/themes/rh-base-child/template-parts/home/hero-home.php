<?php
/**
 * Full-viewport home hero (RH Carpentry design).
 *
 * @package RH_Base_Child
 */

if (! defined('ABSPATH')) {
	exit;
}

$bg_url         = rh_carpentry_get_hero_background_url();
$cta_contact    = rh_carpentry_home_section_url('contact');
$cta_projects   = rh_carpentry_home_section_url('projects');
$hero_logo_path = get_stylesheet_directory() . '/assets/images/rh-logo-hero.png';
$hero_logo_url  = get_stylesheet_directory_uri() . '/assets/images/rh-logo-hero.png';
$hero_logo_ver  = file_exists($hero_logo_path) ? (string) filemtime($hero_logo_path) : wp_get_theme()->get('Version');

$hero_title = (string) get_theme_mod('rh_hero_title', '');
$hero_lede  = (string) get_theme_mod('rh_hero_lede', '');
?>

<div class="rh-hero-home">
	<div class="rh-hero-home__bg" style="background-image: url('<?php echo esc_url($bg_url); ?>');"></div>
	<div class="rh-hero-home__overlay" aria-hidden="true"></div>

	<div class="rh-hero-home__inner">
		<header class="rh-hero-header">
			<a class="rh-hero-logo" href="<?php echo esc_url(home_url('/')); ?>">
				<img
					class="rh-hero-logo__img"
					src="<?php echo esc_url(add_query_arg('v', $hero_logo_ver, $hero_logo_url)); ?>"
					width="598"
					height="127"
					alt="<?php echo esc_attr(get_bloginfo('name')); ?>"
					decoding="async"
					fetchpriority="high"
				/>
			</a>

			<button type="button" class="rh-hero-nav-toggle" aria-controls="rh-hero-menu" aria-expanded="false" data-rh-hero-nav-toggle>
				<span class="screen-reader-text"><?php esc_html_e('Menu', 'rh-base-child'); ?></span>
				<span class="rh-hero-nav-toggle__bar" aria-hidden="true"></span>
				<span class="rh-hero-nav-toggle__bar" aria-hidden="true"></span>
				<span class="rh-hero-nav-toggle__bar" aria-hidden="true"></span>
			</button>

			<nav class="rh-hero-nav" id="rh-hero-menu" aria-label="<?php esc_attr_e('Primary', 'rh-base-child'); ?>" data-rh-hero-nav>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'menu_id'        => 'rh-hero-primary-menu',
						'container'      => false,
						'menu_class'     => 'rh-hero-nav__menu',
						'fallback_cb'    => 'rh_carpentry_hero_fallback_menu',
					)
				);
				?>
			</nav>
		</header>

		<div class="rh-hero-body">
			<div class="rh-hero-copy rh-hero-bento-panel">
				<div class="rh-hero-bento-panel__inner">
					<p class="rh-hero-kicker">
						<span class="rh-hero-kicker__line" aria-hidden="true"></span>
						<?php esc_html_e('Bespoke solutions', 'rh-base-child'); ?>
					</p>
					<h1 class="rh-hero-title"><?php echo $hero_title !== '' ? esc_html($hero_title) : esc_html__('Built right.', 'rh-base-child'); ?></h1>
					<p class="rh-hero-lede">
						<?php
						if ($hero_lede !== '') {
							echo esc_html($hero_lede);
						} else {
							esc_html_e('Full carpentry, complete build packages, and project management—from a team with decades on site.', 'rh-base-child');
						}
						?>
					</p>
					<div class="rh-hero-actions">
						<a class="rh-hero-btn rh-hero-btn--muted" href="<?php echo esc_url($cta_contact); ?>"><?php esc_html_e('Get in touch', 'rh-base-child'); ?></a>
						<a class="rh-hero-btn rh-hero-btn--accent" href="<?php echo esc_url($cta_projects); ?>"><?php esc_html_e('Our Projects', 'rh-base-child'); ?></a>
					</div>
				</div>
			</div>

			<aside class="rh-hero-aside rh-hero-bento-panel" aria-label="<?php esc_attr_e('Get in touch', 'rh-base-child'); ?>">
				<div class="rh-hero-bento-panel__inner rh-hero-bento-panel__inner--aside">
					<p class="rh-hero-kicker rh-hero-kicker--right">
						<span class="rh-hero-kicker__line" aria-hidden="true"></span>
						<?php esc_html_e('Get in touch', 'rh-base-child'); ?>
					</p>
					<?php rh_carpentry_render_hero_contact_icons(); ?>
				</div>
			</aside>
		</div>
	</div>
</div>
