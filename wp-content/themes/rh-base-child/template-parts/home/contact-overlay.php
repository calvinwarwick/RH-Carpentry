<?php
/**
 * Full-viewport contact overlay (#contact) — hero-matched frame and background.
 *
 * @package RH_Base_Child
 */

if (! defined('ABSPATH')) {
	exit;
}

$bg_url = rh_carpentry_get_hero_background_url();
$notice = isset($_GET['contact']) ? sanitize_key(wp_unslash($_GET['contact'])) : '';

$overlay_phone_label = (string) get_theme_mod('rh_contact_phone', '');
$overlay_phone_href  = rh_carpentry_tel_href_from_display($overlay_phone_label);
if ($overlay_phone_href === '') {
	$overlay_phone_label = (string) get_theme_mod('rh_contact_mobile', '');
	$overlay_phone_href  = rh_carpentry_tel_href_from_display($overlay_phone_label);
}

$overlay_social_items = array();
foreach (rh_carpentry_get_footer_contact_icon_items() as $item) {
	if (! empty($item['external'])) {
		$overlay_social_items[] = $item;
	}
}

$overlay_show_alt = ($overlay_phone_href !== '' || $overlay_social_items !== array());
?>
<div
	id="contact"
	class="rh-contact-overlay"
	data-rh-contact-overlay
	role="dialog"
	aria-modal="true"
	aria-labelledby="rh-contact-heading"
	aria-hidden="true"
>
	<div class="rh-hero-home rh-contact-overlay__hero">
		<div class="rh-hero-home__bg" style="background-image: url('<?php echo esc_url($bg_url); ?>');"></div>
		<div class="rh-hero-home__overlay" aria-hidden="true"></div>

		<div class="rh-hero-home__inner rh-contact-overlay__inner">
			<header class="rh-contact-overlay__bar">
				<p class="rh-hero-kicker rh-contact-overlay__kicker">
					<span class="rh-hero-kicker__line" aria-hidden="true"></span>
					<?php esc_html_e('Start your project now', 'rh-base-child'); ?>
				</p>
				<button type="button" class="rh-contact-overlay__close" data-rh-contact-close aria-label="<?php esc_attr_e('Close', 'rh-base-child'); ?>">
					<span class="rh-contact-overlay__close-icon" aria-hidden="true"></span>
				</button>
			</header>

			<div class="rh-contact-overlay__body">
				<h2 class="rh-hero-title rh-contact-overlay__title" id="rh-contact-heading"><?php esc_html_e('Get in touch', 'rh-base-child'); ?></h2>

				<?php if ($notice === 'sent') : ?>
					<p class="rh-contact-overlay__notice rh-contact-overlay__notice--success" role="status">
						<?php esc_html_e('Thank you — your message has been sent.', 'rh-base-child'); ?>
					</p>
				<?php elseif ($notice === 'required') : ?>
					<p class="rh-contact-overlay__notice rh-contact-overlay__notice--warn" role="alert">
						<?php esc_html_e('Please fill in your name, a valid email, and a message.', 'rh-base-child'); ?>
					</p>
				<?php elseif ($notice === 'invalid') : ?>
					<p class="rh-contact-overlay__notice rh-contact-overlay__notice--warn" role="alert">
						<?php esc_html_e('Something went wrong. Please try again.', 'rh-base-child'); ?>
					</p>
				<?php endif; ?>

				<form class="rh-contact-overlay__form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
					<input type="hidden" name="action" value="rh_home_contact" />
					<?php wp_nonce_field('rh_home_contact', 'rh_home_contact_nonce'); ?>

					<div class="rh-contact-overlay__fields">
						<div class="rh-contact-overlay__field">
							<label class="rh-form-floating" for="rh-contact-name">
								<input class="rh-form-floating__control" type="text" name="rh_contact_name" id="rh-contact-name" autocomplete="name" placeholder=" " required />
								<span class="rh-form-floating__label-text"><?php esc_html_e('Name', 'rh-base-child'); ?></span>
							</label>
						</div>
						<div class="rh-contact-overlay__field">
							<label class="rh-form-floating" for="rh-contact-email">
								<input class="rh-form-floating__control" type="email" name="rh_contact_email" id="rh-contact-email" autocomplete="email" placeholder=" " required />
								<span class="rh-form-floating__label-text"><?php esc_html_e('Email', 'rh-base-child'); ?></span>
							</label>
						</div>
						<div class="rh-contact-overlay__field rh-contact-overlay__field--full">
							<label class="rh-form-floating" for="rh-contact-phone">
								<input class="rh-form-floating__control" type="tel" name="rh_contact_phone" id="rh-contact-phone" autocomplete="tel" placeholder=" " />
								<span class="rh-form-floating__label-text"><?php esc_html_e('Phone', 'rh-base-child'); ?></span>
							</label>
						</div>
						<div class="rh-contact-overlay__field rh-contact-overlay__field--full">
							<label class="rh-form-floating rh-form-floating--textarea" for="rh-contact-message">
								<textarea class="rh-form-floating__control rh-form-floating__control--textarea" name="rh_contact_message" id="rh-contact-message" rows="5" placeholder=" " required></textarea>
								<span class="rh-form-floating__label-text"><?php esc_html_e('Message', 'rh-base-child'); ?></span>
							</label>
						</div>
					</div>
					<p class="rh-contact-overlay__actions">
						<button type="submit" class="rh-hero-btn rh-hero-btn--accent rh-contact-overlay__submit"><?php esc_html_e('Send', 'rh-base-child'); ?></button>
					</p>
				</form>

				<?php if ($overlay_show_alt) : ?>
					<div class="rh-contact-overlay__after-form">
						<div class="rh-contact-overlay__or" role="presentation">
							<hr class="rh-contact-overlay__or-line" aria-hidden="true" />
							<span class="rh-contact-overlay__or-label"><?php esc_html_e('OR', 'rh-base-child'); ?></span>
						</div>

						<?php if ($overlay_phone_href !== '') : ?>
							<a
								class="rh-hero-btn rh-hero-btn--muted rh-contact-overlay__call-btn"
								href="<?php echo esc_url($overlay_phone_href); ?>"
							>
								<i class="fa-solid fa-phone rh-contact-overlay__call-icon" aria-hidden="true"></i>
								<span class="rh-contact-overlay__call-inner">
									<span class="rh-contact-overlay__call-kicker"><?php esc_html_e('Give us a call', 'rh-base-child'); ?></span>
									<span class="rh-contact-overlay__call-number"><?php echo esc_html($overlay_phone_label); ?></span>
								</span>
							</a>
						<?php endif; ?>

						<?php if ($overlay_social_items !== array()) : ?>
							<div class="rh-contact-overlay__social-wrap">
								<p class="rh-hero-kicker rh-contact-overlay__social-kicker">
									<span class="rh-hero-kicker__line" aria-hidden="true"></span>
									<?php esc_html_e('Socials', 'rh-base-child'); ?>
								</p>
								<nav class="rh-contact-overlay__social rh-hero-social" aria-label="<?php esc_attr_e('Social media', 'rh-base-child'); ?>">
								<?php
								foreach ($overlay_social_items as $item) {
									$rel_target   = ! empty($item['external']) ? ' rel="noopener noreferrer" target="_blank"' : '';
									$title_attr   = ($item['title'] ?? '') !== '' ? ' title="' . esc_attr((string) $item['title']) . '"' : '';
									$icon_classes = isset($item['icon']) ? (string) $item['icon'] : 'fa-solid fa-link';
									printf(
										'<a class="rh-hero-icon-btn" href="%s"%s%s><span class="screen-reader-text">%s</span><i class="%s" aria-hidden="true"></i></a>',
										esc_url($item['href']),
										$title_attr,
										$rel_target,
										esc_html($item['label']),
										esc_attr($icon_classes)
									);
								}
								?>
								</nav>
							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
