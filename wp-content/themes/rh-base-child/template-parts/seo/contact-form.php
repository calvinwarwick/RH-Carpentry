<?php
/**
 * Contact form (home overlay + contact page).
 *
 * @package RH_Base_Child
 */

if (! defined('ABSPATH')) {
	exit;
}

$return_url     = is_page() ? get_permalink() : trailingslashit(home_url('/')) . '#contact';
$field_prefix   = isset($field_id_prefix) ? sanitize_html_class((string) $field_id_prefix) : 'rh-contact-page';
$wrapper_class  = isset($wrapper_class) ? (string) $wrapper_class : 'rh-contact-page-form';
if ($wrapper_class === '') {
	$wrapper_class = 'rh-contact-page-form';
}
?>
<div class="<?php echo esc_attr($wrapper_class); ?>" data-rh-contact-page-form>
	<div class="rh-contact-page-form__notice-slot" data-rh-contact-notice-slot hidden>
		<p class="rh-contact-overlay__notice" data-rh-contact-notice role="status"></p>
	</div>

	<form class="rh-contact-overlay__form rh-contact-page-form__form" method="post" action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" data-rh-contact-form novalidate>
		<input type="hidden" name="action" value="rh_home_contact" />
		<input type="hidden" name="rh_contact_return" value="<?php echo esc_url($return_url); ?>" />
		<?php wp_nonce_field('rh_home_contact', 'rh_home_contact_nonce'); ?>

		<div class="rh-contact-overlay__fields">
			<div class="rh-contact-overlay__field">
				<label class="rh-form-floating" for="<?php echo esc_attr($field_prefix); ?>-name">
					<input class="rh-form-floating__control" type="text" name="rh_contact_name" id="<?php echo esc_attr($field_prefix); ?>-name" autocomplete="name" placeholder=" " required />
					<span class="rh-form-floating__label-text"><?php esc_html_e('Name', 'rh-base-child'); ?></span>
				</label>
			</div>
			<div class="rh-contact-overlay__field">
				<label class="rh-form-floating" for="<?php echo esc_attr($field_prefix); ?>-email">
					<input class="rh-form-floating__control" type="email" name="rh_contact_email" id="<?php echo esc_attr($field_prefix); ?>-email" autocomplete="email" placeholder=" " required />
					<span class="rh-form-floating__label-text"><?php esc_html_e('Email', 'rh-base-child'); ?></span>
				</label>
			</div>
			<div class="rh-contact-overlay__field rh-contact-overlay__field--full">
				<label class="rh-form-floating" for="<?php echo esc_attr($field_prefix); ?>-phone">
					<input class="rh-form-floating__control" type="tel" name="rh_contact_phone" id="<?php echo esc_attr($field_prefix); ?>-phone" autocomplete="tel" placeholder=" " />
					<span class="rh-form-floating__label-text"><?php esc_html_e('Phone', 'rh-base-child'); ?></span>
				</label>
			</div>
			<div class="rh-contact-overlay__field rh-contact-overlay__field--full">
				<label class="rh-form-floating rh-form-floating--textarea" for="<?php echo esc_attr($field_prefix); ?>-message">
					<textarea class="rh-form-floating__control rh-form-floating__control--textarea" name="rh_contact_message" id="<?php echo esc_attr($field_prefix); ?>-message" rows="5" placeholder=" " required></textarea>
					<span class="rh-form-floating__label-text"><?php esc_html_e('Message', 'rh-base-child'); ?></span>
				</label>
			</div>
		</div>

		<button type="submit" class="rh-hero-btn rh-hero-btn--accent rh-contact-overlay__submit">
			<?php esc_html_e('Send message', 'rh-base-child'); ?>
		</button>
	</form>
</div>
