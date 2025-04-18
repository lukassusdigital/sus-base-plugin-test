<?php
/**
 * Plugin Name: My Elementor Widgets
 * Description: Prototype plugin with GitHub Updater support.
 * Version: 1.1.3
 * Author: Your Name
 * Text Domain: my-elementor-widgets
 *
 * GitHub Plugin URI: https://github.com/lukassusdigital/sus-base-plugin-test.git
 * GitHub Branch: main
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Simple test shortcode to verify plugin is active
add_shortcode('my_test', function () {
    return 'My Elementor Widgets plugin is active!';
});

// You can add your Elementor widget registration and other plugin code here
