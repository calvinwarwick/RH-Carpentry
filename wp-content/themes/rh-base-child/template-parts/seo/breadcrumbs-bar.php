<?php
/**
 * Site-wide breadcrumbs (all pages except home).
 *
 * @package RH_Base_Child
 */

if (! defined('ABSPATH')) {
	exit;
}

if (! function_exists('rh_seo_render_breadcrumbs')) {
	return;
}

$rh_crumbs = rh_seo_breadcrumbs();
if ($rh_crumbs === array()) {
	return;
}
?>
<div class="rh-breadcrumbs-bar">
	<div class="rh-breadcrumbs-bar__inner">
		<?php rh_seo_render_breadcrumbs(); ?>
	</div>
</div>
