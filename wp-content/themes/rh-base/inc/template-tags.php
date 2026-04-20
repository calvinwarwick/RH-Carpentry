<?php
/**
 * Template tags.
 *
 * @package RH_Base
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
	exit;
}

/**
 * Prints HTML with meta information for the current post-date/time.
 */
function rh_base_posted_on(): void {
	$published = sprintf(
		'<time class="entry-date published" datetime="%1$s">%2$s</time>',
		esc_attr(get_the_date(DATE_W3C)),
		esc_html(get_the_date())
	);

	$updated = '';
	if (get_the_time('U') !== get_the_modified_time('U')) {
		$updated = sprintf(
			' <time class="updated" datetime="%1$s">%2$s</time>',
			esc_attr(get_the_modified_date(DATE_W3C)),
			esc_html(get_the_modified_date())
		);
	}

	echo '<span class="posted-on">' . $published . $updated . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Prints HTML with meta information for categories.
 */
function rh_base_categories(): void {
	$categories_list = get_the_category_list(esc_html__(', ', 'rh-base'));
	if ($categories_list) {
		printf('<span class="cat-links">%s</span>', $categories_list); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

/**
 * Pagination for archives.
 */
function rh_base_the_posts_navigation(): void {
	the_posts_pagination(
		array(
			'mid_size'  => 2,
			'prev_text' => esc_html__('Older posts', 'rh-base'),
			'next_text' => esc_html__('Newer posts', 'rh-base'),
		)
	);
}
