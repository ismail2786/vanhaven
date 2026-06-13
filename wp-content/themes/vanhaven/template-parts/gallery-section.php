<?php

$gallery_posts = get_posts([
    'post_type'      => 'gallery',
    'posts_per_page' => -1,
    'post_status'    => 'publish'
]);

$desktop_chunks = array_chunk($gallery_posts, 6);
$mobile_chunks  = array_chunk($gallery_posts, 1);

?>

<section class="container vh-gallery vh-section">

    <div class="vh-grid-2">
        <h2>
            THE MOMENT YOUR
            <span>NEW CHAPTER BEGINS</span>
        </h2>

        <p>
            Explore our recent handovers and see the passion behind every bespoke build as it meets its new owner and takes to the open road.
        </p>
    </div>

    <!-- Desktop Slider -->
    <div class="swiper vh-gallery-slider vh-gallery-desktop">

        <div class="swiper-wrapper">

            <?php foreach ($desktop_chunks as $group) : ?>

                <div class="swiper-slide">

                    <div class="gallery-wrapper">

                        <div class="gallery-grid gallery-grid-top">

                            <?php
                            $top_cards = array_slice($group, 0, 3);

                            foreach ($top_cards as $post) :
                                setup_postdata($post);
                                get_template_part('gallery-card');
                            endforeach;
                            ?>

                        </div>

                        <div class="gallery-grid gallery-grid-bottom">

                            <?php
                            $bottom_cards = array_slice($group, 3, 3);

                            foreach ($bottom_cards as $post) :
                                setup_postdata($post);
                                get_template_part('gallery-card');
                            endforeach;
                            ?>

                        </div>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>

    </div>

    <!-- Mobile Slider -->
    <div class="swiper vh-gallery-mobile">

        <div class="swiper-wrapper">

            <?php foreach ($mobile_chunks as $group) : ?>

                <div class="swiper-slide">

                    <div class="gallery-mobile-grid">

                        <?php foreach ($group as $post) :

                            setup_postdata($post);

                            get_template_part('gallery-card');

                        endforeach; ?>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>

    </div>

    <div class="vh-gallery-nav">
        <button class="vh-prev">←</button>
        <button class="vh-next">→</button>
    </div>

</section>

<?php wp_reset_postdata(); ?>