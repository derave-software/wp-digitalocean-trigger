<?php
/**
 * Plugin Name: DigitalOcean Build Trigger
 * Plugin URI: https://deravesoftware.com
 * Description: The plugin triggers the build process for your app on the DigitalOcean App Platform.
 * Version: 1.0
 * Author: Vladislav Malienkov
 * Author URI: https://htmljs.ninja
 * License: GPLv2 or later
 */


class WP_DO
{
    /**
     * @var string
     */
    private $plugin_path;

    /**
     * @var WordPressSettingsFramework
     */
    private $wpsf;

    /**
     * WP_DO constructor.
     */
    public function __construct()
    {
        $this->plugin_path = plugin_dir_path(__FILE__);

        // Include and create a new WordPressSettingsFramework
        require_once($this->plugin_path . './wp-settings-framework/wp-settings-framework.php');
        $this->wpsf = new WordPressSettingsFramework($this->plugin_path . 'settings/settings-general.php', 'wp_do');

        // Add admin menu
        add_action('admin_menu', array($this, 'add_settings_page'), 20);

        $this->hooks();
    }

    /**
     * Add settings page.
     */
    public function add_settings_page()
    {
        $this->wpsf->add_settings_page(array(
            'page_title' => __('DigitalOcean Build Trigger', 'text-domain'),
            'menu_title' => __('DO Build Trigger', 'text-domain'),
            'capability' => 'read',
        ));
    }

    public function hooks()
    {
        $plugin_options = get_option('wp_do_settings');
        $plugin_hooks = $plugin_options['general_Hooks'];

        foreach ($plugin_hooks as $hook) {

            if ($hook === 'save_post') {
                add_action($hook, [$this, 'wp_do_trigger'], 10, 2);
            } else {
                add_action($hook, [$this, 'deploy']);
            }
        }
    }

    public function wp_do_trigger($post_id, $post)
    {
        if (wp_is_post_autosave($post_id)) {
            return;
        }

        if (!(wp_is_post_revision($post_id))) {

            $status_array = ['pending', 'draft', 'auto-draft'];
            $types_array = ['page', 'post'];

            if (in_array($post->post_status, $status_array) || !in_array($post->post_type, $types_array)) {
                return;
            }

            $this->deploy();
        }
    }

    public function deploy()
    {
        $plugin_options = get_option('wp_do_settings');
        $app_id = $plugin_options['general_DO_APP_ID'];
        $token = $plugin_options['general_DO_TOKEN'];

        $body = wp_json_encode([
            'force_build' => true
        ]);

        $response = wp_remote_post('https://api.digitalocean.com/v2/apps/' . $app_id . '/deployments', array(
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ],
            'body' => $body,
            'timeout' => 30,
            'method' => 'POST'
        ));

        $response_code = $response['response']['code'];
        $errors = new WP_Error;

        if ($response_code !== 200) {
            $error_message = $response['response']['message'];
            $errors->add($response_code, $error_message);
        }

    }
}

new WP_DO();
