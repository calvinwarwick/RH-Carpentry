<?php
/**
 * Global site footer: brand (left), map (centre), primary menu + social (end).
 *
 * @package RH_Base_Child
 */

if (! defined('ABSPATH')) {
	exit;
}

$footer_address = (string) apply_filters(
	'rh_carpentry_footer_address',
	'Bouverie, St. Mary\'s Road, Aingers Green, Gt Bentley, Colchester, Essex, CO7 8NN'
);

$map_query = (string) apply_filters(
	'rh_carpentry_footer_map_query',
	$footer_address
);
$map_src   = 'https://maps.google.com/maps?q=' . rawurlencode($map_query) . '&z=14&output=embed';

$footer_uri         = get_stylesheet_directory_uri() . '/assets/images/footer/';
$webcube_logo_path  = get_stylesheet_directory() . '/assets/images/webcube-logo.svg';
$webcube_logo_url   = get_stylesheet_directory_uri() . '/assets/images/webcube-logo.svg';
$webcube_logo_ver   = file_exists($webcube_logo_path) ? (string) filemtime($webcube_logo_path) : wp_get_theme()->get('Version');
$webcube_logo       = add_query_arg('v', $webcube_logo_ver, $webcube_logo_url);
$hero_logo_path = get_stylesheet_directory() . '/assets/images/rh-logo-hero.png';
$hero_logo_url  = get_stylesheet_directory_uri() . '/assets/images/rh-logo-hero.png';
$hero_logo_ver  = file_exists($hero_logo_path) ? (string) filemtime($hero_logo_path) : wp_get_theme()->get('Version');
$hero_logo      = add_query_arg('v', $hero_logo_ver, $hero_logo_url);

$footer_contact_items = function_exists('rh_carpentry_get_footer_contact_icon_items')
	? rh_carpentry_get_footer_contact_icon_items()
	: array();

$footer_has_contact = is_array($footer_contact_items) && $footer_contact_items !== array();
$footer_grid_class    = 'rh-site-footer__grid' . ( $footer_has_contact ? '' : ' rh-site-footer__grid--no-contact' );
?>
<footer id="colophon" class="rh-site-footer">
	<div class="rh-site-footer__surface">
		<div class="rh-site-footer__surface-bg" aria-hidden="true"></div>
		<div class="rh-site-footer__surface-overlay" aria-hidden="true"></div>
		<div class="rh-site-footer__surface-inner">
			<div class="<?php echo esc_attr($footer_grid_class); ?>" data-rh-fx-group data-rh-fx-stagger="100">
				<div class="rh-site-footer__col rh-site-footer__col--brand">
					<a class="rh-hero-logo rh-site-footer__hero-logo" href="<?php echo esc_url(home_url('/')); ?>" data-rh-fx="fade">
						<img
							class="rh-hero-logo__img"
							src="<?php echo esc_url($hero_logo); ?>"
							width="598"
							height="127"
							alt="<?php echo esc_attr(get_bloginfo('name')); ?>"
							loading="lazy"
							decoding="async"
						/>
					</a>
					<div class="rh-site-footer__badges" aria-label="<?php esc_attr_e('Accreditations and memberships', 'rh-base-child'); ?>" data-rh-fx="fade">
						<img src="<?php echo esc_url($footer_uri . 'citb.png'); ?>" width="3840" height="1450" alt="<?php echo esc_attr__('CITB', 'rh-base-child'); ?>" loading="lazy" decoding="async" />
						<img src="<?php echo esc_url($footer_uri . 'chas.png'); ?>" width="600" height="306" alt="<?php echo esc_attr__('CHAS — Accredited Contractor', 'rh-base-child'); ?>" loading="lazy" decoding="async" />
						<img src="<?php echo esc_url($footer_uri . 'fsb.png'); ?>" width="237" height="155" alt="<?php echo esc_attr__('FSB member', 'rh-base-child'); ?>" loading="lazy" decoding="async" />
					</div>
					<div class="webcube-link" data-rh-fx="fade">
						<a href="https://webcube.uk" target="_blank" rel="noopener noreferrer">
							<?php esc_html_e('made by', 'rh-base-child'); ?>
							<img
								class="webcube-logo"
								src="<?php echo esc_url($webcube_logo); ?>"
								width="26"
								height="25"
								alt="<?php echo esc_attr__('Webcube', 'rh-base-child'); ?>"
								loading="lazy"
								decoding="async"
							/>
							<?php esc_html_e('webcube', 'rh-base-child'); ?>
						</a>
					</div>
				</div>

				<div class="rh-site-footer__col rh-site-footer__col--map">
					<p class="rh-home-kicker rh-site-footer__kicker" data-rh-fx="wipe" data-rh-fx-tone="light">
						<span class="rh-home-kicker__line" aria-hidden="true"></span>
						<?php esc_html_e('Find us', 'rh-base-child'); ?>
					</p>
					<address class="rh-site-footer__address" data-rh-fx="wipe" data-rh-fx-tone="light"><?php echo esc_html($footer_address); ?></address>
					<div class="rh-site-footer__map" data-rh-fx="fade">
						<iframe
							title="<?php esc_attr_e('Map showing company location', 'rh-base-child'); ?>"
							loading="lazy"
							referrerpolicy="no-referrer-when-downgrade"
							src="<?php echo esc_url($map_src); ?>"
						></iframe>
					</div>
				</div>

				<?php if ($footer_has_contact) : ?>
					<div class="rh-site-footer__col rh-site-footer__col--contact">
						<nav class="rh-site-footer__primary-nav" aria-label="<?php esc_attr_e('Primary menu', 'rh-base-child'); ?>" data-rh-fx="fade">
							<?php
							if (has_nav_menu('primary')) {
								wp_nav_menu(
									array(
										'theme_location' => 'primary',
										'menu_id'        => 'rh-footer-primary-menu',
										'depth'          => 1,
										'container'      => false,
										'fallback_cb'    => false,
										'menu_class'     => 'rh-site-footer__menu rh-site-footer__menu--primary',
									)
								);
							} else {
								?>
								<ul id="rh-footer-primary-menu" class="rh-site-footer__menu rh-site-footer__menu--primary">
									<li><a href="<?php echo esc_url(rh_carpentry_about_page_url()); ?>"><?php esc_html_e('About', 'rh-base-child'); ?></a></li>
									<li><a href="<?php echo esc_url(rh_carpentry_services_hub_url()); ?>"><?php esc_html_e('Services', 'rh-base-child'); ?></a></li>
									<li><a href="<?php echo esc_url(rh_carpentry_projects_archive_url()); ?>"><?php esc_html_e('Projects', 'rh-base-child'); ?></a></li>
								</ul>
								<?php
							}
							?>
						</nav>
						<div class="rh-site-footer__social-row">
							<p class="rh-home-kicker rh-site-footer__kicker" data-rh-fx="wipe" data-rh-fx-tone="light">
								<span class="rh-home-kicker__line" aria-hidden="true"></span>
								<?php esc_html_e('Get in touch', 'rh-base-child'); ?>
							</p>
							<?php rh_carpentry_render_footer_contact_icons(); ?>
						</div>
					</div>
				<?php endif; ?>
			</div>

			<?php if (has_nav_menu('footer')) : ?>
				<div class="rh-site-footer__legal">
					<nav class="rh-site-footer__legal-nav" aria-label="<?php esc_attr_e('Legal and policies', 'rh-base-child'); ?>">
						<?php
						wp_nav_menu(
							array(
								'theme_location' => 'footer',
								'menu_id'        => 'rh-footer-legal-menu',
								'depth'          => 1,
								'container'      => false,
								'fallback_cb'    => false,
							)
						);
						?>
					</nav>
				</div>
			<?php endif; ?>
		</div>
	</div>
</footer>
