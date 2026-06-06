<?php get_header(); ?>

<?php while (have_posts()) : the_post(); ?>
<?php
    $args = array('post_type' => 'banner', 'posts_per_page' => 10);
    $the_query = new WP_Query($args);
    ?>
    <?php if ($the_query->have_posts()) : ?>
      <?php while ($the_query->have_posts()) : $the_query->the_post();?>

        <?php endwhile;
        wp_reset_postdata(); ?>
    <?php else :  ?>
        <p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
    <?php endif; ?>
<?php
endwhile;
?>

<?php get_footer(); ?>