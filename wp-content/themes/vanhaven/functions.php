<?php

function vanhaven_enqueue_styles() {
    wp_enqueue_style(
        'vanhaven-style',
        get_stylesheet_uri(),
        [],
        filemtime(get_stylesheet_directory() . '/style.css')
    );
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
        'secondary' => __( 'Secondary Menu', 'Footer' ),
    ) 

);