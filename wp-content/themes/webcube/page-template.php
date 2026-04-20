<?php

/**
 * Template Name: Custom Page Template
 * Description: A custom page template for additional pages
 */

get_header(); ?>

<main class="container-fluid p-0">
    <div class="page-inner">
        <div class="row">
            <div class="col-12">
                <h1 class="section-fade"><?php the_title(); ?></h1>
                <div class="section-fade">
                    <?php the_content(); ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?php get_footer(); ?>