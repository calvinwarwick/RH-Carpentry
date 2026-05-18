<?php
/**
 * Homepage sections — about, stats strip, services (bento), clients marquee, testimonials.
 *
 * @package RH_Base_Child
 */

if (! defined('ABSPATH')) {
	exit;
}

$cta_contact    = rh_carpentry_contact_url();
$home_services  = rh_carpentry_services();
$services_landing = function_exists('rh_carpentry_services_landing_url') ? rh_carpentry_services_landing_url() : home_url('/services/');

$about_section_image_id = (int) get_theme_mod('rh_about_section_image_id', 0);

$client_logos = rh_carpentry_client_logos_items();
$marquee_secs = count($client_logos) > 0
	? max(48, min(220, count($client_logos) * 5.25))
	: 80;

$rh_fire_credentials_dir  = get_stylesheet_directory() . '/assets/images/credentials/';
$rh_fire_credentials_base = get_stylesheet_directory_uri() . '/assets/images/credentials/';
$rh_fire_credentials       = array();
$rh_uk_fire_door_logo_id   = (int) get_theme_mod('rh_uk_fire_door_logo_id', 0);
$rh_uk_fire_door_alt       = __('UK Fire Door Training — Approved Installer, Inspector and Maintainer', 'rh-base-child');

if ($rh_uk_fire_door_logo_id > 0 && wp_attachment_is_image($rh_uk_fire_door_logo_id)) {
	$rh_fire_credentials[] = array(
		'attachment_id' => $rh_uk_fire_door_logo_id,
		'alt'           => $rh_uk_fire_door_alt,
	);
} elseif (is_readable($rh_fire_credentials_dir . 'uk-fire-door-training.png')) {
	$rh_fire_credentials[] = array(
		'file' => 'uk-fire-door-training.png',
		'alt'  => $rh_uk_fire_door_alt,
	);
}

if (is_readable($rh_fire_credentials_dir . 'firequal-logo.png')) {
	$rh_fire_credentials[] = array(
		'file' => 'firequal-logo.png',
		'alt'  => __('FireQual', 'rh-base-child'),
	);
}

$home_projects = array();
if (post_type_exists('rh_project')) {
	$home_projects_q = new WP_Query(
		array(
			'post_type'              => 'rh_project',
			'posts_per_page'         => 12,
			'post_status'            => 'publish',
			'ignore_sticky_posts'    => true,
			'orderby'                => 'date',
			'order'                  => 'DESC',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => true,
		)
	);
	if ($home_projects_q->have_posts()) {
		while ($home_projects_q->have_posts()) {
			$home_projects_q->the_post();
			$proj_id = get_the_ID();
			$sectors = array();
			if ( taxonomy_exists( 'rh_project_sector' ) ) {
				$t = wp_get_post_terms(
					$proj_id,
					'rh_project_sector',
					array(
						'fields' => 'names',
					)
				);
				if ( ! is_wp_error( $t ) ) {
					$sectors = $t;
				}
			}
			$badges = array_slice(
				array_values(
					array_filter(
						array_map(
							static function ( $n ) {
								return is_string( $n ) ? trim( $n ) : '';
							},
							$sectors
						)
					)
				),
				0,
				3
			);
			if ( $badges === array() ) {
				$badges = array(
					__( 'Carpentry', 'rh-base-child' ),
					__( 'Installation', 'rh-base-child' ),
				);
			}
			$home_projects[] = array(
				'id'       => $proj_id,
				'title'    => get_the_title(),
				'url'      => get_permalink(),
				'image_id' => (int) get_post_thumbnail_id(),
				'badges'   => $badges,
			);
		}
		wp_reset_postdata();
	}
}
?>

<section id="about" class="rh-home-section rh-home-section--about" aria-labelledby="rh-home-about-heading">
	<?php
	get_template_part(
		'template-parts/home/about-section-inner',
		null,
		array(
			'heading_id'             => 'rh-home-about-heading',
			'about_section_image_id' => $about_section_image_id,
			'rh_fire_credentials'    => $rh_fire_credentials,
			'rh_fire_credentials_dir'  => $rh_fire_credentials_dir,
			'rh_fire_credentials_base' => $rh_fire_credentials_base,
			'show_landing_link'      => false,
		)
	);
	?>
</section>

<?php if ($home_projects !== array()) : ?>
	<?php
	$home_projects_count    = count($home_projects);
	$home_projects_is_bento = $home_projects_count < 5;
	?>
<section id="projects" class="rh-home-section rh-home-section--projects<?php echo $home_projects_is_bento ? ' rh-home-section--projects--bento' : ''; ?>" data-count="<?php echo (int) $home_projects_count; ?>" aria-labelledby="rh-home-projects-heading">
	<div class="rh-clients-hero rh-testimonials-hero rh-projects-hero">
		<div class="rh-clients-hero__bg" aria-hidden="true"></div>
		<div class="rh-clients-hero__overlay" aria-hidden="true"></div>
		<div class="rh-clients-hero__inner">
			<header class="rh-home-section__header rh-home-section__header--clients rh-home-section__header--projects rh-home-section__header--row" data-rh-fx-group data-rh-fx-stagger="140">
				<div>
					<p class="rh-home-kicker" data-rh-fx="wipe" data-rh-fx-tone="light">
						<span class="rh-home-kicker__line" aria-hidden="true"></span>
						<?php esc_html_e('Portfolio', 'rh-base-child'); ?>
					</p>
					<h2 class="rh-home-heading rh-home-heading--section" id="rh-home-projects-heading" data-rh-fx="wipe" data-rh-fx-tone="light"><?php esc_html_e('Projects', 'rh-base-child'); ?></h2>
				</div>
				<?php
				$rh_projects_archive_url = get_post_type_archive_link('rh_project');
				if (! is_string($rh_projects_archive_url) || $rh_projects_archive_url === '') {
					$rh_projects_archive_url = home_url('/projects/');
				}
				?>
				<a class="rh-hero-btn rh-hero-btn--muted" href="<?php echo esc_url($rh_projects_archive_url); ?>" data-rh-fx="fade"><?php esc_html_e('View all projects', 'rh-base-child'); ?></a>
			</header>
			<div
				class="rh-home-section--projects__cards-fx"
				data-rh-fx-group
				data-rh-fx-stagger="82"
				data-rh-fx-base="1080"
			>
			<div
				class="rh-home-projects-carousel<?php echo $home_projects_is_bento ? ' rh-home-projects-carousel--bento' : ''; ?>"
				<?php if (! $home_projects_is_bento) : ?>data-rh-projects-carousel
				data-interval="5000"
				data-at-start="true"
				data-at-end="<?php echo esc_attr($home_projects_count <= 1 ? 'true' : 'false'); ?>"<?php endif; ?>
				data-count="<?php echo (int) $home_projects_count; ?>"
				role="region"
				<?php if (! $home_projects_is_bento) : ?>aria-roledescription="<?php echo esc_attr(__('Carousel', 'rh-base-child')); ?>"<?php endif; ?>
				aria-label="<?php echo esc_attr(__('Featured projects', 'rh-base-child')); ?>"
			>
				<div class="rh-home-projects-carousel__viewport" tabindex="0">
					<div class="rh-home-projects-carousel__track" role="list">
							<?php foreach ($home_projects as $pi => $proj) : ?>
								<?php
								$proj_img = '';
								if ( isset( $proj['image_url'] ) && is_string( $proj['image_url'] ) && $proj['image_url'] !== '' ) {
									$proj_img = $proj['image_url'];
								} elseif ( $proj['image_id'] > 0 ) {
									$from_id = wp_get_attachment_image_url( $proj['image_id'], 'large' );
									$proj_img = ( is_string( $from_id ) && $from_id !== '' ) ? $from_id : '';
								}
								$proj_badges = array();
								if ( isset( $proj['badges'] ) && is_array( $proj['badges'] ) ) {
									foreach ( $proj['badges'] as $b ) {
										if ( is_string( $b ) && $b !== '' ) {
											$proj_badges[] = $b;
										}
									}
									$proj_badges = array_slice( $proj_badges, 0, 4 );
								}
								?>
								<article
									class="rh-home-project-card rh-bento-cell<?php echo ($home_projects_is_bento || 0 === $pi) ? ' is-active' : ''; ?>"
									id="<?php echo esc_attr('rh-home-project-' . $pi); ?>"
									role="listitem"
									data-rh-fx="scale"
									<?php if (! $home_projects_is_bento) : ?>data-rh-project-slide
									data-rh-project-index="<?php echo (int) $pi; ?>"<?php endif; ?>
									data-rh-project-url="<?php echo esc_url( isset( $proj['url'] ) ? (string) $proj['url'] : '' ); ?>"
									aria-label="<?php
									echo esc_attr(
										sprintf(
											/* translators: 1: project title, 2: slide number, 3: total slides */
											__('%1$s — project %2$d of %3$d', 'rh-base-child'),
											$proj['title'],
											$pi + 1,
											$home_projects_count
										)
									);
									?>"
								>
									<span class="rh-home-project-card__cta" aria-hidden="true">
										<?php esc_html_e( 'Find out more', 'rh-base-child' ); ?>
										<i class="fa-solid fa-chevron-right rh-home-project-card__cta-icon" aria-hidden="true"></i>
									</span>
									<?php if ($proj_img !== '' && $proj_img !== false) : ?>
										<span class="rh-home-project-card__bg" style="background-image: url('<?php echo esc_url($proj_img); ?>');"></span>
									<?php else : ?>
										<span class="rh-home-project-card__bg rh-home-project-card__bg--placeholder" aria-hidden="true"></span>
									<?php endif; ?>
									<span class="rh-home-project-card__overlay" aria-hidden="true"></span>
									<div class="rh-home-project-card__text">
										<span class="rh-home-project-card__title"><?php echo esc_html( $proj['title'] ); ?></span>
										<?php if ( $proj_badges !== array() ) : ?>
											<ul class="rh-home-project-card__badges">
												<?php foreach ( $proj_badges as $badge_label ) : ?>
													<li>
														<span class="rh-home-project-card__badge"><?php echo esc_html( $badge_label ); ?></span>
													</li>
												<?php endforeach; ?>
											</ul>
										<?php endif; ?>
									</div>
								</article>
							<?php endforeach; ?>
					</div>
				</div>
				<?php if (! $home_projects_is_bento) : ?>
				<div class="rh-home-projects-carousel__bottom-bar">
					<?php if ($home_projects_count > 1) : ?>
						<button
							type="button"
							class="rh-home-projects-carousel__pause"
							data-rh-project-autoplay-toggle
							data-label-pause="<?php echo esc_attr(__('Pause automatic slideshow', 'rh-base-child')); ?>"
							data-label-play="<?php echo esc_attr(__('Play automatic slideshow', 'rh-base-child')); ?>"
							aria-pressed="false"
							aria-label="<?php echo esc_attr(__('Pause automatic slideshow', 'rh-base-child')); ?>"
						>
							<svg class="rh-home-projects-carousel__pause-svg" viewBox="0 0 40 40" aria-hidden="true" focusable="false">
								<circle class="rh-home-projects-carousel__pause-track" cx="20" cy="20" r="17" fill="none" stroke="rgba(255,255,255,0.22)" stroke-width="2" />
								<circle
									class="rh-home-projects-carousel__pause-progress"
									cx="20"
									cy="20"
									r="17"
									fill="none"
									stroke="rgba(255,255,255,0.92)"
									stroke-width="2"
									stroke-dasharray="106.814"
									stroke-dashoffset="106.814"
									stroke-linecap="round"
									transform="rotate(-90 20 20)"
								/>
							</svg>
							<span class="rh-home-projects-carousel__pause-icon-wrap">
								<i class="fa-solid fa-pause" aria-hidden="true"></i>
							</span>
						</button>
					<?php endif; ?>
					<div class="rh-home-projects-carousel__arrows">
						<button
							type="button"
							class="rh-home-projects-carousel__arrow"
							data-rh-project-prev
							aria-label="<?php echo esc_attr(__('Previous project', 'rh-base-child')); ?>"
						>
							<i class="fa-solid fa-chevron-left" aria-hidden="true"></i>
						</button>
						<button
							type="button"
							class="rh-home-projects-carousel__arrow"
							data-rh-project-next
							aria-label="<?php echo esc_attr(__('Next project', 'rh-base-child')); ?>"
						>
							<i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
						</button>
					</div>
				</div>
				<?php endif; ?>
			</div>
			</div>
		</div>
	</div>
</section>
<?php endif; ?>

<div class="rh-bento-page">
	<section id="services" class="rh-home-section rh-home-section--features" aria-labelledby="rh-home-work-heading">
		<?php
		get_template_part(
			'template-parts/home/services-section-inner',
			null,
			array(
				'heading_id'        => 'rh-home-work-heading',
				'home_services'     => $home_services,
				'show_landing_link' => false,
			)
		);
		?>
	</section>
</div>
<?php
rh_include_template_part(
	'template-parts/home/testimonials-section.php',
	array(
		'cta_contact' => $cta_contact,
		'heading_id'  => 'rh-home-testimonials-heading',
	)
);
rh_include_template_part(
	'template-parts/home/clients-section.php',
	array(
		'client_logos' => $client_logos,
		'marquee_secs' => $marquee_secs,
		'cta_contact'  => $cta_contact,
		'heading_id'   => 'rh-home-clients-heading',
	)
);
?>
