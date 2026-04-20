<?php
/**
 * 404 template.
 *
 * @package RH_Base
 */

get_header();
?>

<div class="rh-container rh-container--narrow">
	<section class="error-404 not-found">
		<header class="page-header">
			<h1 class="page-title"><?php esc_html_e('Page not found', 'rh-base'); ?></h1>
		</header>
		<div class="page-content">
			<p><?php esc_html_e('Nothing matched that address. Try a search below.', 'rh-base'); ?></p>
			<?php get_search_form(); ?>
		</div>
	</section>
</div>

<?php
get_footer();
