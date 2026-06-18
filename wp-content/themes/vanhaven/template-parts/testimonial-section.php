<?php

$testimonials = get_posts(array(
    'post_type'      => 'testimonials',
    'posts_per_page' => -1,
    'post_status'    => 'publish'
));

echo '<pre>';
echo 'Found: ' . count($testimonials);  
echo '</pre>';

?>

<section class="vh-testimonials vh-section">

    <div class="container">

        <div class="swiper vh-testimonial-slider">

            <div class="swiper-wrapper">

                <?php foreach($testimonials as $post) :

                    setup_postdata($post);
                ?>

                    <div class="swiper-slide">

                        <?php get_template_part('template-parts/testimonial-card'); ?>

                    </div>

                <?php endforeach; ?>

            </div>

        </div>

        <div class="vh-testimonial-nav">
            <button class="vh-testimonial-prev">←</button>
            <button class="vh-testimonial-next">→</button>
        </div>

    </div>

</section>

<?php wp_reset_postdata(); ?>