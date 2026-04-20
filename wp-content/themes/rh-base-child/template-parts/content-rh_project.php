<?php
/**
 * Project card (archive / grids).
 *
 * @package RH_Base_Child
 */

if (! defined('ABSPATH')) {
	exit;
}

$terms = get_the_terms(get_the_ID(), 'rh_project_sector');
$label = '';
if ($terms && ! is_wp_error($terms)) {
	$label = $terms[0]->name;
}
?>

<li class="rh-archive-project-card">
	<a class="rh-archive-project-card__link" href="<?php the_permalink(); ?>">
		<div class="rh-archive-project-card__media">
			<?php
			if (has_post_thumbnail()) {
				the_post_thumbnail(
					'medium_large',
					array(
						'class'   => 'rh-archive-project-card__img',
						'loading' => 'lazy',
						'alt'     => esc_attr(get_the_title()),
					)
				);
			} else {
				echo '<div class="rh-archive-project-card__placeholder" aria-hidden="true"></div>';
			}
			?>
		</div>
		<div class="rh-archive-project-card__body">
			<?php if ($label !== '') : ?>
				<span class="rh-archive-project-card__tag"><?php echo esc_html($label); ?></span>
			<?php endif; ?>
			<h2 class="rh-archive-project-card__title"><?php the_title(); ?></h2>
			<?php if (has_excerpt()) : ?>
				<p class="rh-archive-project-card__excerpt"><?php echo esc_html(wp_strip_all_tags(get_the_excerpt())); ?></p>
			<?php endif; ?>
		</div>
	</a>
</li>
