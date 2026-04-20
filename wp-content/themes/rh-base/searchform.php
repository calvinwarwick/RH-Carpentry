<?php
/**
 * Search form markup.
 *
 * @package RH_Base
 */

if (! defined('ABSPATH')) {
	exit;
}

$unique_id = wp_unique_id('search-form-');
?>

<form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
	<label for="<?php echo esc_attr($unique_id); ?>">
		<span class="screen-reader-text"><?php esc_html_e('Search for:', 'rh-base'); ?></span>
	</label>
	<input type="search" id="<?php echo esc_attr($unique_id); ?>" class="search-field" placeholder="<?php esc_attr_e('Search …', 'rh-base'); ?>" value="<?php echo get_search_query(); ?>" name="s" />
	<button type="submit" class="search-submit rh-button"><?php esc_html_e('Search', 'rh-base'); ?></button>
</form>
