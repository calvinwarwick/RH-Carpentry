<?php
/**
 * Split page hero — copy left, photo right.
 *
 * @package RH_Base_Child
 *
 * @var string $pre_title  Line above the H1.
 * @var string $title
 * @var string $subtitle   Optional line below the H1.
 * @var string $intro
 * @var string $image_url
 * @var bool   $show_cta  When true, render CTAs under the intro.
 */

if (! defined('ABSPATH')) {
	exit;
}

$pre_title = isset($pre_title) ? (string) $pre_title : '';
$title     = isset($title) ? (string) $title : '';
$subtitle  = isset($subtitle) ? (string) $subtitle : '';
$intro     = isset($intro) ? (string) $intro : '';
$image_url = isset($image_url) ? (string) $image_url : '';
$show_cta  = isset($show_cta) && $show_cta;

if ($title === '') {
	return;
}

if ($image_url === '' && function_exists('rh_landing_hero_image_url')) {
	$image_url = rh_landing_hero_image_url();
}

$image_alt = $title;
?>
<header
	class="rh-page-hero-split rh-archive-projects__header"
	data-rh-fx-group
	data-rh-fx-stagger="140"
	data-rh-fx-base="0"
>
	<div class="rh-page-hero-split__content">
		<?php if ($pre_title !== '') : ?>
			<p class="rh-home-kicker rh-archive-projects__kicker" data-rh-fx="wipe" data-rh-fx-tone="dark">
				<span class="rh-home-kicker__line" aria-hidden="true"></span>
				<?php echo esc_html($pre_title); ?>
			</p>
		<?php endif; ?>
		<h1 class="page-title rh-home-heading rh-home-heading--section" data-rh-fx="wipe" data-rh-fx-tone="dark">
			<?php echo esc_html($title); ?>
		</h1>
		<?php if ($subtitle !== '') : ?>
			<p class="rh-archive-projects__subtitle" data-rh-fx="wipe" data-rh-fx-tone="dark">
				<?php echo esc_html($subtitle); ?>
			</p>
		<?php endif; ?>
		<?php if ($intro !== '') : ?>
			<p class="rh-archive-projects__intro" data-rh-fx="wipe" data-rh-fx-tone="dark">
				<?php echo esc_html($intro); ?>
			</p>
		<?php endif; ?>
		<?php if ($show_cta && function_exists('rh_landing_render_page_cta')) : ?>
			<?php rh_landing_render_page_cta(); ?>
		<?php endif; ?>
	</div>
	<?php if ($image_url !== '') : ?>
		<figure class="rh-page-hero-split__media" data-rh-fx="fade" data-rh-fx-tone="dark">
			<img
				class="rh-page-hero-split__img"
				src="<?php echo esc_url($image_url); ?>"
				alt="<?php echo esc_attr($image_alt); ?>"
				width="720"
				height="540"
				loading="eager"
				decoding="async"
			/>
		</figure>
	<?php endif; ?>
</header>
