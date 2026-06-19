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
        <div class="logo is-desktop">
            <a href="<?php echo home_url(); ?>">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/logo.png" alt="Van Haven Logo">
            </a>
        </div>
        <!-- <div class="vh-btn-outline">
            <a href="/contact" class="btn-outline">CONTACT US</a>
        </div> -->

        <div class="ecommerce-menus">

            <a href="/search" class="vh-action-icon" aria-label="Search">
                <svg viewBox="0 0 24 24" width="18" height="18">
                    <circle cx="11" cy="11" r="7" fill="none" stroke="currentColor" stroke-width="1.8"/>
                    <line x1="16.5" y1="16.5" x2="21" y2="21" stroke="currentColor" stroke-width="1.8"/>
                </svg>
            </a>

            <a href="/login" class="vh-action-icon" aria-label="Account">
                <svg viewBox="0 0 24 24" width="18" height="18">
                    <circle cx="12" cy="8" r="4" fill="none" stroke="currentColor" stroke-width="1.8"/>
                    <path d="M4 20c1.5-4 5-6 8-6s6.5 2 8 6" fill="none" stroke="currentColor" stroke-width="1.8"/>
                </svg>
            </a>

            <a href="/wishlist" class="vh-action-icon" aria-label="Wishlist">
                <svg viewBox="0 0 24 24" width="18" height="18">
                    <path d="M12 21s-7-4.6-9.5-9C.5 8.5 2.5 4 7 4c2.2 0 3.9 1.2 5 3 1.1-1.8 2.8-3 5-3 4.5 0 6.5 4.5 4.5 8-2.5 4.4-9.5 9-9.5 9z"
                        fill="none" stroke="currentColor" stroke-width="1.8"/>
                </svg>
            </a>

            <a href="/cart" class="vh-action-icon" aria-label="Cart">
                <svg viewBox="0 0 24 24" width="18" height="18">
                    <path d="M5 8h14l-1.5 10h-11z"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linejoin="round"/>

                    <path d="M9 8V6a3 3 0 0 1 6 0v2"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"/>
                </svg>
            </a>

            <a href="/compare" class="vh-action-icon" aria-label="Compare">
                <svg viewBox="0 0 24 24" width="18" height="18">
                    <path
                        d="M12 3L15 9L21 12L15 15L12 21L9 15L3 12L9 9L12 3Z"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
            </a>

            <div class="vh-btn-outline">
                <a href="/contact" class="btn-outline">CONTACT US</a>
            </div>
        </div>
        <div class="is-mobile">
            <div class="vh-header-actions">

            <a href="/search" class="vh-action-icon" aria-label="Search">
                <svg viewBox="0 0 24 24" width="18" height="18">
                    <circle cx="11" cy="11" r="7" fill="none" stroke="currentColor" stroke-width="1.8"/>
                    <line x1="16.5" y1="16.5" x2="21" y2="21" stroke="currentColor" stroke-width="1.8"/>
                </svg>
            </a>

            <a href="/search" class="vh-action-icon" aria-label="Search">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/logo.png" alt="Van Haven Logo">
            </a>

            <a href="/login" class="vh-action-icon" aria-label="Account">
                <svg viewBox="0 0 24 24" width="18" height="18">
                    <circle cx="12" cy="8" r="4" fill="none" stroke="currentColor" stroke-width="1.8"/>
                    <path d="M4 20c1.5-4 5-6 8-6s6.5 2 8 6" fill="none" stroke="currentColor" stroke-width="1.8"/>
                </svg>
            </a>

            <a href="/cart" class="vh-action-icon" aria-label="Cart">
                <svg viewBox="0 0 24 24" width="18" height="18">
                    <path d="M5 8h14l-1.5 10h-11z"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linejoin="round"/>

                    <path d="M9 8V6a3 3 0 0 1 6 0v2"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"/>
                </svg>
            </a>
        </div>
    </div>
</header>

<main>