<?php

function vanhaven_enqueue_styles() {

    wp_enqueue_style(
        'vanhaven-style',
        get_stylesheet_uri(),
        [],
        filemtime(get_stylesheet_directory() . '/style.css')
    );

    foreach (glob(get_template_directory() . '/css/*.css') as $file) {

        $handle = 'vh-' . basename($file, '.css');

        wp_enqueue_style(
            $handle,
            get_template_directory_uri() . '/css/' . basename($file),
            ['vanhaven-style'],
            filemtime($file)
        );
    }
}

add_action('wp_enqueue_scripts', 'vanhaven_enqueue_styles');

function my_theme_enqueue_scripts() {
    wp_enqueue_script(
        'theme-scripts',
        get_template_directory_uri() . '/js/scripts.js',
        array(), // Dependencies
        '1.0.0', // Version
        true     // Load in footer
    );
}

add_action('wp_enqueue_scripts', 'my_theme_enqueue_scripts');

register_nav_menus( 
    array(
        'primary' => __( 'Primary Menu', 'Header' ),
        'showroom' => __('Showroom', 'vanhaven'),
        'services' => __('Parts & Services', 'vanhaven'),
        'merch' => __('Merch', 'vanhaven'),
        'about'    => __('About', 'vanhaven'),
        'more'     => __('More', 'vanhaven'),
        'ownership'     => __('Ownership', 'vanhaven'),
        'legal'     => __('Legal', 'vanhaven'),
        'compliance'     => __('Compliance', 'vanhaven'),
        'company_info'     => __('Company Info', 'vanhaven'),
    ) 

);


/**
 * Theme Setup
 */
function vh_theme_setup() {

    add_theme_support('post-thumbnails');
    add_theme_support('title-tag');

}
add_action('after_setup_theme', 'vh_theme_setup');


/**
 * Gallery CPT
 */
function vh_register_gallery_cpt() {

    $args = array(

        'labels' => array(
            'name'               => 'Gallery',
            'singular_name'      => 'Gallery',
            'add_new_item'       => 'Add New Gallery',
            'edit_item'          => 'Edit Gallery',
            'view_item'          => 'View Gallery',
        ),

        'public'              => true,
        'show_in_rest'        => true,
        'has_archive'         => true,
        'menu_icon'           => 'dashicons-format-gallery',

        'rewrite' => array(
            'slug' => 'gallery'
        ),

        'supports' => array(
            'title',
            'thumbnail'
        ),

    );

    register_post_type('gallery', $args);

}
add_action('init', 'vh_register_gallery_cpt');


/**
 * Gallery Tags
 */
function vh_register_gallery_tags() {

    register_taxonomy(
        'gallery_tags',
        'gallery',
        array(

            'labels' => array(
                'name'          => 'Gallery Tags',
                'singular_name' => 'Gallery Tag',
            ),

            'hierarchical' => false,
            'show_in_rest' => true,

        )
    );

}
add_action('init', 'vh_register_gallery_tags');

function vh_gallery_section_shortcode() {

    ob_start();

    get_template_part('template-parts/gallery-section');

    return ob_get_clean();
}

add_shortcode('vh_gallery', 'vh_gallery_section_shortcode');



function vh_enqueue_assets() {

    wp_enqueue_style(
        'swiper-css',
        'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css',
        [],
        '11.0.0'
    );

    wp_enqueue_script(
        'swiper-js',
        'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js',
        [],
        '11.0.0',
        true
    );

    wp_enqueue_script(
        'vh-scripts',
        get_template_directory_uri() . '/js/scripts.js',
        ['swiper-js'], // IMPORTANT dependency
        filemtime(get_template_directory() . '/js/scripts.js'),
        true
    );
}
add_action('wp_enqueue_scripts', 'vh_enqueue_assets');