<?php
/**
 * Global site footer: hero logo + accreditations (left), get in touch (centre), map (right).
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

$footer_uri = get_stylesheet_directory_uri() . '/assets/images/footer/';
$hero_logo  = get_stylesheet_directory_uri() . '/assets/images/rh-logo-hero.png';
$ukfd_src   = function_exists( 'rh_carpentry_uk_fire_door_training_image_url' )
	? rh_carpentry_uk_fire_door_training_image_url()
	: $footer_uri . 'uk-fire-door-training.jpg';

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
			<div class="<?php echo esc_attr($footer_grid_class); ?>">
				<div class="rh-site-footer__col rh-site-footer__col--brand">
					<a class="rh-hero-logo rh-site-footer__hero-logo" href="<?php echo esc_url(home_url('/')); ?>">
						<img
							class="rh-hero-logo__img"
							src="<?php echo esc_url($hero_logo); ?>"
							width="598"
							height="129"
							alt="<?php echo esc_attr(get_bloginfo('name')); ?>"
							loading="lazy"
							decoding="async"
						/>
					</a>
					<div class="rh-site-footer__badges" aria-label="<?php esc_attr_e('Accreditations and memberships', 'rh-base-child'); ?>">
						<img src="<?php echo esc_url($footer_uri . 'citb.png'); ?>" width="3840" height="1450" alt="<?php echo esc_attr__('CITB', 'rh-base-child'); ?>" loading="lazy" decoding="async" />
						<img src="<?php echo esc_url($footer_uri . 'chas.png'); ?>" width="600" height="306" alt="<?php echo esc_attr__('CHAS — Accredited Contractor', 'rh-base-child'); ?>" loading="lazy" decoding="async" />
						<img src="<?php echo esc_url($footer_uri . 'fsb.png'); ?>" width="237" height="155" alt="<?php echo esc_attr__('FSB member', 'rh-base-child'); ?>" loading="lazy" decoding="async" />
					</div>
					<div class="rh-site-footer__uk-fire-door" role="group" aria-label="<?php esc_attr_e('UK Fire Door Training — approved installer accreditations', 'rh-base-child'); ?>">
						<div class="rh-site-footer__uk-fire-door-main-wrap">
							<img
								class="rh-site-footer__uk-fire-door-main"
								src="<?php echo esc_url($ukfd_src); ?>"
								width="1024"
								height="671"
								alt="<?php echo esc_attr__('UK Fire Door Training', 'rh-base-child'); ?>"
								loading="lazy"
								decoding="async"
							/>
						</div>
						<div class="rh-site-footer__uk-fire-door-badges">
							<div class="rh-ukfd-strip rh-ukfd-strip--1" aria-hidden="true">
								<img src="<?php echo esc_url($ukfd_src); ?>" width="1024" height="671" alt="" loading="lazy" decoding="async" />
							</div>
							<div class="rh-ukfd-strip rh-ukfd-strip--2" aria-hidden="true">
								<img src="<?php echo esc_url($ukfd_src); ?>" width="1024" height="671" alt="" loading="lazy" decoding="async" />
							</div>
							<div class="rh-ukfd-strip rh-ukfd-strip--3" aria-hidden="true">
								<img src="<?php echo esc_url($ukfd_src); ?>" width="1024" height="671" alt="" loading="lazy" decoding="async" />
							</div>
						</div>
					</div>
				</div>

				<?php if ($footer_has_contact) : ?>
					<div class="rh-site-footer__col rh-site-footer__col--contact">
						<p class="rh-home-kicker rh-site-footer__kicker">
							<span class="rh-home-kicker__line" aria-hidden="true"></span>
							<?php esc_html_e('Get in touch', 'rh-base-child'); ?>
						</p>
						<?php rh_carpentry_render_footer_contact_icons(); ?>
					</div>
				<?php endif; ?>

				<div class="rh-site-footer__col rh-site-footer__col--map">
					<p class="rh-home-kicker rh-site-footer__kicker">
						<span class="rh-home-kicker__line" aria-hidden="true"></span>
						<?php esc_html_e('Find us', 'rh-base-child'); ?>
					</p>
					<address class="rh-site-footer__address"><?php echo esc_html($footer_address); ?></address>
					<div class="rh-site-footer__map">
						<iframe
							title="<?php esc_attr_e('Map showing company location', 'rh-base-child'); ?>"
							loading="lazy"
							referrerpolicy="no-referrer-when-downgrade"
							src="<?php echo esc_url($map_src); ?>"
						></iframe>
					</div>
				</div>
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
