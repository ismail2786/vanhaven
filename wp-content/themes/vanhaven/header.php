<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<header class="site-header">
    <div class="container">
        <div class="menu-toggle">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>

            <?php
                wp_nav_menu([
                    'theme_location' => 'primary'
                ]);
            ?>
        </div>
        <div class="logo">
            <a href="<?php echo home_url(); ?>">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/logo.png" alt="Van Haven Logo">
            </a>
        </div>
        <div class="vh-btn-outline">
            <a href="/contact" class="btn-outline">CONTACT US</a>
        </div>
    </div>
</header>

<main>