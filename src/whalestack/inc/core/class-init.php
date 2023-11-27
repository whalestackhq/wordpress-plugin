<?php

namespace Whalestack\Inc\Core;
use Whalestack as WS;
use Whalestack\Inc\Admin as Admin;
use Whalestack\Inc\Frontend as Frontend;

class Init {

	protected $loader;
	protected $plugin_basename;
	protected $version;
	protected $plugin_text_domain;
	protected $plugin_name_url;

	// define the core functionality of the plugin.
	public function __construct() {

        ini_set('serialize_precision', 14);

        $this->plugin_name = WS\PLUGIN_NAME;
		$this->version = WS\PLUGIN_VERSION;
		$this->plugin_basename = WS\PLUGIN_BASENAME;
		$this->plugin_text_domain = WS\PLUGIN_TEXT_DOMAIN;
		$this->plugin_name_url = WS\PLUGIN_NAME_URL;

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Loads the following required dependencies for this plugin.
	 *
	 * - Loader - Orchestrates the hooks of the plugin.
	 * - Internationalization_i18n - Defines internationalization functionality.
	 * - Admin - Defines all hooks for the admin area.
	 * - Frontend - Defines all hooks for the public side of the site.
	 *
	 * @access    private
	 */
	private function load_dependencies() {
		$this->loader = new Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Internationalization_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @access    private
	 */
	private function set_locale() {

		$plugin_i18n = new Internationalization_i18n($this->plugin_text_domain);

		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * Callbacks are documented in inc/admin/class-admin.php
	 * 
	 * @access    private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Admin\Admin($this->get_plugin_name(), $this->get_version(), $this->get_plugin_text_domain(), $this->get_plugin_name_url());

		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');

		// Add a top-level admin menu for our plugin
		$this->loader->add_action('admin_menu', $plugin_admin, 'add_plugin_admin_menu');
		
		// when a form is submitted to admin-post.php
		$this->loader->add_action('admin_post_nopriv_whalestack_admin_form_response', $plugin_admin, 'admin_form_response_handler');
		$this->loader->add_action('admin_post_whalestack_admin_form_response', $plugin_admin, 'admin_form_response_handler');

		// when a form is submitted to admin-ajax.php
		$this->loader->add_action('wp_ajax_nopriv_whalestack_admin_form_response', $plugin_admin, 'admin_form_response_handler');
		$this->loader->add_action('wp_ajax_whalestack_admin_form_response', $plugin_admin, 'admin_form_response_handler');

		// displays success and error notices in the admin area (for POST form submits)
		$this->loader->add_action('admin_notices', $plugin_admin, 'print_plugin_admin_notices');

        // adds a settings link to the plugin
        $this->loader->add_filter('plugin_action_links_' . $this->get_plugin_basename(), $plugin_admin, 'whalestack_settings_link');

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @access    private
	 */
	private function define_public_hooks() {

		$plugin_public = new Frontend\Frontend($this->get_plugin_name(), $this->get_version(), $this->get_plugin_text_domain(), $this->get_plugin_name_url());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');

		// registers the checkout button short code
		$this->loader->add_shortcode('Whalestack_checkout', $plugin_public, 'whalestack_render_shortcode_form');

		// when a form is submitted to admin-post.php
		$this->loader->add_action('admin_post_nopriv_submit_whalestack_checkout', $plugin_public, 'public_form_response_handler');
		$this->loader->add_action('admin_post_submit_whalestack_checkout', $plugin_public, 'public_form_response_handler');

		// when a form is submitted to admin-ajax.php
		$this->loader->add_action('wp_ajax_nopriv_submit_whalestack_checkout', $plugin_public, 'public_form_response_handler');
		$this->loader->add_action('wp_ajax_submit_whalestack_checkout', $plugin_public, 'public_form_response_handler');

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @return    Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	public function get_version() {
		return $this->version;
	}

	public function get_plugin_text_domain() {
		return $this->plugin_text_domain;
	}

	public function get_plugin_name_url() {
		return $this->plugin_name_url;
	}

    public function get_plugin_basename() {
        return $this->plugin_basename;
    }

    public function get_plugin_data() {
        global $wp_version;
        $wp = 'WP ' . $wp_version;
        $whalestack = 'Whalestack ' . $this->get_version();
        return $wp . ', ' . $whalestack;
    }

}
