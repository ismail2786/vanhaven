<article class="gallery-card">

    <a href="<?php the_permalink(); ?>">

        <?php

        $image = get_field('gallery_images');

        if ($image) :

        ?>

            <img
                src="<?php echo esc_url($image['sizes']['large']); ?>"
                alt="<?php echo esc_attr($image['alt']); ?>"
                class="gallery-image"
            >

        <?php endif; ?>

        <!-- <h3><?php the_title(); ?></h3> -->

    </a>

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

</article>