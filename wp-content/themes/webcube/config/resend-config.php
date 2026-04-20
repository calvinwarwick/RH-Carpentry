<?php

/**
 * Resend API Configuration
 * 
 * IMPORTANT: Replace these values with your actual Resend API credentials
 * 
 * To get your Resend API key:
 * 1. Sign up at https://resend.com
 * 2. Go to API Keys section
 * 3. Create a new API key
 * 4. Replace the placeholder below with your actual key
 * 
 * To verify your domain:
 * 1. In Resend dashboard, go to Domains
 * 2. Add your domain (e.g., 500club.org)
 * 3. Follow the DNS verification steps
 * 4. Update the 'from' email address below
 */

// Resend API Configuration
define('RESEND_API_KEY', 're_ig5XoWcx_7eAPn6FUUFjwhTtiAhKADJYt'); // Replace with your actual Resend API key
define('RESEND_FROM_EMAIL', '500 Club <noreply@forms.palmerpartners.uk>'); // Replace with your verified domain
define('RESEND_TO_EMAIL', 'hello@500club.org'); // Admin email to receive contact form submissions

// Email Templates
define('CONTACT_EMAIL_SUBJECT', 'New Contact Form Submission - 500 Club');
define('CONTACT_EMAIL_FROM_NAME', '500 Club Website');

// Security Settings
define('CONTACT_FORM_ENABLED', true);
define('CONTACT_FORM_RATE_LIMIT', 5); // Max submissions per hour per IP
define('CONTACT_FORM_HONEYPOT_FIELD', 'website'); // Hidden field name for spam protection

// Debug Settings (set to false in production)
define('CONTACT_FORM_DEBUG', true);
define('CONTACT_FORM_LOG_ERRORS', true);
