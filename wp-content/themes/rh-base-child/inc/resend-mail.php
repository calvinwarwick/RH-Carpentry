<?php
/**
 * Resend API mail helper for contact form notifications.
 *
 * Configure in wp-config.php (recommended):
 *   define('RH_RESEND_API_KEY', 're_...');
 *   define('RH_RESEND_TO_EMAIL', 'you@example.com');
 *
 * Outbound mail uses Resend's default sender (onboarding@resend.dev).
 * Reply-To is set to the visitor's email on contact form submissions.
 *
 * @package RH_Base_Child
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
	exit;
}

/**
 * Resend API key from constant or environment variable.
 */
function rh_carpentry_resend_api_key(): string {
	if (defined('RH_RESEND_API_KEY') && is_string(RH_RESEND_API_KEY) && RH_RESEND_API_KEY !== '') {
		return RH_RESEND_API_KEY;
	}
	$env = getenv('RH_RESEND_API_KEY');
	return is_string($env) && $env !== '' ? $env : '';
}

/**
 * Default "from" address (Resend onboarding sender — no custom domain required).
 */
function rh_carpentry_resend_from_email(): string {
	if (defined('RH_RESEND_FROM_EMAIL') && is_string(RH_RESEND_FROM_EMAIL) && RH_RESEND_FROM_EMAIL !== '') {
		return RH_RESEND_FROM_EMAIL;
	}
	$site = wp_specialchars_decode((string) get_bloginfo('name'), ENT_QUOTES);
	return $site . ' <onboarding@resend.dev>';
}

/**
 * Dev/test inbox when the contact form is loaded with ?dev in the URL.
 */
function rh_carpentry_resend_dev_email(): string {
	if (defined('RH_RESEND_DEV_EMAIL') && is_string(RH_RESEND_DEV_EMAIL) && is_email(RH_RESEND_DEV_EMAIL)) {
		return RH_RESEND_DEV_EMAIL;
	}
	return '';
}

/**
 * Whether the current request should render ?dev contact-form fields.
 */
function rh_carpentry_contact_form_dev_mode_enabled(): bool {
	if (rh_carpentry_resend_dev_email() === '') {
		return false;
	}
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	return isset($_GET['dev']);
}

/**
 * Whether a submission should go to the dev inbox (requires ?dev page + nonce).
 */
function rh_carpentry_contact_form_use_dev_recipient(): bool {
	if (rh_carpentry_resend_dev_email() === '') {
		return false;
	}
	if (! isset($_POST['rh_contact_dev']) || (string) wp_unslash($_POST['rh_contact_dev']) !== '1') {
		return false;
	}
	if (! isset($_POST['rh_contact_dev_nonce']) || ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['rh_contact_dev_nonce'])), 'rh_contact_dev')) {
		return false;
	}
	return true;
}

/**
 * Hidden fields for ?dev test submissions (contact page + overlay forms).
 */
function rh_carpentry_contact_form_render_dev_fields(): void {
	if (! rh_carpentry_contact_form_dev_mode_enabled()) {
		return;
	}
	echo '<input type="hidden" name="rh_contact_dev" value="1" />';
	wp_nonce_field('rh_contact_dev', 'rh_contact_dev_nonce');
}

/**
 * Recipient for contact form notifications.
 */
function rh_carpentry_resend_to_email(): string {
	if (rh_carpentry_contact_form_use_dev_recipient()) {
		return rh_carpentry_resend_dev_email();
	}
	if (defined('RH_RESEND_TO_EMAIL') && is_string(RH_RESEND_TO_EMAIL) && is_email(RH_RESEND_TO_EMAIL)) {
		return RH_RESEND_TO_EMAIL;
	}
	$admin = (string) get_option('admin_email');
	return is_email($admin) ? $admin : '';
}

/**
 * Send an email via Resend.
 *
 * @param array{to:string|string[],subject:string,html:string,reply_to?:string,text?:string} $args
 * @return array{ok:bool,id?:string,error?:string,http_code?:int}
 */
function rh_carpentry_resend_send(array $args): array {
	$api_key = rh_carpentry_resend_api_key();
	if ($api_key === '') {
		return array('ok' => false, 'error' => 'Resend API key is not configured.');
	}

	$to = $args['to'] ?? '';
	if (is_string($to)) {
		$to = array($to);
	}
	if (! is_array($to) || $to === array()) {
		return array('ok' => false, 'error' => 'Missing recipient.');
	}

	$subject = isset($args['subject']) ? (string) $args['subject'] : '';
	$html    = isset($args['html']) ? (string) $args['html'] : '';
	if ($subject === '' || $html === '') {
		return array('ok' => false, 'error' => 'Missing subject or body.');
	}

	$payload = array(
		'from'    => rh_carpentry_resend_from_email(),
		'to'      => array_values($to),
		'subject' => $subject,
		'html'    => $html,
	);
	if (! empty($args['text']) && is_string($args['text'])) {
		$payload['text'] = $args['text'];
	}
	if (! empty($args['reply_to']) && is_email($args['reply_to'])) {
		$payload['reply_to'] = (string) $args['reply_to'];
	}

	if (! function_exists('curl_init')) {
		return array('ok' => false, 'error' => 'cURL is not available on this server.');
	}

	$ch = curl_init('https://api.resend.com/emails');
	if ($ch === false) {
		return array('ok' => false, 'error' => 'Could not initialize cURL.');
	}

	curl_setopt_array(
		$ch,
		array(
			CURLOPT_POST           => true,
			CURLOPT_POSTFIELDS     => wp_json_encode($payload),
			CURLOPT_HTTPHEADER     => array(
				'Authorization: Bearer ' . $api_key,
				'Content-Type: application/json',
			),
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => true,
			CURLOPT_TIMEOUT        => 30,
		)
	);

	$response  = curl_exec($ch);
	$http_code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
	$curl_err  = curl_error($ch);
	curl_close($ch);

	if ($curl_err !== '') {
		return array('ok' => false, 'error' => $curl_err, 'http_code' => $http_code);
	}

	$data = is_string($response) ? json_decode($response, true) : null;
	if ($http_code >= 200 && $http_code < 300 && is_array($data) && ! empty($data['id'])) {
		return array('ok' => true, 'id' => (string) $data['id'], 'http_code' => $http_code);
	}

	$message = 'Resend request failed.';
	if (is_array($data) && isset($data['message']) && is_string($data['message'])) {
		$message = $data['message'];
	}

	return array('ok' => false, 'error' => $message, 'http_code' => $http_code);
}

/**
 * Send homepage contact form notification to the site admin.
 *
 * @return bool True when Resend accepted the message.
 */
function rh_carpentry_send_contact_form_email(string $name, string $email, string $phone, string $message): bool {
	$to = rh_carpentry_resend_to_email();
	if ($to === '') {
		return false;
	}

	$site_name = wp_specialchars_decode((string) get_bloginfo('name'), ENT_QUOTES);
	$subject   = sprintf(
		/* translators: %s: site name */
		__('[%s] Website contact form', 'rh-base-child'),
		$site_name
	);
	if (rh_carpentry_contact_form_use_dev_recipient()) {
		$subject = '[DEV] ' . $subject;
	}

	$safe_name    = esc_html($name);
	$safe_email   = esc_html($email);
	$safe_phone   = esc_html($phone !== '' ? $phone : '—');
	$safe_message = nl2br(esc_html($message));

	$html = '<div style="font-family:Arial,sans-serif;line-height:1.6;color:#1a1a1a;max-width:600px">'
		. '<h2 style="margin:0 0 1rem;color:#0c1c34">' . esc_html__('New contact form submission', 'rh-base-child') . '</h2>'
		. '<p><strong>' . esc_html__('Name', 'rh-base-child') . ':</strong> ' . $safe_name . '</p>'
		. '<p><strong>' . esc_html__('Email', 'rh-base-child') . ':</strong> ' . $safe_email . '</p>'
		. '<p><strong>' . esc_html__('Phone', 'rh-base-child') . ':</strong> ' . $safe_phone . '</p>'
		. '<p><strong>' . esc_html__('Message', 'rh-base-child') . ':</strong><br>' . $safe_message . '</p>'
		. '<p style="margin-top:1.5rem;font-size:12px;color:#666">'
		. esc_html(sprintf('Sent from %s at %s', $site_name, wp_date('Y-m-d H:i:s T'))) . '</p>'
		. '</div>';

	$text = "Name: {$name}\nEmail: {$email}\nPhone: {$phone}\n\n{$message}\n";

	$reply_to = $email;
	if ($name !== '' && is_email($email)) {
		$reply_to = sprintf('%s <%s>', $name, $email);
	}

	$result = rh_carpentry_resend_send(
		array(
			'to'       => $to,
			'subject'  => $subject,
			'html'     => $html,
			'text'     => $text,
			'reply_to' => $reply_to,
		)
	);

	if ($result['ok']) {
		return true;
	}

	$error = $result['error'] ?? 'unknown error';
	// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
	error_log(
		sprintf(
			'RH Resend contact form failed (HTTP %s): %s',
			isset($result['http_code']) ? (string) $result['http_code'] : '?',
			$error
		)
	);

	/* When Resend is configured, do not pretend success via wp_mail — it hides API failures
	   and nothing appears in the Resend dashboard. Verify your domain and set RH_RESEND_FROM_EMAIL. */
	if (rh_carpentry_resend_api_key() !== '') {
		return false;
	}

	$headers = array('Content-Type: text/html; charset=UTF-8');
	if ($reply_to !== '') {
		$headers[] = 'Reply-To: ' . $reply_to;
	}

	return wp_mail($to, $subject, $html, $headers);
}
