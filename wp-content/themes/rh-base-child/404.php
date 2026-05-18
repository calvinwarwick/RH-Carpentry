<?php
/**
 * 404 — off the level (carpentry-themed).
 *
 * @package RH_Base_Child
 */

get_header();
?>

<section class="rh-error-404" aria-labelledby="rh-error-404-heading">
	<div class="rh-error-404__panel">
		<div class="rh-error-404__grain" aria-hidden="true"></div>

		<div class="rh-error-404__scene" aria-hidden="true">
			<div class="rh-error-404__digits">
				<span class="rh-error-404__plank rh-error-404__plank--1">4</span>
				<span class="rh-error-404__plank rh-error-404__plank--2">0</span>
				<span class="rh-error-404__plank rh-error-404__plank--3">4</span>
			</div>
			<div class="rh-error-404__level">
				<div class="rh-error-404__level-body">
					<div class="rh-error-404__level-tube">
						<div class="rh-error-404__level-bubble"></div>
					</div>
				</div>
			</div>
			<ul class="rh-error-404__sawdust">
				<li></li>
				<li></li>
				<li></li>
				<li></li>
				<li></li>
				<li></li>
			</ul>
		</div>

		<div class="rh-error-404__copy">
			<p class="rh-error-404__kicker">
				<span class="rh-error-404__kicker-line" aria-hidden="true"></span>
				<?php esc_html_e('Page not found', 'rh-base-child'); ?>
			</p>
			<h1 class="rh-error-404__title" id="rh-error-404-heading">
				<?php esc_html_e('This page is off the level.', 'rh-base-child'); ?>
			</h1>
			<p class="rh-error-404__lede">
				<?php esc_html_e('The link might be crooked, the page may have moved, or someone measured twice and cut once in the wrong place. Either way — nothing to see here.', 'rh-base-child'); ?>
			</p>

			<nav class="rh-error-404__actions" aria-label="<?php esc_attr_e('Helpful links', 'rh-base-child'); ?>">
				<a class="rh-hero-btn rh-hero-btn--accent" href="<?php echo esc_url(home_url('/')); ?>">
					<?php esc_html_e('Back home', 'rh-base-child'); ?>
				</a>
				<a class="rh-hero-btn rh-hero-btn--muted" href="<?php echo esc_url(rh_carpentry_projects_archive_url()); ?>">
					<?php esc_html_e('View projects', 'rh-base-child'); ?>
				</a>
				<a class="rh-hero-btn rh-hero-btn--muted" href="<?php echo esc_url(rh_carpentry_contact_url()); ?>">
					<?php esc_html_e('Get in touch', 'rh-base-child'); ?>
				</a>
			</nav>
		</div>
	</div>
</section>

<?php
get_footer();
