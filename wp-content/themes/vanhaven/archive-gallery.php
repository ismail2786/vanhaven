<?php get_header(); ?>

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

    <?php
    $posts = get_posts([
        'post_type'      => 'gallery',
        'posts_per_page' => -1,
        'post_status'    => 'publish'
    ]);

    $first_row  = array_slice($posts, 0, 3);
    $second_row = array_slice($posts, 3, 3);
    ?>
    <div class="gallery-wrapper">
        <div class="gallery-grid gallery-grid-top">

            <?php foreach ($first_row as $post) :
                setup_postdata($post);

                get_template_part('gallery-card');

            endforeach; wp_reset_postdata(); ?>

        </div>

        <div class="gallery-grid gallery-grid-bottom">

            <?php foreach ($second_row as $post) :
                setup_postdata($post);

                get_template_part('gallery-card');

            endforeach; wp_reset_postdata(); ?>

        </div>
    </div>

</section>

<?php get_footer(); ?>