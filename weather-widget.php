<?php
/*
* Plugin Name: Weahter Report Wordpress
* Description: A widget that show the weather for the next 7 days based on current location (or chosen manually by the user)
* Version: 1.1
* Requires at least: 6.2
* Requires PHP: 8.2
* Author: Daniel de Vicente
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}
$plugin_dir = plugin_dir_path(__FILE__);
include_once $plugin_dir . 'includes/city-finder.php';

function my_plugin_enqueue_scripts() {
    wp_enqueue_script(
        'bootstrap-js',
        plugin_dir_url(__FILE__) . 'assets/js/bootstrap.js',
        array(),
        null,
        true
    );
	wp_enqueue_script(
		'html-code',
		plugin_dir_url(__FILE__) . 'assets/js/html_code.js',
		array(),
		null,
		true
	);
}
function my_plugin_enqueue_styles() {
    wp_enqueue_style(
        'bootstrap-css',
        plugin_dir_url(__FILE__) . 'assets/css/bootstrap.css',
        array(),
        '1.0.0'
    );
}
add_action('wp_enqueue_scripts', 'my_plugin_enqueue_styles');
add_action('wp_enqueue_scripts', 'my_plugin_enqueue_scripts');
add_shortcode( 'wp_openmeteo', 'getInput');
?>