<?php

/**
 * Contact Form Handler for 500 Club
 * Handles form submission and sends emails via Resend API
 */

// Include configuration
require_once(__DIR__ . '/config/resend-config.php');

// Simple sanitization functions (WordPress alternatives)
if (!function_exists('sanitize_text_field')) {
    function sanitize_text_field($str)
    {
        return htmlspecialchars(trim($str), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('sanitize_email')) {
    function sanitize_email($email)
    {
        return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
    }
}

if (!function_exists('sanitize_textarea_field')) {
    function sanitize_textarea_field($str)
    {
        return htmlspecialchars(trim($str), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('is_email')) {
    function is_email($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}

if (!function_exists('esc_html')) {
    function esc_html($text)
    {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
}

// Set content type for JSON response
header('Content-Type: application/json');

// Enable CORS for AJAX requests
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Debug: Log that the file is being accessed
if (CONTACT_FORM_DEBUG) {
    error_log('Contact handler accessed at: ' . date('Y-m-d H:i:s'));
}

// Check if contact form is enabled
if (!CONTACT_FORM_ENABLED) {
    http_response_code(503);
    echo json_encode(['success' => false, 'message' => 'Contact form is currently disabled']);
    exit;
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Honeypot spam protection
if (!empty($_POST[CONTACT_FORM_HONEYPOT_FIELD])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Spam detected']);
    exit;
}

// Rate limiting (simple IP-based)
$client_ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$rate_limit_key = 'contact_form_' . md5($client_ip);
$rate_limit_file = sys_get_temp_dir() . '/' . $rate_limit_key;

if (file_exists($rate_limit_file)) {
    $submissions = (int)file_get_contents($rate_limit_file);
    $last_submission = filemtime($rate_limit_file);

    // Reset counter if more than an hour has passed
    if (time() - $last_submission > 3600) {
        $submissions = 0;
    }

    if ($submissions >= CONTACT_FORM_RATE_LIMIT) {
        http_response_code(429);
        echo json_encode(['success' => false, 'message' => 'Too many submissions. Please try again later.']);
        exit;
    }
}

// Get form data
$name = sanitize_text_field($_POST['name'] ?? '');
$phone = sanitize_text_field($_POST['phone'] ?? '');
$email = sanitize_email($_POST['email'] ?? '');
$message = sanitize_textarea_field($_POST['message'] ?? '');

// Validate required fields
$errors = [];

if (empty($name)) {
    $errors[] = 'Name is required';
}

if (empty($email) || !is_email($email)) {
    $errors[] = 'Valid email is required';
}

if (empty($message)) {
    $errors[] = 'Message is required';
}

// If there are validation errors, return them
if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Validation failed', 'errors' => $errors]);
    exit;
}

// Update rate limit counter
if (file_exists($rate_limit_file)) {
    $submissions = (int)file_get_contents($rate_limit_file) + 1;
} else {
    $submissions = 1;
}
file_put_contents($rate_limit_file, $submissions);

// Resend API configuration
$resend_api_key = RESEND_API_KEY;
$resend_api_url = 'https://api.resend.com/emails';

// Prepare email content
$email_subject = CONTACT_EMAIL_SUBJECT;
$email_body = "
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #1398A5; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9f9f9; }
        .field { margin-bottom: 15px; }
        .label { font-weight: bold; color: #1398A5; }
        .value { margin-top: 5px; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h2>New Contact Form Submission</h2>
            <p>500 Club Website</p>
        </div>
        <div class='content'>
            <div class='field'>
                <div class='label'>Name:</div>
                <div class='value'>" . esc_html($name) . "</div>
            </div>
            <div class='field'>
                <div class='label'>Email:</div>
                <div class='value'>" . esc_html($email) . "</div>
            </div>
            <div class='field'>
                <div class='label'>Phone:</div>
                <div class='value'>" . esc_html($phone) . "</div>
            </div>
            <div class='field'>
                <div class='label'>Message:</div>
                <div class='value'>" . nl2br(esc_html($message)) . "</div>
            </div>
        </div>
        <div class='footer'>
            <p>This email was sent from the 500 Club contact form.</p>
            <p>Submitted on: " . date('Y-m-d H:i:s') . "</p>
        </div>
    </div>
</body>
</html>
";

// Prepare the data for Resend API
$email_data = [
    'from' => RESEND_FROM_EMAIL,
    'to' => [RESEND_TO_EMAIL],
    'subject' => $email_subject,
    'html' => $email_body,
    'reply_to' => $email
];

// Send email via Resend API
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $resend_api_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($email_data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $resend_api_key,
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

// Handle the response
if ($curl_error) {
    if (CONTACT_FORM_LOG_ERRORS) {
        error_log('Resend API cURL Error: ' . $curl_error);
    }
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to send email. Please try again later.']);
    exit;
}

$response_data = json_decode($response, true);

if ($http_code === 200 && isset($response_data['id'])) {
    // Success - email sent
    if (CONTACT_FORM_DEBUG) {
        error_log('Contact form email sent successfully. ID: ' . $response_data['id']);
    }
    echo json_encode([
        'success' => true,
        'message' => 'Thank you for your message! We will get back to you soon.',
        'email_id' => $response_data['id']
    ]);
} else {
    // Error sending email
    if (CONTACT_FORM_LOG_ERRORS) {
        error_log('Resend API Error: ' . $response);
    }
    http_response_code(500);
    $debug_info = CONTACT_FORM_DEBUG ? $response_data : null;
    echo json_encode([
        'success' => false,
        'message' => 'Failed to send email. Please try again later.',
        'debug' => $debug_info
    ]);
}
