<?php

/**
 * Template Name: Custom Page Template
 * Description: A custom page template for additional pages
 */

get_header();

// Get ACF fields
if (function_exists('get_field')) {
    $main_title = get_field('main_title');
    $main_description = get_field('main_description');
    $page_sections = get_field('page_sections');
} else {
    $main_title = $main_description = $page_sections = null;
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

the_post();
?>

<!-- Page Content -->
<div class="container">
    <div class="row">
        <div class="col-12 my-4 p-5">
            <div id="post-<?php the_ID(); ?>" <?php post_class('content'); ?>>
                <?php if (get_field('main_icon')) : ?>
                    <img src="<?php echo esc_url(get_field('main_icon')['url']); ?>" alt="<?php echo esc_attr(get_field('main_icon')['alt']); ?>" style="width: 62px; height: 62px;" class="mb-3 fade-in-up visible">
                <?php endif; ?>
                <?php if ($main_title) : ?>
                    <h1 class="entry-title"><?php echo esc_html($main_title); ?></h1>
                <?php else : ?>
                    <h1 class="entry-title"><?php the_title(); ?></h1>
                <?php endif; ?>

                <?php if ($main_description) : ?>
                    <div class="main-description mb-4">
                        <?php echo wp_kses_post($main_description); ?>
                    </div>
                <?php endif; ?>

            </div><!-- /#post-<?php the_ID(); ?> -->
            <?php
            // If comments are open or we have at least one comment, load up the comment template.
            if (comments_open() || get_comments_number()) {
                comments_template();
            }
            ?>
        </div><!-- /.col -->
    </div><!-- /.row -->
</div>

<!-- Page Sections -->
<?php if ($page_sections) : ?>
    <?php foreach ($page_sections as $index => $section) : ?>
        <?php
        $has_image = !empty($section['image']);
        $is_even = $index % 2 == 0; // Even index (0, 2, 4...) will have content left, image right
        ?>
        <section class="py-5 feature-section section-fade" id="section-<?php echo $index; ?>">
            <div class="container">
                <div class="row align-items-stretch px-3 px-lg-0">
                    <?php if ($has_image) : ?>
                        <!-- Feature Text Card -->
                        <div class="col-lg-8 mb-4 mb-lg-0 order-1 <?php echo $is_even ? 'order-lg-1' : 'order-lg-2'; ?> px-0">
                            <div class="feature-card content d-flex flex-column justify-content-center p-0 h-100 <?php echo $is_even ? 'mx-3 me-lg-5' : 'mx-3 ms-lg-5'; ?>">
                                <div class="feature-card-header">
                                    <span class="feature-icon">
                                    </span>
                                    <h2 class="feature-title mb-2">
                                        <?php echo esc_html($section['title']); ?>
                                    </h2>
                                </div>
                                <div class="feature-description">
                                    <?php echo wp_kses_post($section['description']); ?>
                                </div>
                                <?php if (!empty($section['enable_button']) && !empty($section['button_text']) && !empty($section['button_link'])) : ?>
                                    <a class="btn btn-primary" href="<?php echo esc_url($section['button_link']); ?>">
                                        <?php echo esc_html($section['button_text']); ?> <i class="ms-3 fas fa-chevron-right"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <!-- Feature Image -->
                        <div class="col-lg-4 text-center order-2 <?php echo $is_even ? 'order-lg-2' : 'order-lg-1'; ?> px-0">
                            <div class="feature-image-wrapper px-3 px-lg-0 h-100 w-100">
                                <img src="<?php echo esc_url($section['image']['url']); ?>"
                                    alt="<?php echo esc_attr($section['image']['alt']); ?>"
                                    class="feature-image">
                            </div>
                        </div>
                    <?php else : ?>
                        <!-- Full width content when no image -->
                        <div class="col-12 px-0">
                            <div class="feature-card content d-flex flex-column justify-content-center p-0 h-100 mx-3">
                                <div class="feature-card-header">
                                    <span class="feature-icon">
                                    </span>
                                    <h2 class="feature-title mb-2">
                                        <?php echo esc_html($section['title']); ?>
                                    </h2>
                                </div>
                                <div class="feature-description">
                                    <?php echo $section['description']; ?>
                                </div>
                                <?php if (!empty($section['enable_button']) && !empty($section['button_text']) && !empty($section['button_link'])) : ?>
                                    <a class="btn btn-primary" href="<?php echo esc_url($section['button_link']); ?>">
                                        <?php echo esc_html($section['button_text']); ?> <i class="ms-3 fas fa-chevron-right"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    <?php endforeach; ?>
<?php endif; ?>

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

    .entry-title {
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
                summary.innerHTML = `You have chosen to donate <b>${formatCurrency(selectedAmount)}</b> <br> every <b>${selectedFrequency === 'monthly' ? 'month' : 'year'}</b> to the 500 club.`;
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
            } else if (frequency === 'yearly') {
                if (amount == 30) optionValue = 'Option 6';
                else if (amount == 50) optionValue = 'Option 7';
                else if (amount == 100) optionValue = 'Option 8';
            }
            if (!optionValue) {
                alert('Please select a valid amount and frequency that matches a PayPal option.');
                return;
            }
            // Set the value in the hidden form and submit
            const paypalForm = document.getElementById('paypal-hidden-form');
            paypalForm.os0.value = optionValue;
            paypalForm.submit();
        });
    });
</script>