<?php
/**
 * Plugin Name: My Elementor Widgets
 * Description: Prototype plugin with GitHub update checking.
 * Version: 1.0.0
 * Author: Your Name
 * Text Domain: my-elementor-widgets
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Simple test shortcode to verify plugin is active
add_shortcode('my_test', function () {
    return 'My Elementor Widgets plugin is active!';
});
