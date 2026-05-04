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

<?php if (is_front_page()) : ?>
	<?php get_template_part('template-parts/home/contact-overlay'); ?>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
