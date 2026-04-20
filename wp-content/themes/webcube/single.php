<?php
get_header();
if (have_posts()) : while (have_posts()) : the_post();
        $featured_image = get_the_post_thumbnail_url(get_the_ID(), 'large');
        if (!$featured_image) {
            $featured_image = 'https://webcube.uk/500-club/wp-content/uploads/2025/05/75502-2-1024x682.jpg';
        }
?>
        <section class="latest-news-section py-5 bg-white">
            <div class="container px-2 px-lg-5">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="news-card position-relative overflow-hidden p-4 mb-4" style="border-radius: 60px 30px 60px 30px; background: #fff;">
                            <?php if ($featured_image) : ?>
                                <img src="<?php echo esc_url($featured_image); ?>" class="w-100 mb-4" alt="<?php the_title_attribute(); ?>" style="object-fit:cover; border-radius: 60px 30px 60px 30px; max-height: 400px;">
                            <?php endif; ?>
                            <div class="d-flex align-items-center mb-2">
                                <h1 class="mb-0 me-3" style="font-family:Lexend,sans-serif;font-weight:700;color:#1398A5; font-size:2.2rem;">
                                    <?php the_title(); ?>
                                </h1>
                            </div>
                            <div class="mb-4" style="color:#888;font-size:1.1rem;">
                                <?php echo get_the_date(); ?>
                            </div>
                            <div class="mb-4" style="font-size:1.15rem; color:#222; line-height:1.7;">
                                <?php the_content(); ?>
                            </div>
                            <a href="<?php echo esc_url(home_url('/latest-news/')); ?>" class="mt-5 btn btn-primary px-4 py-2 d-inline-flex align-items-center">
                                <i class="fa fa-chevron-left me-2"></i> Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
<?php endwhile;
endif;
get_footer();
