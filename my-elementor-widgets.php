<?php
/**
 * Plugin Name:     SuS DigitalBase
 * Plugin URI:      https://example.com/my-agency-base
 * Description:     Basis-Elementor-Widgets und GSAP-Integrationen.
 * Version:         1.0.9
 * Author:          Lukas
 * Text Domain:     sus
 * Update URI:      https://example.com/updates/my-agency-base.json
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

add_shortcode('sus', function () {
    return 'My Elementor Widgets plugin is active!';
});