<?php

namespace COINQVEST\Inc\Frontend;

class Frontend {

	private $plugin_name;
	private $version;
	private $plugin_text_domain;
	private $plugin_name_url;
	private $checkout_form;

	/**
	 * Initialize the class and set its properties.
	 */
	public function __construct($plugin_name, $version, $plugin_text_domain, $plugin_name_url) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->plugin_text_domain = $plugin_text_domain;
		$this->plugin_name_url = $plugin_name_url;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 */
	public function enqueue_styles() {
		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/coinqvest.modal.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 */
	public function enqueue_scripts() {
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/coinqvest.modal.min.js', array('jquery'), $this->version, false);

		$params = array ('ajaxurl' => admin_url('admin-ajax.php' ) );
		wp_enqueue_script('coinqvest_ajax_handle', plugin_dir_url( __FILE__ ) . 'js/coinqvest-frontend-ajax-handler.js', array('jquery'), $this->version, false);
		wp_localize_script('coinqvest_ajax_handle', 'params', $params);
	}

	/**
	 * Renders the checkout button form
	 */
	public function coinqvest_render_shortcode_form($params) {

		$this->checkout_form = new Checkout_Form($this->plugin_name_url);
		$html = $this->checkout_form->render_checkout_form($params);
		return $html;

	}

	/**
	 * form submits are handled here, both Ajax and POST (POST is fallback if Ajax doesn't work)
	 */
	public function public_form_response_handler() {

		$nonce = $_POST['_wpnonce'];
        if (!wp_verify_nonce( $nonce, 'submit_coinqvest_checkout_8b%kj@')) {
            exit; // Get out of here, the nonce is rotten!
        }

		$this->checkout_form = new Checkout_Form($this->plugin_name_url);
		$this->checkout_form->process_checkout();

	}

}
