<?php get_header(); ?>

<?php while (have_posts()) : the_post(); ?>
    <h1><?php the_title(); ?></h1>
    This is front-page.php
    <div class="content">
        <?php the_content(); ?>
    </div>
<?php endwhile; ?>


<?php get_footer(); ?>