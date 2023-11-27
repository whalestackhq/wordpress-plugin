<?php

namespace Whalestack\Inc\Admin;

class Admin {

	private $plugin_name;
	private $version;
	private $plugin_text_domain;
	private $plugin_name_url;

	private $whalestack_start;
	private $payment_buttons_list_table;
	private $add_payment_button;
	private $edit_payment_button;
	private $logs;
	private $settings;
	private $admin_notices;

	public function __construct($plugin_name, $version, $plugin_text_domain, $plugin_name_url) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->plugin_text_domain = $plugin_text_domain;
		$this->plugin_name_url = $plugin_name_url;
	}

	/**
	 * Register the stylesheets for the admin area.
	 */
	public function enqueue_styles() {
		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/whalestack-admin.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
	 */
	public function enqueue_scripts() {
		$params = array ('ajaxurl' => admin_url('admin-ajax.php'));
		wp_enqueue_script('whalestack_ajax_handle', plugin_dir_url(__FILE__) . 'js/whalestack-admin-ajax-handler.js', array('jquery'), $this->version, false);
		wp_localize_script('whalestack_ajax_handle', 'params', $params);
	}
	
	/**
	 * Callback for the user sub-menu in define_admin_hooks() for class Init.
	 */
	public function add_plugin_admin_menu() {

		add_menu_page('Whalestack', 'Whalestack', 'manage_options', 'whalestack', array($this, 'start_whalestack'), $this->plugin_name_url . 'assets/images/favicon-22x22.png');
		$page_hook = add_submenu_page('whalestack', 'Whalestack Payment Buttons', 'Payment Buttons', 'manage_options', 'whalestack-payment-buttons', array($this, 'payment_buttons'));
		add_submenu_page('whalestack', 'Whalestack Create New Payment Button', 'Add Payment Button', 'manage_options', 'whalestack-add-payment-button', array($this, 'add_payment_button'));
		add_submenu_page(null, 'Whalestack Edit Payment Button', 'Edit Payment Button', 'manage_options', 'whalestack-edit-payment-button', array($this, 'edit_payment_button'));
		add_submenu_page('whalestack', 'Whalestack Logs', 'Logs', 'manage_options', 'whalestack-logs', array($this, 'logs'));
		add_submenu_page('whalestack', 'Whalestack Settings', 'Settings', 'manage_options', 'whalestack-settings', array($this, 'settings'));

		/*
		 * The $page_hook_suffix can be combined with the load-($page_hook) action hook
		 * https://codex.wordpress.org/Plugin_API/Action_Reference/load-(page) 
		 * 
		 * The callback below will be called when the respective page is loaded
		 */
		add_action('load-'.$page_hook, array($this, 'payment_buttons_screen_options'));

	}

	/**
	 * form submits are handled here, both Ajax and POST (fallback if Ajax doesn't work)
	 */
	public function admin_form_response_handler() {

		if (!is_user_logged_in()) {
			exit;
		}

		$nonce = $_POST['_wpnonce'];
		$task = $_POST['task'];

		switch ($task) {

			case 'submit_api_settings':

				if (!wp_verify_nonce($nonce, 'submitApiSettings-23iyj@h!')) {
					exit;
				}
				$this->settings = new Settings();
				$this->settings->submit_form_api_settings();
				break;

			case 'submit_global_settings':

				if (!wp_verify_nonce($nonce, 'submitGlobalSettings-abg3@9')) {
					exit;
				}
				$this->settings = new Settings();
				$this->settings->submit_form_global_settings();
				break;

			case 'add_payment_button':

				if (!wp_verify_nonce($nonce, 'addPaymentButton-dfs!%sd')) {
					exit;
				}
				$this->add_payment_button = new Add_Payment_Button();
				$this->add_payment_button->submit_form_add_payment_button();
				break;

			case 'edit_payment_button':

				if (!wp_verify_nonce($nonce, 'editPaymentButton-dfs!%sd')) {
					exit;
				}
				$this->edit_payment_button = new Edit_Payment_Button();
				$this->edit_payment_button->submit_form_edit_payment_button();
				break;

		}

	}

	/**
	 * Admin notices when form submit with POST (adds success/error parameters to URL)
	 */
	public function print_plugin_admin_notices() {
		$this->admin_notices = new Admin_Helpers();
		$this->admin_notices->print_plugin_admin_notices();
	}

	/**
	 * Render the pages
	 */

	public function start_whalestack(){
		$this->whalestack_start = new Whalestack_Start($this->plugin_name_url);
		$this->whalestack_start->render_whalestack_start_page();
	}

	public function add_payment_button() {
		$this->add_payment_button = new Add_Payment_Button();
		$this->add_payment_button->render_add_payment_button_page();
	}

	public function edit_payment_button() {
		$this->edit_payment_button = new Edit_Payment_Button();
		$this->edit_payment_button->render_edit_payment_button_page();
	}

	public function logs(){
		$this->logs = new Logs();
		$this->logs->render_logs_page();
	}

	public function settings(){
		$this->settings = new Settings();
		$this->settings->render_settings_page();
	}

	public function payment_buttons(){
		$this->payment_buttons_list_table->prepare_items();
		// render the payment buttons list table
		include_once('views/partials-wp-payment-buttons-display.php');
	}

	public function payment_buttons_screen_options() {

		$arguments = array(
            'label' => __('Items Per Page', $this->plugin_text_domain),
            'default' => 20,
            'option' =>	'items_per_page'
        );

		add_screen_option('per_page', $arguments);

		$this->payment_buttons_list_table = new Payment_Buttons_List_Table($this->plugin_text_domain);

	}

    public function whalestack_settings_link($links) {
        $plugin_links = array(
            '<a href="admin.php?page=whalestack-settings">' . esc_html(__('Settings', 'whalestack')) . '</a>',
        );
        return array_merge($plugin_links, $links);
    }



}