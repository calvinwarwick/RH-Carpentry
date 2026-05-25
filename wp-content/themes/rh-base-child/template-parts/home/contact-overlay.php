<?php
/**
 * Full-viewport contact overlay (#contact) — hero-matched frame and background.
 *
 * @package RH_Base_Child
 */

if (! defined('ABSPATH')) {
	exit;
}

$bg_url   = rh_carpentry_get_hero_background_url();
$notice   = isset($_GET['contact']) ? sanitize_key(wp_unslash($_GET['contact'])) : '';
$messages = rh_carpentry_home_contact_messages();
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
				<button type="button" class="rh-contact-overlay__close" data-rh-contact-close aria-label="<?php esc_attr_e('Close', 'rh-base-child'); ?>">
					<span class="rh-contact-overlay__close-icon" aria-hidden="true"></span>
				</button>
			</header>

			<div class="rh-contact-overlay__body">
				<p class="rh-hero-kicker rh-contact-overlay__kicker">
					<span class="rh-hero-kicker__line" aria-hidden="true"></span>
					<?php esc_html_e('Start your project now', 'rh-base-child'); ?>
				</p>
				<h2 class="rh-hero-title rh-contact-overlay__title" id="rh-contact-heading"><?php esc_html_e('Get in touch', 'rh-base-child'); ?></h2>
				<p class="rh-contact-overlay__intro"><?php esc_html_e('Tell us a little about your project and we will get back to you shortly.', 'rh-base-child'); ?></p>

				<div class="rh-contact-overlay__notice-slot" data-rh-contact-notice-slot hidden>
					<p class="rh-contact-overlay__notice" data-rh-contact-notice role="status"></p>
				</div>

				<?php if ($notice !== '' && isset($messages[ $notice ])) : ?>
					<p class="rh-contact-overlay__notice rh-contact-overlay__notice--<?php echo $notice === 'sent' ? 'success' : 'warn'; ?>" role="<?php echo $notice === 'sent' ? 'status' : 'alert'; ?>">
						<?php echo esc_html($messages[ $notice ]); ?>
					</p>
				<?php endif; ?>

				<form class="rh-contact-overlay__form" method="post" action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" data-rh-contact-form novalidate>
					<input type="hidden" name="action" value="rh_home_contact" />
					<?php wp_nonce_field('rh_home_contact', 'rh_home_contact_nonce'); ?>
					<?php rh_carpentry_contact_form_render_dev_fields(); ?>

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
						<button type="submit" class="rh-hero-btn rh-hero-btn--accent rh-contact-overlay__submit"><?php esc_html_e('Send message', 'rh-base-child'); ?></button>
					</p>
				</form>
			</div>
		</div>
	</div>
</div>
