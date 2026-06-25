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
                <div class="header-accordion is-mobile">
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
                <div class="header-accordion is-desktop">
                    <h4><a href="/contact">CONTACT</a></h4>
                </div>
                <div class="header-accordion is-mobile">
                    <h4>OWNERSHIP</h4>
                    <?php
                    wp_nav_menu([
                        'theme_location' => 'ownership',
                        'container'      => false,
                        'menu_class'     => 'menu',
                    ]);
                    ?>
                </div>
                <div class="header-accordion is-mobile">
                    <h4>LEGAL</h4>
                    <?php
                    wp_nav_menu([
                        'theme_location' => 'legal',
                        'container'      => false,
                        'menu_class'     => 'menu',
                    ]);
                    ?>
                </div>
                <div class="header-accordion is-mobile">
                    <h4>COMPLIANCE</h4>
                    <?php
                    wp_nav_menu([
                        'theme_location' => 'compliance',
                        'container'      => false,
                        'menu_class'     => 'menu',
                    ]);
                    ?>
                </div>
                <div class="header-accordion is-mobile">
                    <h4>COMPANY INFO</h4>
                    <?php
                    wp_nav_menu([
                        'theme_location' => 'company_info',
                        'container'      => false,
                        'menu_class'     => 'menu',
                    ]);
                    ?>
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
                <div class="header-accordion is-desktop">
                    <h4>FOLLOW US ON SOCIALS</h4>
                </div>
                <div class="header-socials">
                    <a href="#" target="_blank" rel="noopener"><img src="<?php echo get_template_directory_uri(); ?>/assets/instagram.png" alt="Instagram"></a>
                    <a href="#" target="_blank" rel="noopener"><img src="<?php echo get_template_directory_uri(); ?>/assets/facebook.png" alt="Facebook"></a>
                    <a href="#" target="_blank" rel="noopener"><img src="<?php echo get_template_directory_uri(); ?>/assets/youtube.png" alt="YouTube"></a>
                    <a href="#" target="_blank" rel="noopener"><img src="<?php echo get_template_directory_uri(); ?>/assets/linkedin.png" alt="LinkedIn"></a>
                </div>
                <div class="vh-btn-outline is-mobile">
                    <a href="/contact" class="btn-outline">CONTACT US</a>
                </div>
            </div>
        </div>
        <div class="logo is-desktop">
            <a href="<?php echo home_url(); ?>">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/logo.png" alt="Van Haven Logo">
            </a>
        </div>

        <div class="ecommerce-menus">

            <a href="/search" class="vh-action-icon" aria-label="Search">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/search.png" alt="Van Haven Search">
            </a>

            <a href="/login" class="vh-action-icon" aria-label="Account">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/user.png">
            </a>

            <a href="/wishlist" class="vh-action-icon" aria-label="Wishlist">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/favourite.png">
            </a>

            <a href="/cart" class="vh-action-icon" aria-label="Cart">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/cart.png">
            </a>

            <a href="/compare-vans" class="vh-action-icon" aria-label="Compare">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/compare.png">
            </a>

            <div class="vh-btn-outline">
                <a href="/contact" class="btn-outline">CONTACT US</a>
            </div>
        </div>
        <div class="is-mobile">
            <div class="vh-header-actions">
                <a href="/search" class="vh-action-icon" aria-label="Search">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/search.png">
                </a>
            </div>
        </div>
        <div class="is-mobile">
            <div class="vh-header-actions">
                <a href="/" class="vh-action-icon logo" aria-label="Logo">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/logo.png" alt="Van Haven Logo">
                </a>
            </div>
        </div>
        <div class="is-mobile">
            <div class="vh-header-actions">
                <a href="/login" class="vh-action-icon" aria-label="Account">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/user.png">
                </a>
            </div>
        </div>
        <div class="is-mobile">
            <div class="vh-header-actions">
                <a href="/cart" class="vh-action-icon" aria-label="Cart">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/cart.png">
                </a>
            </div>
        </div>
        </div>
    </div>
</header>

<!-- /* Mobile Sticky Menu */ -->
<div class="mobile-sticky-menu is-mobile">
    <div class="sticky-menu-wrapper">
        <a href="/login" aria-label="Account">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/user.png">
        </a>

        <a href="/wishlist" aria-label="Favourite">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/favourite.png">
        </a>

        <a href="/" aria-label="Home">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/home.png">
        </a>

        <a href="/compare-vans" aria-label="Compare">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/compare.png">
        </a>
        <a href="/cart" aria-label="Cart">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/cart.png">
        </a>
    </div>
</div>

<main>