<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php wp_head(); ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=PT+Serif:wght@400;700&display=swap" rel="stylesheet">
</head>

<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>
    <style>
        :root {
            --primary-color: #1398A5;
            --primary-color-dark: #117e88;
            --accent-color: #169eaa;
            --box-shadow: 5px 5px 0 0 #1398A54D;
        }

        p a {
            color: var(--primary-color);
            position: relative;
            text-decoration: none;
            display: inline-block;
            transition: color 0.3s;
            /* overflow: hidden; */
        }

        p a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background-color: var(--primary-color-dark);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.3s cubic-bezier(.4, 0, .2, 1);
        }

        p a:hover,
        p a:focus {
            color: var(--primary-color-dark);
        }

        p a:hover::after,
        p a:focus::after {
            transform: scaleX(1);
            transform-origin: left;
        }

        p a:not(:hover):not(:focus)::after {
            transform: scaleX(0);
            transform-origin: right;
        }

        .border-primary {
            border-color: #1398A5 !important;
            border-width: 4px !important;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            background: var(--primary-color);
            color: #fff !important;
            font-family: 'Lexend', Arial, sans-serif;
            font-weight: 700;
            font-size: 1rem;
            border: none;
            border-radius: 60px 30px 60px 30px !important;
            padding: 12px 30px !important;
            box-shadow: var(--box-shadow) !important;
            transition: box-shadow 0.3s, background 0.3s, color 0.3s, opacity 0.8s, transform 0.8s;
            text-decoration: none;
            position: relative;
            letter-spacing: 0.01em;
            margin: 0;
            opacity: 0;
            transform: translateY(40px);
        }

        .btn-primary.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .btn-primary:hover,
        .btn-primary:focus {
            background: var(--primary-color-dark);
            color: #fff !important;
            box-shadow: 0px 0px 0 0 var(--box-shadow) !important;
            text-decoration: none;
        }

        .btn-secondary {
            display: inline-block;
            background: #fff;
            color: var(--primary-color) !important;
            font-family: 'Lexend', Arial, sans-serif;
            font-weight: 700;
            font-size: 1rem;
            border: none;
            border-radius: 60px 30px 60px 30px !important;
            padding: 12px 30px !important;
            margin-right: 12px;
            margin-bottom: 0;
            box-shadow: 5px 5px 0 0 rgb(255, 255, 255, 0.5);
            transition: opacity 0.8s cubic-bezier(.4, 0, .2, 1), transform 0.8s cubic-bezier(.4, 0, .2, 1), box-shadow 0.3s, background 0.3s, color 0.3s;
            text-decoration: none;
            letter-spacing: 0.01em;
            vertical-align: middle;
            opacity: 0;
            transform: translateY(40px);
        }

        .btn-secondary.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .btn-secondary:hover,
        .btn-secondary:focus {
            background: #fefefe;
            color: #303030 !important;
            box-shadow: 0px 0px 0 0 rgb(255, 255, 255, 0.5);
            text-decoration: none;
        }

        .btn-sm-70 {
            font-size: 0.75rem !important;
            border-radius: 40px 20px 40px 20px !important;
            padding: 6px 15px !important;
            box-shadow: 3px 3px 0 0 #1398A54D !important;
        }

        html,
        body {
            overflow-x: hidden;
            font-family: 'Lexend', Arial, sans-serif;
        }

        .font-pt-serif,
        .bg-light.fw-bold {
            font-family: 'PT Serif', serif !important;
        }

        .btn-primary .fa-chevron-right,
        .btn-secondary .fa-chevron-right {
            display: inline-block;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.3s linear;
            margin-left: 1rem;
            position: relative;
        }

        .btn-primary:hover .fa-chevron-right,
        .btn-primary:focus .fa-chevron-right,
        .btn-secondary:hover .fa-chevron-right,
        .btn-secondary:focus .fa-chevron-right {
            animation: arrow-slide-fade 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .facebook-button {
            background: #1877F3 !important;
            color: #fff !important;
            border: none;
            box-shadow: 5px 5px 0 0 #1877F366 !important;
            margin-left: 10px;
            /* Facebook blue shadow, semi-transparent */
            width: auto;
            min-width: unset;
            padding: 12px 18px;
            border-radius: 50%;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s, color 0.2s, box-shadow 0.2s;
        }

        .facebook-button:hover,
        .facebook-button:focus {
            background: #145db2 !important;
            color: #fff !important;
            box-shadow: 0 0px 0 0 #1877F366 !important;
        }

        @keyframes arrow-slide-fade {
            0% {
                opacity: 1;
                transform: translateX(0);
            }

            40% {
                opacity: 0;
                transform: translateX(30px);
            }

            41% {
                opacity: 0;
                transform: translateX(-30px);
            }

            100% {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .menu-link-pill,
        .nav-link.menu-link-pill {
            display: inline-block;
            background: #fff;
            color: #169eaa !important;
            font-family: 'Lexend', Arial, sans-serif;
            font-weight: 700;
            font-size: 1rem;
            border: none;
            border-radius: 60px 30px 60px 30px !important;
            padding: 12px 30px !important;
            margin-right: 12px;
            margin-bottom: 0;
            box-shadow: 5px 5px 0 0 rgb(255, 255, 255, 0.5);
            transition: opacity 0.8s cubic-bezier(.4, 0, .2, 1), transform 0.8s cubic-bezier(.4, 0, .2, 1), box-shadow 0.3s, background 0.3s, color 0.3s;
            text-decoration: none;
            letter-spacing: 0.01em;
            vertical-align: middle;
            /* visibility: hidden; */
            opacity: 0;
            transform: translateY(40px);
        }

        .menu-link-pill:last-child,
        .nav-link.menu-link-pill:last-child {
            margin-right: 0;
        }

        .menu-link-pill:hover,
        .menu-link-pill:focus,
        .nav-link.menu-link-pill:hover,
        .nav-link.menu-link-pill:focus {
            background: #fefefe;
            box-shadow: 0px 0px 0 0 rgb(255, 255, 255, 0.5);
            color: #303030 !important;
            text-decoration: none;
        }

        .menu-link-pill.visible,
        .nav-link.menu-link-pill.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .header-bg-bootstrap {
            min-height: 100vh;
            width: 100vw;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: stretch;
            background: url('<?php echo the_post_thumbnail_url('full'); ?>') center/cover no-repeat;
            background-size: cover;
            z-index: 0;
        }

        .header-bg-bootstrap::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to bottom, rgba(255, 255, 255, 0.9) 0%, rgba(22, 158, 170, 0.9) 100%);
            z-index: 1;
            pointer-events: none;
        }

        .header-content-bootstrap {
            position: relative;
            z-index: 2;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: stretch;
        }

        .page-inner {
            max-width: 1400px;
            margin-left: auto;
            margin-right: auto;
            padding-left: 2rem;
            padding-right: 2rem;
            width: 100%;
        }

        header {
            max-width: 100vw;
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

        .icon-card-text {
            letter-spacing: -1px;
            line-height: 1.1;
        }

        .mobile-menu-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(22, 158, 170, 0.75);
            z-index: 99999 !important;
            display: flex;
            align-items: flex-start;
            justify-content: flex-end;
            opacity: 0;
            pointer-events: none;
            transition: transform 0.3s;
        }

        .mobile-menu-overlay.open {
            opacity: 1;
            pointer-events: auto;
        }

        .mobile-menu-content {
            width: 100vw;
            max-width: 500px;
            min-height: 100vh;
            box-shadow: -2px 0 16px rgba(0, 0, 0, 0.08);
            position: relative;
            background: #fff;
            transform: translateX(100%);
            transition: transform 0.3s;
            z-index: 100000;
        }

        .mobile-menu-overlay.open .mobile-menu-content {
            transform: translateX(0);
        }

        body.menu-open {
            overflow: hidden;
            position: fixed;
            width: 100%;
        }

        body.menu-open * {
            z-index: 1;
        }

        .mobile-menu-content .menu-link-pill {
            display: block !important;
            width: 100%;
            margin-right: 0;
            border-bottom: 1px solid #eee !important;
            margin-bottom: 12px;
            text-align: left;
            border-radius: 0 !important;
        }

        .mobile-menu-content .nav-link {
            display: block !important;
            width: 100%;
            margin-right: 0;
            border-bottom: 1px solid #eee !important;
            margin-bottom: 12px;
            text-align: left;
            border-radius: 0 !important;
        }

        @media (max-width: 767.98px) {
            nav.w-100>.d-flex {
                display: none !important;
            }

            .mobile-menu-content .menu-link-pill {
                font-size: 1.5rem !important;
                padding: 16px 30px !important;
                margin-bottom: 2px !important;
                border-radius: 0 !important;
            }

            .mobile-menu-content .nav-link {
                font-size: 1.5rem !important;
                padding: 16px 30px !important;
                margin-bottom: 2px !important;
                border-radius: 0 !important;
            }

            .mobile-menu-close {
                background: #fff;
                z-index: 1000000;
                margin-bottom: 1rem;
                margin-top: 0.5rem;
            }
        }

        .icon-video-wrapper {
            width: 100px;
            height: 100px;
            position: relative;
            z-index: 1;
            display: inline-block;
        }

        .icon-fallback {
            position: absolute;
            top: 0;
            left: 0;
            width: 100px;
            height: 100px;
            object-fit: contain;
            z-index: 1 !important;
            display: none;
            transition: opacity 0.2s;
        }

        .animated-icon {
            position: absolute;
            top: 0;
            left: 0;
            width: 100px;
            height: 100px;
            object-fit: contain;
            z-index: 2 !important;
            background: transparent;
            pointer-events: none;
        }

        .icon-video-wrapper.playing .icon-fallback {
            opacity: 0;
        }

        .overflow-hidden {
            overflow: hidden !important;
        }

        .bear {
            position: absolute;
            bottom: -150px;
            left: -20px;
            z-index: 10;
            /* transform: translateX(-10%); */
            height: 500px;
            animation: bear-slide-in forwards 3.5s ease-in-out;
        }

        @media (max-width: 767.98px) {
            .bear {
                height: 300px;
                left: -130px;
                transform: none;
                bottom: -100px;
            }
        }

        @keyframes bear-slide-in {
            0% {
                transform: translateX(-100%);
            }

            100% {
                transform: translateX(-10%);
            }
        }


        .palmer-partners-link {
            /* padding: 5px 20px !important; */
            transition: all 0.3s;
        }

        .palmer-partners-link {
            position: relative;
        }

        .palmer-partners-link::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -2px;
            width: 0;
            height: 2px;
            background-color: #e15400;
            transition: width 0.3s ease-out;
        }

        .palmer-partners-link:hover::after {
            width: 100%;
        }

        .palmer-partners-link i {
            transition: all 0.3s;
        }

        .palmer-partners-link:hover i {
            color: #e15400 !important;
        }

        /* 4. Section Fade-In on Scroll */
        .section-fade {
            opacity: 0;
            transform: translateY(40px);
            transition: opacity 0.7s cubic-bezier(.4, 0, .2, 1), transform 0.7s cubic-bezier(.4, 0, .2, 1);
        }

        .section-fade.visible {
            opacity: 1;
            transform: none;
        }

        /* Single element fade-in-up animation */
        .fade-in-up {
            opacity: 0;
            transform: translateY(32px) scale(0.98);
            transition: opacity 0.7s cubic-bezier(.4, 0, .2, 1), transform 0.7s cubic-bezier(.4, 0, .2, 1);
        }

        .fade-in-up.visible {
            opacity: 1;
            transform: none;
        }

        .mobile-menu-close {
            display: block;
            text-align: center;
            font-weight: bold;
            font-size: 1.1rem;
            border-radius: 30px;
            background: #f5f5f5;
            transition: all 0.3s;
            color: #333 !important;
        }

        .mobile-menu-close:hover,
        .mobile-menu-close:focus,
        .mobile-menu-close:active {
            background: #ececec !important;
            color: #111 !important;
        }

        .mobile-menu-content {
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            height: 100%;
        }

        .top-bar-animate {
            transform: translateY(-100%);
            opacity: 0;
            transition: transform 0.7s cubic-bezier(.4, 0, .2, 1), opacity 0.7s cubic-bezier(.4, 0, .2, 1);
        }

        .top-bar-animate.visible {
            transform: translateY(0);
            opacity: 1;
        }

        .mobile-menu-toggle {
            position: relative;
            font-size: 1.4rem;
        }

        .latest-news-section {
            background: #f8f9fa;
        }

        textarea {
            resize: none;
        }

        <?php if (!is_front_page()) : ?>.header-bg-bootstrap {
            min-height: unset;
            max-height: 350px;
            background: linear-gradient(to bottom, rgba(255, 255, 255, 0.9) 0%, rgba(22, 158, 170, 0.9) 100%) !important;
        }

        .header-content-bootstrap {
            min-height: unset;
            max-height: 350px;
        }

        <?php endif; ?>
    </style>

    <?php if (is_front_page()) : ?>
        <div class="bear fade-in-up">
            <img src="https://500club.org/wp-content/uploads/2025/09/Roman-Full-Wave-Right-scaled-e1758122748446.png" alt="Roman Full Wave Right" class="img-fluid" style="width: 80%; height: 80%; object-fit: contain; rotate: 25deg;">
        </div>
    <?php endif; ?>



    <header class="header-bg-bootstrap container-fluid m-0 p-0">

        <div class="header-content-bootstrap h-100 w-100">
            <!-- Top Bar -->
            <div class="bg-light fw-bold border-bottom border-primary font-pt-serif w-100 " style="overflow:hidden;">
                <div class="d-flex justify-content-between align-items-center position-relative">

                    <div class="">
                        <?php if (is_front_page()) : ?>
                            <a href="https://www.palmerpartners.com/" target="_blank" rel="noopener">
                                <img src="https://500club.org/wp-content/uploads/2025/07/image.png" alt="Palmer & Partners Logo" class="ps-4 img-fluid me-2" style="filter: brightness(0) contrast(1); max-height:40px; width: auto; object-fit: contain;">
                            </a>
                        <?php else : ?>
                            <a href="<?php
                                        $current_url = $_SERVER['REQUEST_URI'];
                                        $home_url = home_url();

                                        // Remove trailing slash if present
                                        $current_url = rtrim($current_url, '/');

                                        // Split the URL path into segments
                                        $path_segments = explode('/', trim($current_url, '/'));

                                        // If we're at the homepage or have only one segment, go to homepage
                                        if (empty($path_segments) || count($path_segments) <= 1) {
                                            echo esc_url($home_url);
                                        } else {
                                            // Remove the last segment to get the parent URL
                                            array_pop($path_segments);
                                            $parent_path = '/' . implode('/', $path_segments);
                                            echo esc_url($home_url . $parent_path);
                                        }
                                        ?>" class="ms-4"> <i class="text-dark fa-solid fa-lg fa-chevron-left mx-2 me-4"></i></a>

                        <?php endif; ?>
                    </div>


                    <div class="mx-4">
                        <a href="<?php echo home_url(); ?>">
                            <img src="https://500club.org/wp-content/uploads/2025/05/image-1.png" alt="500 club Logo" class="img-fluid" style="max-height: 60px; width: auto; object-fit: contain;">
                        </a>
                    </div>
                    <div>


                        <!-- Hamburger Icon (visible on mobile/tablet) -->
                        <div for="mobile-menu-toggle" class="mobile-menu-toggle rounded-0 d-lg-none text-dark btn btn-link px-4 p-3" style="border-radius:0 !important; display: flex; align-items: center; justify-content: center; cursor:pointer;" aria-expanded="false" aria-controls="mobileMenuContent">
                            <i class="fas fa-bars fa-lg"></i>
                        </div>
                    </div>

                </div>
            </div>
            <!-- Main Navigation -->
            <nav class="w-100 d-none d-lg-block">
                <?php if (is_front_page()) : ?>
                    <div class="d-flex justify-content-center align-items-center p-5">
                        <div class="d-flex align-items-center">
                            <!-- <a href="<?php echo esc_url(home_url()); ?>" class="btn-secondary">
                                <i class="fa-solid fa-house"></i>
                            </a> -->
                            <a href="<?php echo is_front_page() ? '#our-mission' : esc_url(home_url('/#our-mission')); ?>" class="btn-secondary">Our mission</a>
                            <a href="<?php echo is_front_page() ? '#how-to-help' : esc_url(home_url('/#how-to-help')); ?>" class="btn-secondary">How to help</a>
                            <a href="<?php echo esc_url(home_url('/events/')); ?>" class="btn-secondary">Events</a>
                            <a href="<?php echo esc_url(home_url('/latest-news/')); ?>" class="btn-secondary">Latest news</a>
                            <a href="<?php echo is_front_page() ? '#get-in-touch' : esc_url(home_url('/#get-in-touch')); ?>" class="btn-secondary">Get in touch</a>
                        </div>
                        <div class="d-flex align-items-center">
                            <!-- <a class="btn btn-primary" href="#join-our-club">Join our club <i class="ms-3 fas fa-chevron-right"></i></a> -->
                        </div>
                    </div>
                <?php endif; ?>
            </nav>
            <?php if (is_front_page()) : ?>
                <!-- Logo Centered -->
                <div class="d-flex flex-grow-1 justify-content-center align-items-center w-100 px-2 mb-5 flex-column">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/img/logo.png" alt="500 Club Logo" class="px-5 img-fluid fade-in-up" style="max-height: 350px; width: auto; object-fit: contain;" />
                    <a class="btn btn-primary mt-4" href="#join-our-club">Join our club <i class="ms-3 fas fa-chevron-right"></i></a>
                </div>
                <!-- Animated Down Chevron -->
                <div class="w-100 d-flex justify-content-center align-items-end">
                    <div class="scroll-down-circle d-flex justify-content-center align-items-center">
                        <i class="fas fa-chevron-down chevron-animate"></i>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <!-- Mobile Menu Overlay -->

    </header>

    <div id="mobileMenu" class="mobile-menu-overlay d-lg-none">
        <div id="mobileMenuContent" class="mobile-menu-content bg-white d-flex flex-column justify-content-between h-100">
            <button class="mobile-menu-close btn btn-primary text-dark mt-2 mb-4 mx-4" aria-label="Close Menu">
                <i class="fas fa-times"></i>
            </button>

            <nav class="nav flex-column flex-grow-1">
                <div>
                    <a href="<?php echo is_front_page() ? '#our-mission' : esc_url(home_url('/#our-mission')); ?>" class="nav-link btn-secondary mb-2">Our mission</a>
                    <a href="<?php echo is_front_page() ? '#how-to-help' : esc_url(home_url('/#how-to-help')); ?>" class="nav-link btn-secondary mb-2">How you can help</a>
                    <a href="<?php echo esc_url(home_url('/events/')); ?>" class="nav-link btn-secondary mb-2">Events</a>
                    <a href="<?php echo esc_url(home_url('/latest-news/')); ?>" class="nav-link btn-secondary mb-2">Latest news</a>
                    <a href="<?php echo is_front_page() ? '#get-in-touch' : esc_url(home_url('/#get-in-touch')); ?>" class="nav-link btn-secondary mb-2">Get in touch</a>
                </div>
                <div class="w-100 d-flex justify-content-center mt-3 gap-2">
                    <a class="btn btn-primary" style="width:auto; min-width:unset;" href="#join-our-club">Join our club <i class="ms-3 fas fa-chevron-right"></i></a>
                    <a class="btn facebook-button btn-primary" href="https://facebook.com/" target="_blank" rel="noopener" aria-label="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                </div>
            </nav>
        </div>
    </div>


    <script>
        // Mobile menu JS (CSS-only animation + prevent body scroll + accessibility)
        document.addEventListener("DOMContentLoaded", function() {
            const menuToggle = document.querySelector('.mobile-menu-toggle');
            const menuOverlay = document.getElementById('mobileMenu');

            function setBodyScrollLock(lock) {
                const html = document.documentElement;
                if (lock) {
                    document.body.classList.add('menu-open');
                    menuToggle.setAttribute('aria-expanded', 'true');
                } else {
                    document.body.classList.remove('menu-open');
                    menuToggle.setAttribute('aria-expanded', 'false');
                }
            }

            if (menuToggle && menuOverlay) {
                menuToggle.addEventListener('click', () => {
                    menuOverlay.classList.add('open');
                    setBodyScrollLock(true);
                });
                // Delegate click for any .mobile-menu-close inside the overlay
                menuOverlay.addEventListener('click', (e) => {
                    if (e.target.classList.contains('mobile-menu-close') || e.target.closest('.mobile-menu-close')) {
                        menuOverlay.classList.remove('open');
                        setBodyScrollLock(false);
                    } else if (e.target === menuOverlay) {
                        menuOverlay.classList.remove('open');
                        setBodyScrollLock(false);
                    }
                });
                document.addEventListener('keydown', function(e) {
                    if (menuOverlay.classList.contains('open') && e.key === 'Escape') {
                        menuOverlay.classList.remove('open');
                        setBodyScrollLock(false);
                    }
                });

                // Close menu if a menu link with anchor # is clicked
                const anchorLinks = menuOverlay.querySelectorAll('a[href^="#"]');
                anchorLinks.forEach(link => {
                    link.addEventListener('click', function() {
                        menuOverlay.classList.remove('open');
                        setBodyScrollLock(false);
                    });
                });
            }
        });

        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll('.animated-icon-container').forEach(container => {
                const wrapper = container.querySelector('.icon-video-wrapper');
                const img = wrapper.querySelector('.icon-fallback');
                const vid = wrapper.querySelector('.animated-icon');
                if (!vid) return;

                // Always show PNG by default
                wrapper.classList.remove('playing');

                // Play video on hover/focus/tap
                const playIfNotPlaying = () => {
                    if (vid.paused || vid.ended) {
                        vid.currentTime = 0;
                        vid.play();
                    }
                };
                container.addEventListener("mouseenter", playIfNotPlaying);
                container.addEventListener("focus", playIfNotPlaying);
                container.addEventListener("touchstart", playIfNotPlaying);

                // Hide PNG when video plays, show when video ends/pauses
                vid.addEventListener("play", () => {
                    wrapper.classList.add('playing');
                });
                vid.addEventListener("ended", () => {
                    wrapper.classList.remove('playing');
                    vid.currentTime = 0;
                });
                vid.addEventListener("pause", () => {
                    if (vid.currentTime === 0 || vid.ended) {
                        wrapper.classList.remove('playing');
                    }
                });
            });
        });

        // Micro-animations: Menu links staggered fade-in, section fade-in on scroll

        document.addEventListener("DOMContentLoaded", function() {

            // 4. Section fade-in on scroll
            const fadeSections = document.querySelectorAll('.section-fade');

            function revealSections() {
                fadeSections.forEach(section => {
                    const rect = section.getBoundingClientRect();
                    if (rect.top < window.innerHeight - 80) {
                        section.classList.add('visible');
                    }
                });
            }
            window.addEventListener('scroll', revealSections);
            revealSections();
        });

        // Single element fade-in-up animation
        function fadeInUpObserver() {
            const fadeEls = document.querySelectorAll('.fade-in-up');
            if ('IntersectionObserver' in window) {
                const obs = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('visible');
                            // If it's an icon-video-wrapper, play the video once
                            if (entry.target.classList.contains('icon-video-wrapper')) {
                                const vid = entry.target.querySelector('.animated-icon');
                                if (vid && vid.paused) {
                                    vid.currentTime = 0;
                                    vid.play();
                                }
                            }
                            obs.unobserve(entry.target);
                        }
                    });
                }, {
                    threshold: 0.2
                });
                fadeEls.forEach(el => obs.observe(el));
            } else {
                // Fallback for old browsers
                fadeEls.forEach(el => {
                    el.classList.add('visible');
                    if (el.classList.contains('icon-video-wrapper')) {
                        const vid = el.querySelector('.animated-icon');
                        if (vid && vid.paused) {
                            vid.currentTime = 0;
                            vid.play();
                        }
                    }
                });
            }
        }
        document.addEventListener('DOMContentLoaded', fadeInUpObserver);

        document.addEventListener("DOMContentLoaded", function() {
            // 1. Fade in header background and gradient (almost immediately)
            var bg = document.querySelector('.header-bg-bootstrap');
            if (bg) bg.classList.add('visible');

            // 2. Fade in logo after 150ms
            setTimeout(function() {
                var logo = document.querySelector('.px-5.img-fluid.fade-in-up');
                if (logo) logo.classList.add('visible');

                // 3. Slide down top bar immediately after logo fades in
                var topBar = document.querySelector('.top-bar-animate');
                if (topBar) topBar.classList.add('visible');

                // 4. After 40ms, slide up menu links one by one (staggered by 100ms)
                setTimeout(function() {
                    var menuLinks = document.querySelectorAll('.btn-secondary, .nav-link.btn-secondary');
                    menuLinks = Array.from(menuLinks);
                    menuLinks.forEach(function(link, i) {
                        setTimeout(function() {
                            link.classList.add('visible');
                            // If this is the last menu link, trigger the join button animation
                            if (i === menuLinks.length - 1) {
                                setTimeout(function() {
                                    var primaryBtns = document.querySelectorAll('.btn-primary');
                                    primaryBtns.forEach(function(btn) {
                                        btn.classList.add('visible');
                                        btn.style.display = '';
                                        btn.style.visibility = 'visible';
                                    });
                                }, 100); // 100ms delay after last menu link
                            }
                        }, i * 100);
                    });

                    // 5. Slide down and fade in the down chevron after 200ms
                    setTimeout(function() {
                        var chevron = document.querySelector('.scroll-down-circle');
                        if (chevron) chevron.classList.add('visible');
                    }, 200);

                }, 40); // menu links start 40ms after top bar
            }, 150); // logo after bg
        });
    </script>