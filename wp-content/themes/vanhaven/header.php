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
            <!-- <span class="menu">MENU</span> -->

            <div class="accordion-wrapper">
                <div class="header-accordion">
                    <h4><a href="/">HOME</a></h4>
                </div>
                <div class="header-accordion">
                    <h4>SHOWROOM</h4>
                    <?php
                    wp_nav_menu([
                        'theme_location' => 'showroom',
                        'container'      => false,
                        'menu_class'     => 'menu',
                    ]);
                    ?>
                </div>
                <div class="header-accordion">
                    <h4>PARTS & SERVICES</h4>
                    <?php
                    wp_nav_menu([
                        'theme_location' => 'services',
                        'container'      => false,
                        'menu_class'     => 'menu',
                    ]);
                    ?>
                </div>
                <div class="header-accordion">
                    <h4>MERCH</h4>
                    <?php
                    wp_nav_menu([
                        'theme_location' => 'merch',
                        'container'      => false,
                        'menu_class'     => 'menu',
                    ]);
                    ?>
                </div>
                <div class="header-accordion">
                    <h4>ABOUT</h4>
                    <?php
                    wp_nav_menu([
                        'theme_location' => 'about',
                        'container'      => false,
                        'menu_class'     => 'menu',
                    ]);
                    ?>
                </div>
                <div class="header-accordion">
                    <h4><a href="/contact">CONTACT</a></h4>
                </div>
                <div class="header-accordion">
                    <h4>MORE</h4>
                    <?php
                    wp_nav_menu([
                        'theme_location' => 'more',
                        'container'      => false,
                        'menu_class'     => 'menu',
                    ]);
                    ?>
                </div>
                <div class="header-accordion">
                    <h4>FOLLOW US ON SOCIALS</h4>
                </div>
                <div class="header-socials">
                    <a href="#" target="_blank" rel="noopener"><img src="<?php echo get_template_directory_uri(); ?>/assets/instagram.png" alt="Instagram"></a>
                    <a href="#" target="_blank" rel="noopener"><img src="<?php echo get_template_directory_uri(); ?>/assets/facebook.png" alt="Facebook"></a>
                    <a href="#" target="_blank" rel="noopener"><img src="<?php echo get_template_directory_uri(); ?>/assets/youtube.png" alt="YouTube"></a>
                    <a href="#" target="_blank" rel="noopener"><img src="<?php echo get_template_directory_uri(); ?>/assets/linkedin.png" alt="LinkedIn"></a>
                </div>
            </div>
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