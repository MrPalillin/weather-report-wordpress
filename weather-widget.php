<?php
/*
* Plugin Name: Weahter Widget (own version)
* Description: A widget that show the weather for the next 7 days based on current location (or chosen manually by the user)
* Version: 1.0
* Requires at least: 6.2
* Requires PHP: 8.2
* Author: Daniel de Vicente
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

$plugin_dir = plugin_dir_path(__FILE__);
include_once $plugin_dir . 'city-finder.php';

function add_module_type($tag, $handle, $src) {
    if ($handle === 'material-web') {
        return '<script type="module" src="' . esc_url($src) . '"></script>';
    }
    return $tag;
}

function my_plugin_enqueue_scripts() {
    wp_enqueue_script(
        'material-web',
        plugin_dir_url(__FILE__) . 'bootstrap.js',
        array(),
        null,
        true
    );
}

function my_plugin_enqueue_styles() {
    wp_enqueue_style(
        'material-ui-css',
        plugin_dir_url(__FILE__) . 'bootstrap.css',
        array(),
        '1.0.0'
    );
}

add_action('wp_enqueue_scripts', 'my_plugin_enqueue_styles');
add_action('wp_enqueue_scripts', 'my_plugin_enqueue_scripts');
add_filter('script_loader_tag', 'add_module_type', 10, 3);
add_shortcode( 'wp_openmeteo', 'getInput');

?>