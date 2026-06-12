<?php get_header(); ?>

<?php while (have_posts()) : the_post(); ?>

<section class="gallery-single">

    <div class="container">

        <h1><?php the_title(); ?></h1>

        <?php

        $image = get_field('gallery_images');

        if (!empty($image)) :

        ?>

            <img
                src="<?php echo esc_url($image['url']); ?>"
                alt="<?php echo esc_attr($image['alt']); ?>"
                class="gallery-single-image"
            >

        <?php endif; ?>

        <?php

        $terms = get_the_terms(
            get_the_ID(),
            'gallery_tags'
        );

        if (!empty($terms) && !is_wp_error($terms)) :

        ?>

            <div class="gallery-tags">

                <?php foreach ($terms as $term) : ?>

                    <span class="tag">
                        <?php echo esc_html($term->name); ?>
                    </span>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </div>

</section>

<?php endwhile; ?>

<?php get_footer(); ?>