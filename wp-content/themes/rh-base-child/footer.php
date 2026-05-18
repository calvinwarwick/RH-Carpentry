<?php
/**
 * Theme footer.
 *
 * @package RH_Base_Child
 */

if (! defined('ABSPATH')) {
	exit;
}
?>

<?php if (is_front_page()) : ?>
	</main><!-- #primary -->
	</div><!-- .rh-page-frame -->
<?php else : ?>
	</main><!-- #primary -->
<?php endif; ?>

<?php get_template_part('template-parts/footer/site-footer-full'); ?>

<?php
get_template_part('template-parts/overlays/about-overlay');
get_template_part('template-parts/overlays/services-overlay');
get_template_part('template-parts/home/contact-overlay');
?>

<?php wp_footer(); ?>
</body>
</html>
