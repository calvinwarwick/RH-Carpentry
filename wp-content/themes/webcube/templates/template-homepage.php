<?php

/**
 * Template Name: Homepage Template
 */

get_header();

// Get ACF fields
if (function_exists('get_field')) {
    $usps = get_field('usps');
    $first_section = get_field('first_section');
    $second_section = get_field('second_section');
    $join_our_club_section = get_field('join_our_club_section');
    $our_sponsors_section = get_field('our_sponsors_section');
    $our_sponsors = $our_sponsors_section['our_members'] ?? null;
    $our_partners_section = get_field('our_partners_section');
    $our_partners = $our_partners_section['our_partners'] ?? null;
} else {
    $usps = $first_section = $second_section = $join_our_club_section = $our_sponsors_section = $our_sponsors = $our_partners_section = $our_partners = null;
}

if (!function_exists('esc_attr')) {
    function esc_attr($text)
    {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
}
if (!function_exists('esc_html')) {
    function esc_html($text)
    {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
}
if (!function_exists('esc_url')) {
    function esc_url($url)
    {
        return filter_var($url, FILTER_SANITIZE_URL);
    }
}
if (!function_exists('wp_kses_post')) {
    function wp_kses_post($text)
    {
        return $text;
    }
}

if (!function_exists('get_template_directory_uri')) {
    function get_template_directory_uri()
    {
        return '/wp-content/themes/webcube';
    }
}
?>

<section class="bg-light py-3 section-fade">
    <div class="container-fluid p-0">
        <div class="row g-3 justify-content-center px-3 px-0">
            <?php if ($usps) : ?>
                <!-- USP #1 -->
                <div class="col-12 col-lg-4 col-sm-6 order-2 order-lg-1">
                    <div class="h-100 text-center p-4 bg-white animated-icon-container">
                        <div class="icon-video-wrapper fade-in-up" style="position:relative;">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/icon-hands.png" alt="Hands Icon" class="icon-fallback" style="position:absolute;top:0;left:0;width:100px;height:100px;object-fit:contain;z-index:1;">
                            <video class="animated-icon" width="100" height="100" preload="metadata" muted playsinline>
                                <source src="<?php echo get_template_directory_uri(); ?>/assets/video/icon-hands.mp4" type="video/mp4" />
                            </video>
                        </div>
                        <div class="fw-bold icon-card-text fade-in-up countup" data-countup="<?php echo esc_attr($usps['usp_1']['amount']); ?>" style="font-size:2rem; color:#169eaa;">
                            £<?php echo esc_html($usps['usp_1']['amount']); ?>+
                        </div>
                        <div class="mt-1 icon-card-text" style="font-size:1.2rem;">
                            <?php echo esc_html($usps['usp_1']['line_2']); ?>
                        </div>
                    </div>
                </div>
                <!-- USP #2 -->
                <div class="col-lg-4 col-sm-12 order-1 order-lg-2">
                    <div class="h-100 text-center p-4 bg-white animated-icon-container">
                        <div class="icon-video-wrapper fade-in-up">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/icon-piggy.png" alt="Piggy Icon" class="icon-fallback">
                            <video class="animated-icon" width="100" height="100" preload="metadata" muted playsinline>
                                <source src="<?php echo get_template_directory_uri(); ?>/assets/video/icon-piggy.mp4" type="video/mp4" />
                            </video>
                        </div>
                        <div class="icon-card-text" style="font-size:1.2rem;">
                            <?php echo esc_html($usps['usp_2']['line_1']); ?>
                        </div>
                        <div class="fw-bold icon-card-text fade-in-up" style="font-size:2.7rem; color:#169eaa;">
                            <?php echo esc_html($usps['usp_2']['line_2']); ?>
                        </div>
                    </div>
                </div>
                <!-- USP #3 -->
                <div class="col-12 col-lg-4 col-sm-6 order-3 order-lg-3">
                    <div class="h-100 text-center p-4 bg-white animated-icon-container">
                        <div class="icon-video-wrapper fade-in-up">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/icon-donations.png" alt="Donations Icon" class="icon-fallback">
                            <video class="animated-icon" width="100" height="100" preload="metadata" muted playsinline>
                                <source src="<?php echo get_template_directory_uri(); ?>/assets/video/icon-donations.mp4" type="video/mp4" />
                            </video>
                        </div>
                        <div class="fw-bold icon-card-text fade-in-up countup" data-countup="<?php echo esc_attr($usps['usp_3']['amount']); ?>" style="font-size:2rem; color:#169eaa;">
                            <?php echo esc_html($usps['usp_3']['amount']); ?>+
                        </div>
                        <div class="mt-1 icon-card-text" style="font-size:1.2rem;">
                            <?php echo esc_html($usps['usp_3']['line_2']); ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="py-5 feature-section section-fade" id="our-mission">
    <div class="container">
        <div class="row align-items-stretch px-3 px-lg-0">
            <!-- Feature Text Card -->
            <div class="col-lg-8 mb-4 mb-lg-0 order-1 order-lg-1 px-0">
                <div class="feature-card content d-flex flex-column justify-content-center p-0 h-100 mx-3 me-lg-5">
                    <div class="feature-card-header">
                        <span class="feature-icon">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/icon-hearts.png" alt="Hearts Icon" style="width: 62px; height: 62px;" class="mb-3 fade-in-up">
                        </span>
                        <h2 class="feature-title mb-2">
                            <?php echo esc_html($first_section['title']); ?>
                        </h2>
                    </div>
                    <div class="feature-description">
                        <?php echo wp_kses_post($first_section['description']); ?>
                    </div>
                    <?php if (!empty($first_section['enable_button'])) : ?>
                        <a class="btn btn-primary" href="<?php echo esc_url($first_section['button_link']); ?>">
                            <?php echo esc_html($first_section['button_text']); ?> <i class="ms-3 fas fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <!-- Feature Image -->
            <div class="col-lg-4 text-center order-2 order-lg-2 px-0">
                <div class="feature-image-wrapper px-3 px-lg-0 h-100 w-100">
                    <?php if (!empty($first_section['image'])) : ?>
                        <img src="<?php echo esc_url($first_section['image']); ?>" alt="Feature Image" class="feature-image w-100 h-100 fade-in-up" style="width: 220px; max-height: 600px;" />
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How you can help section (swapped columns) -->
<section class="py-5 feature-section section-fade" id="how-to-help">
    <div class="container">
        <div class="row align-items-stretch px-3 px-lg-0">
            <!-- Feature Text Card (now first on mobile) -->
            <div class="col-lg-8 mb-4 mb-lg-0 order-1 order-lg-2">
                <div class="feature-card content d-flex flex-column justify-content-center p-5 h-100 ms-lg-5">
                    <div class="feature-card-header">
                        <span class="feature-icon">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/icon-hands-alt.png" alt="Hands Icon" style="width: 62px; height: 62px;" class="mb-3 fade-in-up">
                        </span>
                        <h2 class="feature-title mb-2">
                            <?php echo esc_html($second_section['title']); ?>
                        </h2>
                    </div>
                    <div class="feature-description">
                        <?php echo wp_kses_post($second_section['description']); ?>
                    </div>
                    <?php if (!empty($second_section['enable_button'])) : ?>
                        <a class="btn btn-primary" href="<?php echo esc_url($second_section['button_link']); ?>">
                            <?php echo esc_html($second_section['button_text']); ?> <i class="ms-3 fas fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <!-- Feature Image (now second on mobile) -->
            <div class="col-lg-4 text-center order-2 order-lg-1">
                <div class="feature-image-wrapper h-100 w-100">
                    <?php if (!empty($second_section['image'])) : ?>
                        <img src="<?php echo esc_url($second_section['image']); ?>" alt="Feature Image" class="feature-image w-100 h-100 fade-in-up" />
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How to help section (text left, empty right) -->
<section class="py-5 feature-section section-fade bg-light" id="join-our-club">
    <div class="container py-5">
        <div class="row align-items-center px-3 px-lg-0">
            <!-- Feature Text Card (now first) -->
            <div class="col-lg-7 mb-4 mb-lg-0 order-1 order-lg-1">
                <div class="feature-card content d-flex flex-column justify-content-center p-5 h-100 me-lg-5">
                    <div class="feature-card-header">
                        <span class="feature-icon">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/icon-people-heart.png" alt="Hands Icon" style="width: 62px; height: 62px;" class="mb-3 fade-in-up">
                        </span>
                        <h2 class="feature-title mb-2">
                            <?php echo esc_html($join_our_club_section['title']); ?>
                        </h2>
                    </div>
                    <div class="feature-description">
                        <?php echo wp_kses_post($join_our_club_section['description']); ?>
                    </div>
                </div>
            </div>
            <!-- Right column: Donation form -->
            <div class="col-lg-5 d-flex align-items-center justify-content-center text-center order-2 order-lg-2">
                <div class="feature-image-wrapper h-100 w-100 d-flex align-items-center justify-content-center">
                    <form id="donate-form" class="donate-form p-3 w-100" style="margin:auto;">
                        <div class="donate-amounts mb-3">
                            <div class="row g-2">
                                <div class="col-4"><button type="button" class="donate-amount-btn active" data-amount="2.5">£2.50</button></div>
                                <div class="col-4"><button type="button" class="donate-amount-btn" data-amount="5">£5.00</button></div>
                                <div class="col-4"><button type="button" class="donate-amount-btn" data-amount="10">£10.00</button></div>
                                <div class="col-4"><button type="button" class="donate-amount-btn" data-amount="25">£25.00</button></div>
                                <div class="col-4"><button type="button" class="donate-amount-btn" data-amount="50">£50.00</button></div>
                                <div class="col-4"><button type="button" class="donate-amount-btn" data-amount="100">£100.00</button></div>
                            </div>
                            <div class="custom-amount-wrapper position-relative mt-2 d-none">
                                <input type="text" class="donate-amount-btn form-control donate-custom-amount" placeholder="Custom amount" style="text-align:center;" disabled />
                            </div>
                        </div>
                        <div class="donate-frequency mb-3 d-flex justify-content-center">
                            <button type="button" class="donate-frequency-btn active" data-frequency="monthly">Monthly</button>
                            <button type="button" class="donate-frequency-btn" data-frequency="yearly">Yearly</button>
                        </div>
                        <div class="donate-summary mb-3 py-3" style="font-size:1.1rem;">
                            You have chosen to donate <b>£2.50</b> <br> every <b>month</b> to the 500 club.
                        </div>
                        <button type="submit" class="btn btn-paypal btn-paypal-custom w-100 py-4">
                            Donate with <img src="https://www.paypalobjects.com/webstatic/icon/pp258.png" alt="PayPal" style="height:1.5em;vertical-align:middle;"> PayPal
                        </button>
                    </form>
                    <!-- Hidden PayPal forms for submission -->
                    <form id="paypal-monthly-form" action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank" style="display:none;">
                        <input type="hidden" name="cmd" value="_s-xclick">
                        <input type="hidden" name="hosted_button_id" value="BEGRMW87PNPY8">
                        <input type="hidden" name="on0" value="payment options">
                        <input type="hidden" name="os0" value="">
                        <input type="hidden" name="currency_code" value="GBP">
                    </form>
                    <form id="paypal-yearly-form" action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank" style="display:none;">
                        <input type="hidden" name="cmd" value="_s-xclick">
                        <input type="hidden" name="hosted_button_id" value="VRW93QL98TLCQ">
                        <input type="hidden" name="on0" value="payment options">
                        <input type="hidden" name="os0" value="">
                        <input type="hidden" name="currency_code" value="GBP">
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Donators & Sponsors Section -->
<section class="donators-section py-5 bg-light">
    <div class="container px-5 py-5">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center mb-3 mb-md-0">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/icon-group.png" alt="Group Icon" style="width: 55px; height: 55px;" class="me-3 fade-in-up">
                <div>
                    <h2 class="mb-1" style="font-family:Lexend,sans-serif;font-weight:700;color:#1398A5;">Our sponsors</h2>
                    <div style="font-size:1.1rem;color:#222;">Thank you to all the businesses supporting the 500 Club!</div>
                </div>
            </div>
        </div>
        <div class="donators-marquee-wrapper">
            <div class="donators-fade donators-fade-left"></div>
            <div class="donators-fade donators-fade-right"></div>
            <div class="marquee-row marquee-row-1 p-3">
                <?php if ($our_sponsors) : ?>
                    <?php foreach ($our_sponsors as $sponsor) : ?>
                        <a href="<?php echo esc_url($sponsor['business_url']); ?>" target="_blank" class="donator-card marquee-card bg-white p-3 text-center h-100 position-relative">
                            <?php if (!empty($sponsor['logo'])) : ?>
                                <img src="<?php echo esc_url($sponsor['logo']); ?>" alt="<?php echo esc_attr($sponsor['name']); ?>" class="marquee-logo p-0 mb-2" loading="eager" />
                            <?php endif; ?>
                            <div style="font-size:0.6rem;color:#888;line-height:1.2;">
                                since <?php echo esc_html($sponsor['member_since']); ?>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Latest News Section -->
<section class="latest-news-section py-5 bg-white">
    <div class="container px-5 py-5">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center mb-3 mb-md-0">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/icon-newspaper.png" alt="Newspaper Icon" style="width: 55px; height: 55px;" class="me-3 fade-in-up">
                <div>
                    <h2 class="mb-1" style="font-family:Lexend,sans-serif;font-weight:700;color:#1398A5;">Latest news</h2>
                    <div style="font-size:1.1rem;color:#222;">Stay up to date with our latest updates.</div>
                </div>
            </div>
            <a href="<?php echo esc_url(home_url('/latest-news/')); ?>" class="d-none d-md-block btn btn-primary px-4 py-2 d-flex align-items-center" style="border-radius: 60px 30px 60px 30px; box-shadow: 5px 5px 0 0 #1398A54D; font-weight:700; font-size:1.1rem;">
                View all <i class="fa fa-chevron-right ms-2"></i>
            </a>
        </div>
        <div class="row g-4">
            <?php
            $args = array(
                'post_type' => 'post',
                'posts_per_page' => 3
            );
            $query = new WP_Query($args);

            if ($query->have_posts()) :
                while ($query->have_posts()) : $query->the_post();
                    $featured_image = get_the_post_thumbnail_url(get_the_ID(), 'large');
                    if (!$featured_image) {
                        $featured_image = 'https://webcube.uk/500-club/wp-content/uploads/2025/05/75502-2-1024x682.jpg';
                    }
            ?>
                    <div class="col-lg-4">
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
                                    <div style="font-size:0.85rem; color:#fff; opacity:0.85;"><?php echo get_the_date(); ?></div>
                                    <div style="font-size:1.25rem; font-weight:700; color:#fff;"><?php the_title(); ?></div>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php
                endwhile;
                wp_reset_postdata();
            else :
                ?>
                <div class="col-12">
                    <p>No news posts found.</p>
                </div>
            <?php endif; ?>
        </div>
        <a href="<?php echo esc_url(home_url('/latest-news/')); ?>" class="mt-5 d-inline d-md-none btn btn-primary px-4 py-2 d-flex align-items-center justify-content-center" style="border-radius: 60px 30px 60px 30px; box-shadow: 5px 5px 0 0 #1398A54D; font-weight:700; font-size:1.1rem; width: auto;">
            View all news <i class="fa fa-chevron-right ms-3"></i>
        </a>
    </div>
</section>


<!-- Get in touch & Gift a subscription section -->
<section class="bg-light">
    <div class="container-fluid p-0">
        <div class="row m-0 align-items-stretch">
            <!-- Get in touch (form) -->
            <div class="col-lg-6 d-flex p-5 p-md-3 p-lg-5 justify-content-center">
                <div class="contact-card bg-light py-5 p-0 p-lg-5 d-flex flex-column justify-content-center" id="get-in-touch">
                    <div class="px-3">
                        <span class="feature-icon">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/icon-plane.png" alt="Hands Icon" style="width: 62px; height: 62px;" class="fade-in-up">
                        </span><br>
                        <h2 class="feature-title mb-2">Get in touch</h2>
                        <div class="mb-4 section-desc">
                            If you would like more information regarding the Palmer & Partners 500 club or it's membership please reach out.
                        </div>
                    </div>
                    <form id="contact-form" novalidate>
                        <div class="mb-3">
                            <input type="text" name="name" id="contact-name" class="form-control form-field" placeholder="Name" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <input type="tel" name="phone" id="contact-phone" class="form-control form-field" placeholder="Phone Number">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <input type="email" name="email" id="contact-email" class="form-control form-field" placeholder="Email" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-4">
                            <textarea name="message" id="contact-message" class="form-control form-field" rows="3" placeholder="Your message..." required></textarea>
                            <div class="invalid-feedback"></div>
                        </div>
                        <!-- Honeypot field for spam protection -->
                        <input type="text" name="website" style="display: none !important; position: absolute; left: -9999px;" tabindex="-1" autocomplete="off">
                        <button type="submit" id="contact-submit" class="btn btn-primary w-100 text-center d-flex justify-content-center align-items-center">
                            <span class="submit-text">Send message</span>
                            <span class="loading-text d-none">Sending...</span>
                            <span class="ms-2"><i class="fa fa-chevron-right"></i></span>
                        </button>
                        <div id="contact-success" class="alert alert-success mt-3 d-none" role="alert">
                            <i class="fa fa-check-circle me-2"></i>
                            <span class="success-message"></span>
                        </div>
                        <div id="contact-error" class="alert alert-danger mt-3 d-none" role="alert">
                            <i class="fa fa-exclamation-circle me-2"></i>
                            <span class="error-message"></span>
                        </div>
                    </form>
                </div>
            </div>

            <style>
                .bg-blue {
                    background: #e4f1f2 !important;
                }

                .btn.btn-primary.text-center {
                    justify-content: center !important;
                    text-align: center !important;
                    align-items: center !important;
                }
            </style>
            <!-- Gift a subscription -->
            <div class="col-lg-6 d-flex p-5 bg-blue justify-content-center position-relative overflow-hidden">
                <div class="contact-card p-0 p-lg-5 w-100">
                    <div class="mb-3">
                        <span class="feature-icon">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/icon-handshake.png" alt="Gift Icon" style="width: 62px; height: 62px;" class="fade-in-up">
                        </span><br>
                        <h2 class="section-title mb-2">Our partners</h2>
                        <div class="mb-4 section-desc">
                            We are proud to have the support of our partners.
                        </div>
                    </div>
                    <div class="card-wrapper">
                        <div class="row g-3">
                            <?php if ($our_partners) : ?>
                                <?php foreach ($our_partners as $partner) : ?>
                                    <div class="col-12">
                                        <a class="partner-card p-4 text-center mb-1 d-block" href="<?php echo esc_url($partner['business_url']); ?>" target="_blank" style=" text-decoration: none;">
                                            <?php if (!empty($partner['logo'])) : ?>
                                                <img class="partner-logo mb-1" style="max-width: 70%;max-height: 100px; transition: filter 0.3s;" src="<?php echo esc_url($partner['logo']); ?>" alt="<?php echo esc_attr($partner['name']); ?>">
                                            <?php endif; ?>
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</section>


<?php
get_footer();
?>

<style>
    .feature-section {
        background: #f7fcfd;
    }

    .feature-card.content {
        position: relative;
        z-index: 2;
    }

    .feature-card {
        position: relative;
        padding: 120px 80px !important;
        background: #1398A51A;
        border-radius: 180px 90px 180px 90px;
        margin-left: auto;
        margin-right: auto;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        z-index: 1;
    }

    .feature-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: #1398A51A;
        border-radius: 180px 90px 180px 90px;
        transform: rotate(4deg);
        z-index: -1;
    }

    .feature-icon {
        font-size: 1.7rem;
        color: #169eaa;
        margin-right: 0.5rem;
        display: flex;
        align-items: center;
    }

    .feature-title {
        font-size: 1.6rem;
        color: #169eaa;
        font-weight: 700;
    }

    .feature-description {
        color: #222;
        font-size: 1.08rem;
        line-height: 1.6;
        margin-bottom: 1.5rem;
    }

    .feature-image-wrapper {
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 10px;
        overflow: visible;
    }

    .feature-image {
        height: 100%;
        width: auto;
        max-width: 100%;
        object-fit: cover;
        border-radius: 120px 60px 120px 60px;
        box-shadow: 5px 5px 0 rgba(11, 11, 11, 0.10);
    }

    @media (max-width: 991.98px) {
        .feature-card {
            max-width: 100%;
            padding: 50px !important;
            border-radius: 70px 35px 70px 35px;
        }

        .feature-card::before {
            border-radius: 70px 35px 70px 35px;
            transform: rotate(2deg) !important;
        }

        .feature-image {
            border-radius: 70px 35px 70px 35px;
        }
    }

    .donate-amount-btn,
    .donate-custom-amount,
    .donate-frequency-btn,
    .btn-paypal {
        border-radius: 30px 15px 30px 15px !important;
    }

    .donate-amount-btn {
        width: 100%;
        background: #f3f3f3;
        border: none;
        padding: .8em 0;
        font-weight: 600;
        color: #222;
        font-size: 1.2rem;
        transition: background 0.2s, color 0.2s;
    }

    .donate-amount-btn.active,
    .donate-amount-btn:focus {
        background: #13aab0;
        color: #fff;
        outline: none;
    }

    .donate-custom-amount {
        background: #f3f3f3;
        border: none;
        color: #222;
        font-size: 1.2rem;
        padding: .8em 0;
        box-shadow: none;
        height: 3.2rem;
        cursor: pointer;
    }

    .donate-custom-amount::placeholder {
        color: #222;
    }

    .donate-frequency-btn {
        flex: 1;
        background: #f3f3f3;
        border: none;
        padding: .8em 0;
        font-weight: 600;
        color: #222;
        font-size: 1.2rem;
        margin: 0 0.2em;
        transition: background 0.2s, color 0.2s;
    }

    .donate-frequency-btn.active,
    .donate-frequency-btn:focus {
        background: #13aab0;
        color: #fff;
        outline: none;
    }

    .btn-paypal-custom {
        background: #FFCC29;
        color: #222;
        font-weight: 600;
        font-size: 1.2rem;
        padding: 0.8rem 0;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        box-shadow: 5px 5px 0px rgba(255, 204, 41, 0.25);
    }

    .btn-paypal-custom:hover {
        background: #ffe066;
        box-shadow: 0 0 0 rgba(255, 204, 41, 0.25);
    }

    .donate-custom-amount::-webkit-outer-spin-button,
    .donate-custom-amount::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .donate-custom-amount[type=number] {
        -moz-appearance: textfield;
    }

    .donate-custom-amount:focus,
    .donate-custom-amount.active,
    .donate-custom-amount:not(:placeholder-shown) {
        background: #13aab0;
        color: #fff;
        outline: none;
    }

    .donate-custom-amount:focus::placeholder,
    .donate-custom-amount.active::placeholder,
    .donate-custom-amount:not(:placeholder-shown)::placeholder {
        color: #e0f7fa;
        opacity: 1;
    }

    .donate-summary {
        font-size: 1.3rem !important;
    }

    @media (max-width: 575.98px) {
        .donate-summary {
            font-size: 1rem !important;
        }
    }

    .custom-amount-wrapper {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .custom-amount-pound {
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-100%, -50%);
        font-size: 1.2rem;
        color: #fff;
        pointer-events: none;
        z-index: 2;
        display: none;
    }

    .custom-amount-wrapper.has-value .custom-amount-pound {
        display: block;
    }

    .custom-amount-wrapper input {
        text-align: center !important;
    }

    .news-card {
        cursor: pointer;
        min-height: 300px;
        box-shadow: 5px 5px 0 rgba(19, 152, 165, 0.08);
        border-radius: 60px 30px 60px 30px;
        transition: box-shadow 0.2s;
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

    .donators-section {
        overflow: hidden;
        width: 100vw;
        position: relative;
        left: 50%;
        right: 50%;
        margin-left: -50vw;
        margin-right: -50vw;
        background: #f7fcfd !important;
        z-index: 1;
        user-select: none;
    }

    .donators-fade {
        position: absolute;
        top: 0;
        width: 20vw;
        height: 100%;
        pointer-events: none;
        z-index: 3;
    }

    .donators-fade-left {
        left: 0;
        background: linear-gradient(to right, #f7fcfd 0%, rgba(247, 252, 253, 0) 100%);
    }

    .donators-fade-right {
        right: 0;
        background: linear-gradient(to left, #f7fcfd 0%, rgba(247, 252, 253, 0) 100%);
    }

    .marquee-row {
        display: flex;
        gap: 2.5rem;
        width: max-content;
        align-items: stretch;
        padding: 1.5rem 0;
        will-change: transform;
        position: relative;
    }

    .donator-card {
        border-radius: 30px 15px 30px 15px;
        box-shadow: 5px 5px 0 rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        background: #fff;
    }

    .donator-card:hover {
        /* transform: translateY(-5px); */
        box-shadow: 0px 0px 0 rgba(19, 152, 165, 0.1);
        background: #ededed !important;
        /* outline: 4px solid #1398A5; */
    }

    .donator-card:hover .marquee-logo {
        filter: none;
    }

    .donators-marquee-wrapper:hover .donator-card:not(:hover) {
        /* opacity: 0.2; */
    }

    .marquee-card {
        min-width: 180px;
        max-width: 250px;
        height: 110px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-start;
        box-sizing: border-box;
        position: relative;
        padding: 1rem;
    }

    .marquee-logo {
        height: 50px;
        width: auto;
        max-width: 120px;
        object-fit: contain;
        border-radius: 18px;
        padding: 0.5rem 1.2rem;
        display: block;
        transition: transform 0.3s ease, filter 0.3s ease;
        /* filter: brightness(0) contrast(0); */
    }


    @media (max-width: 991.98px) {
        .marquee-card {
            min-width: 140px;
            max-width: 140px;
            width: 140px;
            height: 100px;
        }

        .marquee-logo {
            height: 35px;
            max-width: 80px;
            padding: 0.3rem 0.7rem;
        }
    }

    .icon-lg {
        font-size: 2.2rem;
        color: #169eaa;
        display: flex;
        align-items: center;
    }

    .section-title {
        font-weight: 700;
        color: #169eaa;
        font-size: 2rem;
    }

    .section-desc {
        font-size: 1.08rem;
        color: #222;
    }

    .section-desc-bold {
        font-size: 1.08rem;
        font-weight: 600;
        color: #222;
    }

    .contact-card,
    .gift-card {
        min-height: 100%;
        position: relative;
        max-width: 550px;
    }

    .form-field {
        border-radius: 40px 20px 40px 20px !important;
        box-shadow: 2px 2px 0 #e0e0e0;
        font-size: 1.1rem;
        padding: 1.1rem 1.5rem;
        border: 2px solid #d0e3e6;
        background: #f7f7f7;
        margin-bottom: 0.5rem;
        outline: none;
        box-sizing: border-box;
        transition: border-color 0.2s, background 0.2s;
    }

    .form-field:focus {
        border: 2px solid #169eaa;
        background: #fff;
        outline: none;
    }

    input.form-field::-webkit-input-placeholder,
    textarea.form-field::-webkit-input-placeholder {
        color: #888;
    }

    input.form-field:-ms-input-placeholder,
    textarea.form-field:-ms-input-placeholder {
        color: #888;
    }

    input.form-field::placeholder,
    textarea.form-field::placeholder {
        color: #888;
    }

    .send-btn {
        background: #169eaa;
        color: #fff;
        font-weight: 700;
        font-size: 1.2rem;
        border-radius: 32px;
        box-shadow: 6px 6px 0 #b2e6ea;
        padding: 1rem 0;
        transition: background 0.2s, box-shadow 0.2s;
    }

    .send-btn:hover {
        background: #13aab0;
        box-shadow: 0 0 0 #b2e6ea;
    }

    .gift-card {
        overflow: hidden;
    }

    .gift-bg-faded-wrapper {
        position: absolute;
        rotate: 17deg;
        opacity: 0.05;
        bottom: -100px;
        z-index: 0;
        pointer-events: none;
    }

    .gift-bg-faded {
        opacity: 0.05;
        transform: rotate(17deg);
        width: 100%;
        height: auto;
        transition: width 0.2s;
    }

    @media (max-width: 768px) {
        .gift-bg-faded {
            width: 50%;
            right: 0 !important;
        }
    }

    .animated-icon-container {
        transition: none;
    }

    .animated-icon-container:hover {
        transform: none;
    }

    .scroll-down-circle {
        width: 40px;
        height: 40px;
        background: #169eaa;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        margin-bottom: 100px;
        animation: chevron-bounce 5s infinite cubic-bezier(0.4, 0, 0.2, 1);
        animation-delay: 2s;
    }

    .chevron-animate {
        color: #fff;
        font-size: 1rem;
        transform: none !important;
    }

    @keyframes chevron-bounce {
        0% {
            transform: translateY(0);
        }

        10% {
            transform: translateY(58px) scale(.7);
        }

        40% {
            transform: translateY(0) scale(1);
        }

        100% {
            transform: translateY(0);
        }
    }

    .partner-card {
        position: relative;
        overflow: hidden;
        background: #333 !important;
        border-radius: 40px 20px 40px 20px;
        transition: 0.3s;
        box-shadow: 5px 5px 0 rgba(19, 152, 165, 0.1);
    }

    .partner-card:hover,
    .partner-card:focus {
        background: #222 !important;
        box-shadow: 0 0 0 rgba(19, 152, 165, 0.1);
    }

    .partner-card .partner-logo {
        transition: .3s all ease !important;
    }


    .partner-card:hover .partner-logo,
    .partner-card:focus .partner-logo {
        /* filter: brightness(0) grayscale(1) contrast(1.5) !important; */
        scale: 1.05;
    }

    .partner-card:hover .partner-link-text,
    .partner-card:focus .partner-link-text {
        color: #fff !important;
    }

    .btn-sm-70 {
        font-size: 0.7rem;
        padding: 0.2rem 0.4rem;
        border-radius: 0.25rem;
        line-height: 1.2;
        min-height: auto;
    }

    /* Contact Form Styles */
    .form-control.is-invalid {
        border-color: #dc3545;
        background-color: #fff5f5;
    }

    .invalid-feedback {
        display: block;
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.875rem;
        color: #dc3545;
    }

    .alert {
        border-radius: 40px 20px 40px 20px;
        border: none;
        font-weight: 500;
    }

    .alert-success {
        background-color: #d1edff;
        color: #0c5460;
        border-left: 4px solid #1398A5;
    }

    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
        border-left: 4px solid #dc3545;
    }

    #contact-submit:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .loading-text {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .loading-text::after {
        content: '';
        width: 16px;
        height: 16px;
        margin-left: 8px;
        border: 2px solid #ffffff;
        border-top: 2px solid transparent;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
</style>

<script>
    // Count-up animation for numbers (smooth with ease-out cubic)
    function easeOutCubic(t) {
        return 1 - Math.pow(1 - t, 3);
    }

    function animateCountUp(el, target, prefix = '', suffix = '+', duration = 1800) {
        let start = 0;
        let startTime = null;

        function step(ts) {
            if (!startTime) startTime = ts;
            let progress = Math.min((ts - startTime) / duration, 1);
            let eased = easeOutCubic(progress);
            let value = Math.floor(eased * (target - start) + start);
            el.textContent = prefix + value.toLocaleString() + suffix;
            if (progress < 1) {
                requestAnimationFrame(step);
            } else {
                el.textContent = prefix + target.toLocaleString() + suffix;
            }
        }
        requestAnimationFrame(step);
    }

    function countUpObserver() {
        const els = document.querySelectorAll('.countup[data-countup]');
        els.forEach(el => {
            let target = parseInt(el.getAttribute('data-countup'), 10);
            let prefix = '';
            let suffix = '+';
            if (el.textContent.trim().startsWith('£')) prefix = '£';
            if (!el.textContent.trim().endsWith('+')) suffix = '';
            if ('IntersectionObserver' in window) {
                const obs = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            animateCountUp(el, target, prefix, suffix);
                            obs.unobserve(entry.target);
                        }
                    });
                }, {
                    threshold: 0.6
                });
                obs.observe(el);
            } else {
                animateCountUp(el, target, prefix, suffix);
            }
        });
    }
    document.addEventListener('DOMContentLoaded', countUpObserver);

    document.addEventListener('DOMContentLoaded', function() {
        // Amount selection
        const amountBtns = document.querySelectorAll('.donate-amount-btn');
        const customAmount = document.querySelector('.donate-custom-amount');
        const customAmountWrapper = document.querySelector('.custom-amount-wrapper');
        let selectedAmount = 2.5;
        let selectedFrequency = 'monthly';

        amountBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                amountBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                // customAmount.value = '';
                selectedAmount = parseFloat(this.getAttribute('data-amount'));
                updateSummary();
            });
        });

        function formatCurrency(num) {
            if (isNaN(num) || num === '' || num === null) return '';
            return '£' + Number(num).toLocaleString('en-GB', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        // Frequency selection
        const freqBtns = document.querySelectorAll('.donate-frequency-btn');
        freqBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                freqBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                selectedFrequency = this.getAttribute('data-frequency');
                updateSummary();
            });
        });
        // Update summary
        function updateSummary() {
            const summary = document.querySelector('.donate-summary');
            if (selectedAmount && selectedFrequency) {
                summary.innerHTML = `You have chosen to donate <b>${formatCurrency(selectedAmount)}</b> <br>every <b>${selectedFrequency === 'monthly' ? 'month' : 'year'}</b> to the 500 club.`;
            } else {
                summary.innerHTML = 'Please enter an amount.<br><br>';
            }
        }
        // PayPal integration for preset options
        document.getElementById('donate-form').addEventListener('submit', function(e) {
            e.preventDefault();
            // Map your custom selection to PayPal options
            // These must match the PayPal button options exactly!
            const amount = selectedAmount;
            const frequency = selectedFrequency;
            let optionValue = '';

            if (frequency === 'monthly') {
                if (amount == 2.5) optionValue = 'Option 1';
                else if (amount == 5) optionValue = 'Option 2';
                else if (amount == 10) optionValue = 'Option 3';
                else if (amount == 25) optionValue = 'Option 4';
                else if (amount == 50) optionValue = 'Option 5';
                else if (amount == 100) optionValue = 'Option 6';
            } else if (frequency === 'yearly') {
                if (amount == 2.5) optionValue = 'Option 1';
                else if (amount == 5) optionValue = 'Option 2';
                else if (amount == 10) optionValue = 'Option 3';
                else if (amount == 25) optionValue = 'Option 4';
                else if (amount == 50) optionValue = 'Option 5';
                else if (amount == 100) optionValue = 'Option 6';
            }

            if (!optionValue) {
                alert('Please select a valid amount and frequency that matches a PayPal option.');
                return;
            }

            // Set the value in the appropriate hidden form and submit
            const paypalForm = frequency === 'monthly' ?
                document.getElementById('paypal-monthly-form') :
                document.getElementById('paypal-yearly-form');
            paypalForm.os0.value = optionValue;
            paypalForm.submit();
        });
    });

    // Marquee scrolling for member cards
    function setupMemberMarquee() {
        const marqueeRow = document.querySelector('.marquee-row-1');
        if (!marqueeRow) return;

        // Get all existing member cards
        const memberCards = marqueeRow.querySelectorAll('.donator-card');
        if (memberCards.length === 0) return;

        // Clone cards for continuous scrolling
        memberCards.forEach(card => {
            const clone = card.cloneNode(true);
            marqueeRow.appendChild(clone);
        });

        // Clone again for seamless loop
        memberCards.forEach(card => {
            const clone = card.cloneNode(true);
            marqueeRow.appendChild(clone);
        });

        // Animation variables
        let pos = 0;
        let isPaused = false;
        const cardWidth = memberCards[0].offsetWidth + parseFloat(getComputedStyle(marqueeRow).gap || 0);
        const totalWidth = cardWidth * memberCards.length;
        let animationFrame;
        const baseSpeed = window.innerWidth <= 768 ? 1.2 : 0.5;

        // Add hover pause functionality
        marqueeRow.addEventListener('mouseenter', () => {
            isPaused = true;
            cancelAnimationFrame(animationFrame);
        });

        marqueeRow.addEventListener('mouseleave', () => {
            isPaused = false;
            animate();
        });

        const animate = () => {
            if (!isPaused) {
                pos -= baseSpeed;
                marqueeRow.style.transform = `translateX(${pos}px)`;

                // Reset position when we've scrolled past the first set
                if (Math.abs(pos) >= totalWidth) {
                    pos = 0;
                }
            }
            animationFrame = requestAnimationFrame(animate);
        };

        // Start animation
        animate();
    }

    // Initialize marquee when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        // Wait a bit for any dynamic content to load
        setTimeout(setupMemberMarquee, 500);
    });

    // Contact Form Handler
    document.addEventListener('DOMContentLoaded', function() {
        const contactForm = document.getElementById('contact-form');
        const submitBtn = document.getElementById('contact-submit');
        const submitText = submitBtn.querySelector('.submit-text');
        const loadingText = submitBtn.querySelector('.loading-text');
        const successAlert = document.getElementById('contact-success');
        const errorAlert = document.getElementById('contact-error');
        const successMessage = successAlert.querySelector('.success-message');
        const errorMessage = errorAlert.querySelector('.error-message');

        if (contactForm) {
            contactForm.addEventListener('submit', function(e) {
                e.preventDefault();

                // Hide previous alerts
                successAlert.classList.add('d-none');
                errorAlert.classList.add('d-none');

                // Clear previous validation states
                const formFields = contactForm.querySelectorAll('.form-control');
                formFields.forEach(field => {
                    field.classList.remove('is-invalid');
                    const feedback = field.parentNode.querySelector('.invalid-feedback');
                    if (feedback) feedback.textContent = '';
                });

                // Validate form
                if (!validateForm()) {
                    return;
                }

                // Show loading state
                submitBtn.disabled = true;
                submitText.classList.add('d-none');
                loadingText.classList.remove('d-none');

                // Prepare form data
                const formData = new FormData(contactForm);

                // Send AJAX request
                fetch('/contact-form.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            // Success
                            successMessage.textContent = data.message;
                            successAlert.classList.remove('d-none');
                            contactForm.reset();

                            // Scroll to success message
                            successAlert.scrollIntoView({
                                behavior: 'smooth',
                                block: 'center'
                            });
                        } else {
                            // Error - show field validation but no error message
                            // Show field-specific errors if available
                            if (data.errors && Array.isArray(data.errors)) {
                                data.errors.forEach(error => {
                                    // Try to match error to specific field
                                    if (error.toLowerCase().includes('name')) {
                                        showFieldError('contact-name', '');
                                    } else if (error.toLowerCase().includes('email')) {
                                        showFieldError('contact-email', '');
                                    } else if (error.toLowerCase().includes('message')) {
                                        showFieldError('contact-message', '');
                                    }
                                });
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        // No error message displayed to user
                    })
                    .finally(() => {
                        // Reset button state
                        submitBtn.disabled = false;
                        submitText.classList.remove('d-none');
                        loadingText.classList.add('d-none');
                    });
            });

            function validateForm() {
                let isValid = true;
                const name = document.getElementById('contact-name').value.trim();
                const email = document.getElementById('contact-email').value.trim();
                const message = document.getElementById('contact-message').value.trim();

                // Validate name
                if (!name) {
                    showFieldError('contact-name', '');
                    isValid = false;
                }

                // Validate email
                if (!email) {
                    showFieldError('contact-email', '');
                    isValid = false;
                } else if (!isValidEmail(email)) {
                    showFieldError('contact-email', '');
                    isValid = false;
                }

                // Validate message
                if (!message) {
                    showFieldError('contact-message', '');
                    isValid = false;
                }

                return isValid;
            }

            function showFieldError(fieldId, message) {
                const field = document.getElementById(fieldId);
                const feedback = field.parentNode.querySelector('.invalid-feedback');

                field.classList.add('is-invalid');
                if (feedback) {
                    feedback.textContent = ''; // Always empty message
                }
            }

            function isValidEmail(email) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email);
            }

            // Clear validation on input
            const formFields = contactForm.querySelectorAll('.form-control');
            formFields.forEach(field => {
                field.addEventListener('input', function() {
                    this.classList.remove('is-invalid');
                    const feedback = this.parentNode.querySelector('.invalid-feedback');
                    if (feedback) feedback.textContent = '';
                });
            });
        }
    });
</script>