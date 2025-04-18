<?php
/**
 * Plugin Name: My Elementor Widgets
 * Description: Prototype plugin with GitHub update checking.
 * Version: 1.0.0
 * Author: Your Name
 * Text Domain: my-elementor-widgets
 */
define('MY_PLUGIN_VERSION', '1.1.0');

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Simple test shortcode to verify plugin is active
add_shortcode('my_test', function () {
    return 'My Elementor Widgets plugin is active!';
});

add_action('admin_notices', function () {
    $version = MY_PLUGIN_VERSION;
    echo "<div class='notice notice-success is-dismissible'>
        <p>My Elementor Widgets plugin version <strong>{$version}</strong> is active!</p>
    </div>";
});


if (!class_exists('My_Custom_GitHub_Updater')) {
    class My_Custom_GitHub_Updater {
        private $plugin_slug;
        private $plugin_file;
        private $github_repo;
        private $github_api_url;
        private $current_version;

        public function __construct($plugin_file, $github_repo, $current_version) {
            $this->plugin_file = $plugin_file;
            $this->plugin_slug = plugin_basename($plugin_file);
            $this->github_repo = $github_repo;
            $this->github_api_url = "https://api.github.com/repos/{$github_repo}/releases/latest";
            $this->current_version = $current_version;

            add_filter('pre_set_site_transient_update_plugins', [$this, 'check_for_update']);
            add_filter('plugins_api', [$this, 'plugin_api_call'], 10, 3);
            add_filter('upgrader_post_install', [$this, 'after_install'], 10, 3);
        }

        public function check_for_update($transient) {
            if (empty($transient->checked[$this->plugin_slug])) {
                return $transient;
            }

            $remote = $this->get_remote_info();
            if (!$remote) {
                return $transient;
            }

            if (version_compare($this->current_version, $remote['tag_name'], '<')) {
                $plugin = new stdClass();
                $plugin->slug = $this->plugin_slug;
                $plugin->new_version = $remote['tag_name'];
                $plugin->url = $remote['html_url'];
                $plugin->package = $remote['zipball_url'];

                $transient->response[$this->plugin_slug] = $plugin;
            }

            return $transient;
        }

        public function plugin_api_call($false, $action, $args) {
            if ($args->slug !== dirname($this->plugin_slug)) {
                return $false;
            }

            $remote = $this->get_remote_info();
            if (!$remote) {
                return $false;
            }

            $res = new stdClass();
            $res->name = $remote['name'];
            $res->slug = dirname($this->plugin_slug);
            $res->version = $remote['tag_name'];
            $res->author = '<a href="https://github.com/' . $this->github_repo . '">GitHub</a>';
            $res->homepage = $remote['html_url'];
            $res->download_link = $remote['zipball_url'];
            $res->sections = [
                'description' => $remote['body'],
            ];

            return $res;
        }

        public function after_install($true, $hook_extra, $result) {
            global $wp_filesystem;

            $plugin_folder = dirname($this->plugin_slug);
            $destination = WP_PLUGIN_DIR . '/' . $plugin_folder;

            $wp_filesystem->move($result['destination'], $destination);
            $result['destination'] = $destination;

            if (!is_plugin_active($this->plugin_slug)) {
                activate_plugin($this->plugin_slug);
            }

            return $result;
        }

        private function get_remote_info() {
            $request = wp_remote_get($this->github_api_url, [
                'headers' => [
                    'Accept' => 'application/vnd.github.v3+json',
                    'User-Agent' => 'WordPress Plugin Updater',
                ],
            ]);

            if (is_wp_error($request)) {
                return false;
            }

            $response_code = wp_remote_retrieve_response_code($request);
            if ($response_code != 200) {
                return false;
            }

            $body = wp_remote_retrieve_body($request);
            $data = json_decode($body, true);

            if (empty($data) || !isset($data['tag_name'])) {
                return false;
            }

            return $data;
        }
    }
}

// Initialize the updater
new My_Custom_GitHub_Updater(
    __FILE__,
    'lukassusdigital/sus-base-plugin-test', // Replace with your GitHub repo
    MY_PLUGIN_VERSION
);
