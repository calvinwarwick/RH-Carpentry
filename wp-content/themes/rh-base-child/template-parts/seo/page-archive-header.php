<?php
/**
 * Page hero header (matches projects archive kicker / title / intro).
 *
 * @package RH_Base_Child
 *
 * @var string $kicker
 * @var string $title
 * @var string $subtitle
 * @var string $intro
 */

if (! defined('ABSPATH')) {
	exit;
}

$kicker   = isset($kicker) ? (string) $kicker : '';
$title    = isset($title) ? (string) $title : '';
$subtitle = isset($subtitle) ? (string) $subtitle : '';
$intro    = isset($intro) ? (string) $intro : '';

if ($title === '') {
	return;
}
?>
<header
	class="rh-archive-projects__header"
	data-rh-fx-group
	data-rh-fx-stagger="140"
	data-rh-fx-base="0"
>
	<?php if ($kicker !== '') : ?>
		<p class="rh-home-kicker rh-archive-projects__kicker" data-rh-fx="wipe" data-rh-fx-tone="dark">
			<span class="rh-home-kicker__line" aria-hidden="true"></span>
			<?php echo esc_html($kicker); ?>
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
</header>
