<?php
/**
 * About section inner markup (homepage + overlay).
 *
 * @package RH_Base_Child
 */

if (! defined('ABSPATH')) {
	exit;
}

$about_section_image_id = isset($about_section_image_id) ? (int) $about_section_image_id : (int) get_theme_mod('rh_about_section_image_id', 0);
$heading_id             = isset($heading_id) ? $heading_id : 'rh-home-about-heading';
$rh_fire_credentials_dir  = get_stylesheet_directory() . '/assets/images/credentials/';
$rh_fire_credentials_base = get_stylesheet_directory_uri() . '/assets/images/credentials/';
$rh_fire_credentials    = isset($rh_fire_credentials) && is_array($rh_fire_credentials) && $rh_fire_credentials !== array()
	? $rh_fire_credentials
	: (function_exists('rh_carpentry_fire_credentials') ? rh_carpentry_fire_credentials() : array());
$about_page_url         = function_exists('rh_carpentry_about_page_url') ? rh_carpentry_about_page_url() : home_url('/about/');
$rh_about_hero          = function_exists('rh_landing_page_hero') ? rh_landing_page_hero('about') : null;
$rh_about_subtitle      = is_array($rh_about_hero) ? (string) ($rh_about_hero['subtitle'] ?? '') : '';
if ($rh_about_subtitle === '') {
	$rh_about_subtitle = __('Over 40 years delivering carpentry and complete build packages across Essex and East Anglia.', 'rh-base-child');
}
?>
<div class="rh-home-about-container">
	<div class="rh-home-about__grid">
		<div class="rh-home-about__text-card">
			<div
				class="rh-home-about__text-content"
				data-rh-fx-group
				data-rh-fx-stagger="120"
				data-rh-fx-base="0"
			>
				<header class="rh-home-section__header rh-home-section__header--about">
					<p class="rh-home-kicker" data-rh-fx="wipe" data-rh-fx-tone="dark">
						<span class="rh-home-kicker__line" aria-hidden="true"></span>
						<?php esc_html_e('Who we are', 'rh-base-child'); ?>
					</p>
					<h2 class="rh-home-heading rh-home-heading--section" id="<?php echo esc_attr($heading_id); ?>" data-rh-fx="wipe" data-rh-fx-tone="dark"><?php esc_html_e('About us', 'rh-base-child'); ?></h2>
					<p class="rh-archive-projects__subtitle rh-home-about__subtitle" data-rh-fx="wipe" data-rh-fx-tone="dark">
						<?php echo esc_html($rh_about_subtitle); ?>
					</p>
				</header>
				<div class="rh-home-about__body">
					<p class="rh-home-lede" data-rh-fx="fade" data-rh-fx-tone="dark">
						<?php esc_html_e('We work closely with homeowners, developers and contractors to deliver reliable workmanship, attention to detail and projects completed to a high professional standard.', 'rh-base-child'); ?>
					</p>
				</div>
				<div class="rh-home-about__actions rh-hero-actions">
					<a class="rh-hero-btn rh-hero-btn--accent" href="<?php echo esc_url($about_page_url); ?>" data-rh-fx="fade" data-rh-fx-tone="dark"><?php esc_html_e('Find out more', 'rh-base-child'); ?></a>
				</div>
				<?php if ($rh_fire_credentials !== array()) : ?>
					<div class="rh-home-about__credentials-panel" role="region" aria-label="<?php esc_attr_e('Fire door accreditations', 'rh-base-child'); ?>" data-rh-fx="fade" data-rh-fx-tone="dark">
						<ul class="rh-home-feature__credentials-list">
							<?php foreach ($rh_fire_credentials as $cred_index => $cred) : ?>
								<?php
								$cred_src = '';
								$cred_alt = isset($cred['alt']) ? (string) $cred['alt'] : '';
								if (! empty($cred['attachment_id']) && wp_attachment_is_image((int) $cred['attachment_id'])) {
									$aid      = (int) $cred['attachment_id'];
									$cred_src = (string) wp_get_attachment_image_url($aid, 'full');
									$lib_alt  = (string) get_post_meta($aid, '_wp_attachment_image_alt', true);
									if ($lib_alt !== '') {
										$cred_alt = $lib_alt;
									}
								} elseif (! empty($cred['file'])) {
									$cred_path = $rh_fire_credentials_dir . $cred['file'];
									$cred_url  = $rh_fire_credentials_base . $cred['file'];
									$cred_ver  = file_exists($cred_path) ? (string) filemtime($cred_path) : '';
									$cred_src  = $cred_ver !== '' ? add_query_arg('v', $cred_ver, $cred_url) : $cred_url;
								}
								if ($cred_src === '') {
									continue;
								}
								?>
								<li class="rh-home-feature__credentials-item<?php echo $cred_index === 0 ? ' rh-home-feature__credentials-item--uk-fire-door' : ''; ?>">
									<img src="<?php echo esc_url($cred_src); ?>" alt="<?php echo esc_attr($cred_alt); ?>" loading="lazy" decoding="async" />
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<div class="rh-home-about__media-card" data-rh-fx="scale" data-rh-fx-tone="dark">
			<div class="rh-home-about__media">
				<?php
				if ($about_section_image_id > 0 && wp_attachment_is_image($about_section_image_id)) {
					$about_img_alt = (string) get_post_meta($about_section_image_id, '_wp_attachment_image_alt', true);
					echo wp_get_attachment_image(
						$about_section_image_id,
						'large',
						false,
						array(
							'class'    => 'rh-home-about__img',
							'loading'  => 'lazy',
							'decoding' => 'async',
							'sizes'    => '(max-width: 1000px) 100vw, 50vw',
							'alt'      => $about_img_alt,
						)
					);
				} else {
					printf(
						'<img class="rh-home-about__img" src="%s" alt="%s" width="1200" height="800" loading="lazy" decoding="async" />',
						esc_url(rh_carpentry_get_about_section_image_url()),
						esc_attr__('Modern kitchen interior fitted by RH Carpentry', 'rh-base-child')
					);
				}
				?>
			</div>
		</div>
		<div class="rh-home-about__stats-wrap" data-rh-fx="fade" data-rh-fx-tone="dark">
			<?php get_template_part('template-parts/home/hero-stats-strip'); ?>
		</div>
	</div>
</div>
