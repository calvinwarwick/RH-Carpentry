<?php
/**
 * Header template.
 *
 * @package RH_Base
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

<?php get_template_part('template-parts/header/site-header'); ?>

<main id="primary" class="site-main">
