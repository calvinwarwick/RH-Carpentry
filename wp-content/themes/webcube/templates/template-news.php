<?php

/**
 * Template Name: News Archive
 */

get_header();
?>

<section class="latest-news-section py-5 bg-white">
    <div class="container px-5">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center mb-3 mb-md-0">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/icon-newspaper.png" alt="Newspaper Icon" style="width: 55px; height: 55px;" class="me-3 fade-in-up">
                <div>
                    <h2 class="mb-1" style="font-family:Lexend,sans-serif;font-weight:700;color:#1398A5;">Latest news</h2>
                    <div style="font-size:1.1rem;color:#222;">Stay up to date with our latest updates.</div>
                </div>
            </div>
            <div><a href="<?php echo home_url(); ?>" class="d-none d-lg-block btn btn-primary"> <i class="fa-solid fa-chevron-left me-2"></i> Back</a></div>
        </div>
        <style>
            .page-numbers {
                display: flex;
                list-style: none;
                padding: 0;
                margin: 0;
                gap: 0.5rem;
                justify-content: center;
                align-items: center;
            }

            .page-numbers li {
                display: inline-block;
            }

            .page-numbers a,
            .page-numbers span {
                display: inline-block;
                padding: 0.5rem 1.2rem;
                border-radius: 30px 15px 30px 15px;
                background: #f3f3f3;
                color: #222;
                text-decoration: none;
                font-weight: 500;
                font-size: .8rem;
                transition: all 0.2s;
                box-shadow: 0 2px 8px rgba(19, 152, 165, 0.05);
            }

            .page-numbers a:hover {
                background: #13aab0;
                color: #fff;
            }

            .page-numbers .current,
            .page-numbers span.current {
                background: #13aab0;
                color: #fff;
                font-weight: 700;
                cursor: default;
            }

            .page-numbers .prev,
            .page-numbers .next {
                font-weight: 600;
                background: #f3f3f3;
                color: #222;
            }

            .page-numbers .prev:hover,
            .page-numbers .next:hover {
                background: #1398A5;
                color: #fff;
            }
        </style>
        <div class="row g-4">
            <?php
            $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
            $args = array(
                'post_type' => 'post',
                'posts_per_page' => 6,
                'paged' => $paged
            );
            $query = new WP_Query($args);

            if ($query->have_posts()) :
                while ($query->have_posts()) : $query->the_post();
                    $featured_image = get_the_post_thumbnail_url(get_the_ID(), 'large');
                    if (!$featured_image) {
                        $featured_image = 'https://webcube.uk/500-club/wp-content/uploads/2025/05/75502-2-1024x682.jpg';
                    }
            ?>
                    <div class="col-12 col-md-6 col-lg-4">
                        <a href="<?php the_permalink(); ?>" class="text-decoration-none">
                            <div class="news-card position-relative overflow-hidden">
                                <img src="<?php echo esc_url($featured_image); ?>" class="w-100 h-100 position-absolute top-0 start-0 object-fit-cover" alt="<?php the_title_attribute(); ?>" style="min-height:220px;">
                                <div class="news-card-overlay position-absolute top-0 start-0 w-100 h-100"></div>
                                <?php if (is_sticky() || (time() - get_the_date('U') < 7 * 24 * 60 * 60)) : ?>
                                    <div class="position-absolute top-0 end-0 m-3">
                                        <span class="badge px-3" style="border-radius: 30px 15px 30px 15px; background:#1398A5;font-weight:700;font-size:.8rem;">New</span>
                                    </div>
                                <?php endif; ?>
                                <div class="position-absolute bottom-0 start-0 p-4 w-100">
                                    <div style="font-size:0.95rem; color:#fff; opacity:0.85;"><?php echo get_the_date(); ?></div>
                                    <div style="font-size:1.25rem; font-weight:700; color:#fff;"><?php the_title(); ?></div>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php
                endwhile;
                ?>
                <div class="col-12 mt-5">
                    <div class="d-flex justify-content-center">
                        <?php
                        $big = 999999999;
                        echo paginate_links(array(
                            'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                            'format' => '?paged=%#%',
                            'current' => max(1, $paged),
                            'total' => $query->max_num_pages,
                            'prev_text' => '<i class="fas fa-chevron-left"></i>',
                            'next_text' => '<i class="fas fa-chevron-right"></i>',
                            'type' => 'list',
                            'class' => 'pagination'
                        ));
                        ?>
                    </div>
                </div>
            <?php
            else :
            ?>
                <div class="col-12">
                    <p>No news posts found.</p>
                </div>
            <?php
            endif;
            wp_reset_postdata();
            ?>
        </div>
    </div>
</section>

<style>
    .news-card {
        cursor: pointer;
        min-height: 220px;
        box-shadow: 5px 5px 0 rgba(19, 152, 165, 0.08);
        border-radius: 60px 30px 60px 30px;
        transition: box-shadow 0.2s, transform 0.2s;
    }

    .news-card:hover {
        box-shadow: 0 0px 0px 0 rgba(19, 152, 165, 0.18);
    }

    .news-card img {
        transition: scale 0.6s;
    }

    .news-card:hover img {
        scale: 1.05;
    }

    .news-card img.object-fit-cover {
        object-fit: cover;
        height: 100%;
    }

    .news-card-overlay {
        pointer-events: none;
        background: linear-gradient(to top, rgba(19, 152, 165, 1.85), rgba(19, 152, 165, 0.35));
    }

    .pagination {
        display: flex;
        list-style: none;
        padding: 0;
        margin: 0;
        gap: 0.5rem;
    }

    .pagination li {
        display: inline-block;
    }

    .pagination a,
    .pagination span {
        display: inline-block;
        padding: 0.5rem 1rem;
        border-radius: 30px 15px 30px 15px;
        background: #f3f3f3;
        color: #222;
        text-decoration: none;
        transition: all 0.2s;
    }

    .pagination a:hover {
        background: #13aab0;
        color: #fff;
    }

    .pagination .current {
        background: #13aab0;
        color: #fff;
    }

    .pagination .prev,
    .pagination .next {
        font-weight: 600;
    }
</style>

<?php
get_footer();
?>