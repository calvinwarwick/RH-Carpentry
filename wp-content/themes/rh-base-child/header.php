<?php
/**
 * Theme header (home hero vs default parent header).
 *
 * @package RH_Base_Child
 */

if (! defined('ABSPATH')) {
	exit;
}
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php if (is_front_page()) : ?>
	<div class="rh-page-frame">
		<section class="rh-home-hero" aria-label="<?php esc_attr_e('Home hero', 'rh-base-child'); ?>">
			<?php get_template_part('template-parts/home/hero-home'); ?>
		</section>
		<main id="primary" class="site-main site-main--front">
<?php else : ?>
	<?php get_template_part('template-parts/header/site-header'); ?>
	<main id="primary" class="site-main">
<?php endif; ?>
