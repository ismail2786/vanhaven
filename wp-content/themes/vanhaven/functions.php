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