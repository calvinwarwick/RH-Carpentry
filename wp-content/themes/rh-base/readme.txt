=== RH Base ===

Contributors: rh-carpentry
Requires at least: 6.0
Tested up to: 6.9
Requires PHP: 8.1
Stable tag: 1.0.0
License: GPLv2 or later

Parent starter theme for client sites. Activate a child theme (see rh-base-child) for per-project branding.

== Build ==

From this directory:

1. npm install
2. npm run build

Compiled CSS/JS are written to build/. Deploy the entire theme folder (including build/) to wp-content/themes/rh-base/.

== Child theme workflow ==

1. Copy wp-content/themes/rh-base-child/ to a new folder, e.g. wp-content/themes/client-name/
2. Update style.css headers (Theme Name, Text Domain, description)
3. Adjust :root variables in style.css or add template overrides
4. In WordPress admin, activate the child theme (not RH Base directly on production if you rely on overrides)

== Optional JS island ==

The page template “With interactive (demo island)” loads build/rh-base-interactive.js only on that template. Copy the pattern for other conditional bundles in inc/enqueue.php.
