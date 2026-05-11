<?php
/**
 * Homepage sections — about, stats strip, services (bento), clients marquee, testimonials.
 *
 * @package RH_Base_Child
 */

if (! defined('ABSPATH')) {
	exit;
}

$cta_contact = rh_carpentry_home_section_url('contact');

$about_section_image_id = (int) get_theme_mod('rh_about_section_image_id', 0);

$client_logos = rh_carpentry_client_logos_items();
$marquee_secs = count($client_logos) > 0
	? max(48, min(220, count($client_logos) * 5.25))
	: 80;

$testimonials = array(
	array(
		'quote'   => __('R H Carpenters kept the programme moving and delivered a first-class finish. The site ran cleanly and communication stayed clear throughout.', 'rh-base-child'),
		'name'    => __('Michael Thompson', 'rh-base-child'),
		'role'    => __('Site Manager', 'rh-base-child'),
		'company' => __('Regional Main Contractor', 'rh-base-child'),
	),
	array(
		'quote'   => __('From first fix through to final handover, every trade interaction was professional. The quality of workmanship has been excellent.', 'rh-base-child'),
		'name'    => __('Sarah Mitchell', 'rh-base-child'),
		'role'    => __('Project Lead', 'rh-base-child'),
		'company' => __('Residential Development', 'rh-base-child'),
	),
	array(
		'quote'   => __('They understood our brief immediately, solved issues early, and completed on time. A dependable team we would appoint again.', 'rh-base-child'),
		'name'    => __('David Chen', 'rh-base-child'),
		'role'    => __('Commercial Client', 'rh-base-child'),
		'company' => __('Essex', 'rh-base-child'),
	),
	array(
		'quote'   => __('Strong planning, reliable attendance, and real attention to detail. The joinery and finishing standards were consistently high.', 'rh-base-child'),
		'name'    => __('Rachel Owens', 'rh-base-child'),
		'role'    => __('Contracts Manager', 'rh-base-child'),
		'company' => __('Fit-Out Partner', 'rh-base-child'),
	),
	array(
		'quote'   => __('The team coordinated well with other trades and kept snagging to a minimum. Finish quality was exactly what we needed for a high-spec residential scheme.', 'rh-base-child'),
		'name'    => __('James Hartley', 'rh-base-child'),
		'role'    => __('Development Director', 'rh-base-child'),
		'company' => __('Private Developer', 'rh-base-child'),
	),
	array(
		'quote'   => __('Clear pricing, tidy site standards, and carpenters who actually turn up when they say they will. Refreshing to work with.', 'rh-base-child'),
		'name'    => __('Emma Patel', 'rh-base-child'),
		'role'    => __('Homeowner', 'rh-base-child'),
		'company' => __('Extension & loft', 'rh-base-child'),
	),
	array(
		'quote'   => __('They took ownership of the joinery package and pushed details forward before they became problems. Handover was straightforward.', 'rh-base-child'),
		'name'    => __('Tom Williams', 'rh-base-child'),
		'role'    => __('Site Agent', 'rh-base-child'),
		'company' => __('Regional Builder', 'rh-base-child'),
	),
	array(
		'quote'   => __('Excellent craftsmanship on bespoke storage and stair details. The client was delighted with the end result.', 'rh-base-child'),
		'name'    => __('Laura Brooks', 'rh-base-child'),
		'role'    => __('Interior Designer', 'rh-base-child'),
		'company' => __('Studio practice', 'rh-base-child'),
	),
	array(
		'quote'   => __('From structural work through to final decoration touch-ups, communication was steady and the programme stayed realistic.', 'rh-base-child'),
		'name'    => __('Mark Foster', 'rh-base-child'),
		'role'    => __('Project Manager', 'rh-base-child'),
		'company' => __('Commercial refurbishment', 'rh-base-child'),
	),
);
$home_services = array(
	array(
		'label' => __('Timber framed buildings', 'rh-base-child'),
		'slug'  => 'timber',
		'bento' => 'a',
	),
	array(
		'label' => __('Full refurbishment', 'rh-base-child'),
		'slug'  => 'refurbishment',
		'bento' => 'b',
	),
	array(
		'label' => __('Hand cut & trussed roofs', 'rh-base-child'),
		'slug'  => 'roofs',
		'bento' => 'c',
	),
	array(
		'label' => __('Complete new build projects', 'rh-base-child'),
		'slug'  => 'new-build',
		'bento' => 'd',
	),
	array(
		'label' => __('Barn conversions', 'rh-base-child'),
		'slug'  => 'barn',
		'bento' => 'e',
	),
	array(
		'label' => __('General Maintenance', 'rh-base-child'),
		'slug'  => 'maintenance',
		'bento' => 'f',
	),
	array(
		'label' => __('Extensions & loft conversions', 'rh-base-child'),
		'slug'  => 'extensions',
		'bento' => 'g',
	),
	array(
		'label' => __('Bespoke joinery & fitted furniture', 'rh-base-child'),
		'slug'  => 'joinery',
		'bento' => 'h',
	),
	array(
		'label' => __('Commercial fit-out & shopfitting', 'rh-base-child'),
		'slug'  => 'commercial',
		'bento' => 'i',
	),
);

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
	<div class="rh-home-about-container">
		<div class="rh-home-about__grid">
			<div class="rh-home-about__text-card">
				<div class="rh-home-about__text-content">
					<header class="rh-home-section__header rh-home-section__header--about">
						<p class="rh-home-kicker">
							<span class="rh-home-kicker__line" aria-hidden="true"></span>
							<?php esc_html_e('Who we are', 'rh-base-child'); ?>
						</p>
						<h2 class="rh-home-heading rh-home-heading--section" id="rh-home-about-heading"><?php esc_html_e('About us', 'rh-base-child'); ?></h2>
					</header>
					<div class="rh-home-about__body">
						<p class="rh-home-lede">
							<?php esc_html_e('We work closely with homeowners, developers and contractors to deliver reliable workmanship, attention to detail and projects completed to a high professional standard.', 'rh-base-child'); ?>
						</p>
						<p class="rh-home-lede">
							<?php esc_html_e('From full roof structures and timber framing to kitchen installations and complete property renovations, we take pride in every job we undertake.', 'rh-base-child'); ?>
						</p>
					</div>
					<div class="rh-home-about__actions rh-hero-actions">
						<a class="rh-hero-btn rh-hero-btn--accent" href="<?php echo esc_url($cta_contact); ?>"><?php esc_html_e('Get in touch', 'rh-base-child'); ?></a>
					</div>
				</div>
			</div>
			<div class="rh-home-about__media-card">
				<div class="rh-home-about__media">
					<?php
					if ($about_section_image_id > 0 && wp_attachment_is_image($about_section_image_id)) {
						$about_img_alt = (string) get_post_meta($about_section_image_id, '_wp_attachment_image_alt', true);
						echo wp_get_attachment_image(
							$about_section_image_id,
							'large',
							false,
							array(
								'class'    => 'rh-home-about__img',
								'loading'  => 'lazy',
								'decoding' => 'async',
								'sizes'    => '(max-width: 1000px) 100vw, (max-width: 1399px) 33vw, 33vw',
								'alt'      => $about_img_alt,
							)
						);
					} else {
						printf(
							'<img class="rh-home-about__img" src="%s" alt="" width="1200" height="800" loading="lazy" decoding="async" />',
							esc_url(rh_carpentry_get_about_section_image_url())
						);
					}
					?>
				</div>
			</div>
			<div class="rh-home-about__stats-wrap">
				<?php get_template_part('template-parts/home/hero-stats-strip'); ?>
			</div>
		</div>
	</div>
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
			<header class="rh-home-section__header rh-home-section__header--clients rh-home-section__header--projects rh-home-section__header--row">
				<div>
					<p class="rh-home-kicker">
						<span class="rh-home-kicker__line" aria-hidden="true"></span>
						<?php esc_html_e('Portfolio', 'rh-base-child'); ?>
					</p>
					<h2 class="rh-home-heading rh-home-heading--section" id="rh-home-projects-heading"><?php esc_html_e('Projects', 'rh-base-child'); ?></h2>
				</div>
				<?php
				$rh_projects_archive_url = get_post_type_archive_link('rh_project');
				if (! is_string($rh_projects_archive_url) || $rh_projects_archive_url === '') {
					$rh_projects_archive_url = home_url('/projects/');
				}
				?>
				<a class="rh-hero-btn rh-hero-btn--muted" href="<?php echo esc_url($rh_projects_archive_url); ?>"><?php esc_html_e('View all projects', 'rh-base-child'); ?></a>
			</header>
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
</section>
<?php endif; ?>

<div class="rh-bento-page">
	<section id="services" class="rh-home-section rh-home-section--features" aria-labelledby="rh-home-work-heading">
		<div class="rh-home-section__inner">
			<header class="rh-home-section__header rh-home-section__header--features rh-home-section__header--row">
				<div>
					<p class="rh-home-kicker">
						<span class="rh-home-kicker__line" aria-hidden="true"></span>
						<?php esc_html_e('What we offer', 'rh-base-child'); ?>
					</p>
					<h2 class="rh-home-heading rh-home-heading--section" id="rh-home-work-heading"><?php esc_html_e('Services', 'rh-base-child'); ?></h2>
				</div>
				<a class="rh-hero-btn rh-hero-btn--accent" href="<?php echo esc_url($cta_contact); ?>"><?php esc_html_e('Get in touch', 'rh-base-child'); ?></a>
			</header>
			<div class="rh-home-features rh-home-features--services" role="list">
				<?php foreach ($home_services as $service) : ?>
					<?php
					$service_bg_url = rh_carpentry_get_service_card_image_url($service['slug']);
					?>
					<article class="rh-home-feature rh-home-feature--service rh-home-service-bento--<?php echo esc_attr($service['bento']); ?>" role="listitem">
						<div class="rh-home-service-card__bg" style="background-image: url('<?php echo esc_url($service_bg_url); ?>');"></div>
						<div class="rh-home-service-card__overlay" aria-hidden="true"></div>
						<h3 class="rh-home-feature__title"><?php echo esc_html($service['label']); ?></h3>
					</article>
				<?php endforeach; ?>
			</div>
			<?php
			$credentials_base = get_stylesheet_directory_uri() . '/assets/images/credentials/';
			$credentials_dir  = get_stylesheet_directory() . '/assets/images/credentials/';
			$credentials      = array(
				array(
					'file'    => 'uk-fire-door-training.png',
					'alt'     => __('UK Fire Door Training — Approved Installer', 'rh-base-child'),
					'variant' => 'uk-fire-door',
				),
				array(
					'file'    => 'firequal.jpg',
					'alt'     => __('FireQual Approved Training Centre', 'rh-base-child'),
					'variant' => 'firequal',
				),
			);
			$credentials = array_values(
				array_filter(
					$credentials,
					static function ($row) use ($credentials_dir) {
						return is_readable($credentials_dir . $row['file']);
					}
				)
			);
			?>
			<?php if ($credentials !== array()) : ?>
				<aside class="rh-home-services-credentials" aria-label="<?php esc_attr_e('Accreditations', 'rh-base-child'); ?>">
					<ul class="rh-home-services-credentials__list">
						<?php foreach ($credentials as $cred) : ?>
							<?php
							$cred_path  = $credentials_dir . $cred['file'];
							$cred_url   = $credentials_base . $cred['file'];
							$cred_ver   = file_exists($cred_path) ? (string) filemtime($cred_path) : '';
							$cred_src   = $cred_ver !== '' ? add_query_arg('v', $cred_ver, $cred_url) : $cred_url;
							?>
							<li class="rh-home-services-credentials__item">
								<img
									class="rh-home-services-credentials__img rh-home-services-credentials__img--<?php echo esc_attr($cred['variant']); ?>"
									src="<?php echo esc_url($cred_src); ?>"
									alt="<?php echo esc_attr($cred['alt']); ?>"
									loading="lazy"
									decoding="async"
								/>
							</li>
						<?php endforeach; ?>
					</ul>
				</aside>
			<?php endif; ?>
		</div>
	</section>
</div>

<section class="rh-home-section rh-home-section--testimonials" aria-labelledby="rh-home-testimonials-heading">
	<div class="rh-clients-hero rh-testimonials-hero">
		<div class="rh-clients-hero__bg" aria-hidden="true"></div>
		<div class="rh-clients-hero__overlay" aria-hidden="true"></div>
		<div class="rh-clients-hero__inner">
			<header class="rh-home-section__header rh-home-section__header--clients rh-home-section__header--testimonials rh-home-section__header--row">
				<div>
					<p class="rh-home-kicker">
						<span class="rh-home-kicker__line" aria-hidden="true"></span>
						<?php esc_html_e('Customer feedback', 'rh-base-child'); ?>
					</p>
					<h2 class="rh-home-heading rh-home-heading--section" id="rh-home-testimonials-heading"><?php esc_html_e('Testimonials', 'rh-base-child'); ?></h2>
				</div>
				<a class="rh-hero-btn rh-hero-btn--muted" href="<?php echo esc_url($cta_contact); ?>"><?php esc_html_e('Get in touch', 'rh-base-child'); ?></a>
			</header>
			<?php
			$testimonial_count = count($testimonials);
			?>
			<div
				class="rh-home-testimonials-carousel"
				data-rh-testimonials-carousel
				data-interval="5000"
				data-at-start="true"
				data-at-end="<?php echo esc_attr($testimonial_count <= 1 ? 'true' : 'false'); ?>"
				role="region"
				aria-roledescription="<?php echo esc_attr(__('Carousel', 'rh-base-child')); ?>"
				aria-label="<?php echo esc_attr(__('Customer testimonials', 'rh-base-child')); ?>"
			>
				<div class="rh-home-testimonials-carousel__viewport-shell">
					<div class="rh-home-testimonials-carousel__viewport" tabindex="0">
						<div class="rh-home-testimonials-carousel__track" role="list">
						<?php foreach ($testimonials as $ti => $testimonial) : ?>
							<article
								class="rh-home-testimonial rh-bento-cell<?php echo 0 === $ti ? ' is-active' : ''; ?>"
								id="<?php echo esc_attr('rh-home-testimonial-' . $ti); ?>"
								role="listitem"
								data-rh-testimonial-slide
								data-rh-testimonial-index="<?php echo (int) $ti; ?>"
								aria-label="<?php
								echo esc_attr(
									sprintf(
										/* translators: 1: current slide number, 2: total slides */
										__('Testimonial %1$d of %2$d', 'rh-base-child'),
										$ti + 1,
										$testimonial_count
									)
								);
								?>"
							>
								<blockquote class="rh-home-testimonial__quote">
									<p><?php echo esc_html($testimonial['quote']); ?></p>
								</blockquote>
								<footer class="rh-home-testimonial__footer">
									<p class="rh-home-testimonial__name"><?php echo esc_html($testimonial['name']); ?></p>
									<p class="rh-home-testimonial__meta">
										<?php
										printf(
											/* translators: 1: role, 2: company */
											esc_html__('%1$s, %2$s', 'rh-base-child'),
											esc_html($testimonial['role']),
											esc_html($testimonial['company'])
										);
										?>
									</p>
								</footer>
							</article>
						<?php endforeach; ?>
						</div>
					</div>
				</div>
				<div class="rh-home-testimonials-carousel__bottom-bar">
					<?php if ($testimonial_count > 1) : ?>
						<button
							type="button"
							class="rh-home-testimonials-carousel__pause"
							data-rh-testimonial-autoplay-toggle
							data-label-pause="<?php echo esc_attr(__('Pause automatic slideshow', 'rh-base-child')); ?>"
							data-label-play="<?php echo esc_attr(__('Play automatic slideshow', 'rh-base-child')); ?>"
							aria-pressed="false"
							aria-label="<?php echo esc_attr(__('Pause automatic slideshow', 'rh-base-child')); ?>"
						>
							<svg class="rh-home-testimonials-carousel__pause-svg" viewBox="0 0 40 40" aria-hidden="true" focusable="false">
								<circle class="rh-home-testimonials-carousel__pause-track" cx="20" cy="20" r="17" fill="none" stroke="rgba(255,255,255,0.22)" stroke-width="2" />
								<circle
									class="rh-home-testimonials-carousel__pause-progress"
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
							<span class="rh-home-testimonials-carousel__pause-icon-wrap">
								<i class="fa-solid fa-pause" aria-hidden="true"></i>
							</span>
						</button>
					<?php endif; ?>
					<div class="rh-home-testimonials-carousel__arrows">
						<button
							type="button"
							class="rh-home-testimonials-carousel__arrow"
							data-rh-testimonial-prev
							aria-label="<?php echo esc_attr(__('Previous testimonial', 'rh-base-child')); ?>"
						>
							<i class="fa-solid fa-chevron-left" aria-hidden="true"></i>
						</button>
						<button
							type="button"
							class="rh-home-testimonials-carousel__arrow"
							data-rh-testimonial-next
							aria-label="<?php echo esc_attr(__('Next testimonial', 'rh-base-child')); ?>"
						>
							<i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<?php if ($client_logos !== array()) : ?>
<section class="rh-home-section rh-home-section--clients" aria-labelledby="rh-home-clients-heading">
	<div class="rh-home-clients-container">
		<header class="rh-home-section__header rh-home-section__header--clients rh-home-section__header--row">
			<div>
				<p class="rh-home-kicker">
					<span class="rh-home-kicker__line" aria-hidden="true"></span>
					<?php esc_html_e('Over 1,000+ Happy customers', 'rh-base-child'); ?>
				</p>
				<h2 class="rh-home-heading rh-home-heading--section" id="rh-home-clients-heading"><?php esc_html_e('Our Clients', 'rh-base-child'); ?></h2>
			</div>
			<a class="rh-hero-btn rh-hero-btn--accent" href="<?php echo esc_url($cta_contact); ?>"><?php esc_html_e('Get in touch', 'rh-base-child'); ?></a>
		</header>
		<div class="rh-client-marquee-panel">
			<div class="rh-client-marquee">
				<div class="rh-client-marquee__mask">
					<div
						class="rh-client-marquee__track"
						style="<?php echo esc_attr('--rh-marquee-duration: ' . (string) round($marquee_secs, 1) . 's'); ?>"
					>
						<?php foreach (array(false, true) as $is_duplicate) : ?>
						<ul class="rh-client-marquee__row"<?php echo $is_duplicate ? ' aria-hidden="true"' : ''; ?>>
							<?php foreach ($client_logos as $logo) : ?>
							<li class="rh-client-marquee__item">
								<img
									class="rh-client-marquee__img"
									src="<?php echo esc_url($logo['url']); ?>"
									alt="<?php echo esc_attr($logo['alt']); ?>"
									loading="lazy"
									decoding="async"
									width="180"
									height="90"
								/>
							</li>
							<?php endforeach; ?>
						</ul>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<?php elseif (current_user_can('edit_theme_options')) : ?>
<section class="rh-home-section rh-home-section--clients rh-home-section--clients-empty" aria-labelledby="rh-home-clients-heading-empty">
	<div class="rh-home-clients-container">
		<header class="rh-home-section__header rh-home-section__header--clients rh-home-section__header--row">
			<div>
				<p class="rh-home-kicker">
					<span class="rh-home-kicker__line" aria-hidden="true"></span>
					<?php esc_html_e('Over 1,000+ Happy customers', 'rh-base-child'); ?>
				</p>
				<h2 class="rh-home-heading rh-home-heading--section" id="rh-home-clients-heading-empty"><?php esc_html_e('Our Clients', 'rh-base-child'); ?></h2>
			</div>
			<a class="rh-hero-btn rh-hero-btn--accent" href="<?php echo esc_url($cta_contact); ?>"><?php esc_html_e('Get in touch', 'rh-base-child'); ?></a>
		</header>
		<div class="rh-client-marquee-panel">
			<p class="rh-client-marquee__hint">
				<?php esc_html_e('Add client logos under Appearance -> Customize -> Client logos (marquee).', 'rh-base-child'); ?>
			</p>
		</div>
	</div>
</section>
<?php endif; ?>
