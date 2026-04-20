<?php
/**
 * Search result item.
 *
 * @package RH_Base
 */

if (! defined('ABSPATH')) {
	exit;
}
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('rh-card'); ?>>
	<header class="entry-header">
		<?php the_title('<h2 class="entry-title"><a href="' . esc_url(get_permalink()) . '" rel="bookmark">', '</a></h2>'); ?>
		<div class="entry-meta"><?php rh_base_posted_on(); ?></div>
	</header>
	<div class="entry-summary"><?php the_excerpt(); ?></div>
</article>
