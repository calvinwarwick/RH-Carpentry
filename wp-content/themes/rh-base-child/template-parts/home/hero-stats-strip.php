<?php
/**
 * Homepage stats strip (below About; before services / testimonials).
 *
 * @package RH_Base_Child
 */

if (! defined('ABSPATH')) {
	exit;
}
?>
<section class="rh-home-stats-strip rh-home-stats-strip--pending" aria-label="<?php esc_attr_e('Key figures', 'rh-base-child'); ?>">
	<div class="rh-home-stats-strip__inner">
		<ul class="rh-home-stats-strip__list" role="list">
			<li class="rh-home-stats-strip__item" data-rh-stat-item>
				<span
					class="rh-home-stats-strip__value"
					data-rh-stat-value
					data-target="40"
					data-prefix=""
					data-suffix="+"
					data-group="0"
				>0+</span>
				<span class="rh-home-stats-strip__label">
					<span class="screen-reader-text"><?php esc_html_e('years experience', 'rh-base-child'); ?></span>
					<span class="rh-home-stats-strip__label-chars" aria-hidden="true"></span>
				</span>
			</li>
			<li class="rh-home-stats-strip__item" data-rh-stat-item>
				<span
					class="rh-home-stats-strip__value"
					data-rh-stat-value
					data-target="900"
					data-prefix=""
					data-suffix="+"
					data-group="0"
				>0+</span>
				<span class="rh-home-stats-strip__label">
					<span class="screen-reader-text"><?php esc_html_e('Projects Completed', 'rh-base-child'); ?></span>
					<span class="rh-home-stats-strip__label-chars" aria-hidden="true"></span>
				</span>
			</li>
			<li class="rh-home-stats-strip__item" data-rh-stat-item>
				<span
					class="rh-home-stats-strip__value"
					data-rh-stat-value
					data-target="1200"
					data-prefix=""
					data-suffix=""
					data-group="1"
				>0</span>
				<span class="rh-home-stats-strip__label">
					<span class="screen-reader-text"><?php esc_html_e('Happy Customers', 'rh-base-child'); ?></span>
					<span class="rh-home-stats-strip__label-chars" aria-hidden="true"></span>
				</span>
			</li>
		</ul>
	</div>
</section>
