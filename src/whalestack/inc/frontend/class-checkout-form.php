<?php

namespace Whalestack\Inc\Frontend;
use Whalestack\Inc\Libraries\Api;
use Whalestack\Inc\Common\Common_Helpers;

class Checkout_Form {

	private $plugin_name_url;

	public function __construct($plugin_name_url) {

		$this->plugin_name_url = $plugin_name_url;

	}

	/**
     * Displays the checkout form from shortcode id
     *
	 * @param $params
	 *
	 * @return string
	 */
	public function render_checkout_form($params) {

		$id = absint($params['id']);

		/**
		 * Input Validation
		 */

		global $wpdb;
		$table_name = $wpdb->prefix . 'whalestack_payment_buttons';

		$row = $wpdb->get_row("SELECT hashid, status, cssclass, buttontext, json, total, decimals, currency FROM " . $table_name . " WHERE hashid = " . $id);

		if (!$row) {
			$log = new Api\Whalestack_Logging_Service();
			$log::write('[Whalestack Frontend Shortcode Display] [Whalestack_checkout id="' . $id . '"] doesn\'t exist but is embedded on your website.');
			return '<div class="whalestack_payments_error_msg whalestack-text-color-red">' . esc_html(__('Something is wrong with your Whalestack checkout shortcode.', 'whalestack')) . '</div>';
		}

		// validate that button is active
		if ($row->status != 1) {
			$log = new Api\Whalestack_Logging_Service();
			$log::write('[Whalestack Frontend Shortcode Display] [Whalestack_checkout id="' . $id . '"] is not active but is embedded on your website.');
			return '<p class="whalestack-text-color-red">' . esc_html(__('Your Whalestack checkout button is not active.', 'whalestack')) . '</p>';
		}

		/**
		 * Define parameters
		 */

        // get current user to pre-populate form fields
		$user = wp_get_current_user();
		$user_meta = get_user_meta($user->ID);

		// get the customer info to generate the correct form (none, minimal or compliant)
		$settings = unserialize(get_option('whalestack_settings'));
		$customer_info = $settings['customer_info'];

		$params = array();
		$params['hashid'] = $row->hashid;
		$params['nonce'] = 'submit_whalestack_checkout_8b%kj@';
		$params['user_name'] = isset($user_meta['nickname'][0]) ? $user_meta['nickname'][0] : null;
		$params['first_name'] = isset($user_meta['first_name'][0]) ? $user_meta['first_name'][0] : null;
		$params['last_name'] = isset($user_meta['last_name'][0]) ? $user_meta['last_name'][0] : null;
		$params['email'] = isset($user->user_email) ? $user->user_email : null;
		$params['css_class'] = !empty($row->cssclass) ? $row->cssclass : null;
        $params['button_text'] = is_null($row->buttontext) ? __('Buy Now', 'whalestack') : $row->buttontext;
		$params['customer_info'] = $customer_info;
        $params['display_price'] = Common_Helpers::format_display_price($row->total, $row->currency, $row->decimals);

		/**
		 * Render the checkout form
		 */

		$html = '';

		if ($customer_info == 'none') {

		    $html = $this->render_checkout_button($params);

        } elseif (in_array($customer_info, array('minimal', 'compliant'))) {

		    $html = $this->render_checkout_modal($params);

        }

        return $html;

	}

	public function render_checkout_button($params) {

	    $output = '
        <div id="whalestack_checkout_button">
            <form action="' . esc_url(admin_url("admin-post.php")) . '" method="POST">
                <input type="hidden" name="action" value="submit_whalestack_checkout">
		        ' . wp_nonce_field($params['nonce']) . '
                <input type="hidden" name="whalestack_checkout_id" value="' . esc_attr($params['hashid']) . '">
                <button type="submit" class="button ' . esc_attr($params['css_class']) . '">' . esc_html($params['button_text']) . '</button>
            </form>
        </div>';

	    return $output;

    }

	public function render_checkout_modal($params) {

		$countries = '';
		include("views/partials-wp-country-list.php");

	    $output = '
        <div class="whalestack-checkout-button">
            <a class="button ' . esc_attr($params['css_class']) . '" href="#whalestack-' . esc_attr($params['hashid']) . '" rel="whalestackmodal:open">' . esc_html($params['button_text']) . '</a>
        </div>

        <div id="whalestack-' . esc_attr($params['hashid']) . '" class="whalestack-modal">

            <div class="whalestack-checkout">

                <div class="whalestack-grid">

                    <div class="whalestack-row">
                        <div class="whalestack-col-12">
                            <p class="whalestack-price">' . esc_html($params['display_price']) . '</p>
                        </div>
                    </div>

                    <form id="whalestack-checkout-form-' . esc_attr($params['hashid']) . '" action="' . esc_url(admin_url('admin-post.php')) . '" method="POST">

                        <input type="hidden" name="action" value="submit_whalestack_checkout">
						' . wp_nonce_field( 'submit_whalestack_checkout_8b%kj@' ) . '
                        <input type="hidden" name="whalestack_checkout_id" value="' . esc_attr($params['hashid']) . '">
                        <input type="hidden" name="whalestack_user_name" value="' . esc_attr($params['user_name']) . '">

                        <div class="whalestack-row whalestack-feedback-row whalestack-hide">
                            <div class="whalestack-col-12">
                                <div id="whalestack-feedback"></div>
                            </div>
                        </div>

                        <div class="whalestack-row">
                            <div class="whalestack-col-6 whalestack-margin-right-4percent">
                                <p class="whalestack-label">' . esc_html(__('First name', 'whalestack')) . '</p>
                                <input type="text" class="whalestack-input" name="whalestack_first_name" maxlength="32" value="' . esc_attr($params['first_name']) .'">
                            </div>
                            <div class="whalestack-col-6">
                                <p class="whalestack-label">' . esc_html(__('Last name', 'whalestack')) . '</p>
                                <input type="text" class="whalestack-input" name="whalestack_last_name" maxlength="32" value="' . esc_attr($params['last_name']) . '" >
                            </div>
                        </div>

                        <div class="whalestack-row">
                            <div class="whalestack-col-12">
                                <p class="whalestack-label">' . esc_html(__('Email', 'whalestack' )) . '</p>
                                <input type="text" class="whalestack-input" name="whalestack_email" maxlength="64" value="' . esc_attr($params['email']) .'" >
                            </div>
                        </div>';

						if ($params['customer_info'] == 'compliant') {

						    $output .= '
						    <div class="whalestack-row">
                                <div class="whalestack-col-6 whalestack-margin-right-4percent">
                                    <p class="whalestack-label">' . esc_html(__('Company', 'whalestack')) . ' <span class="whalestack-tip">(' . esc_html(__('optional', 'whalestack')) . ')</span></p>
                                    <input type="text" class="whalestack-input" name="whalestack_company" maxlength="64">
                                </div>
                                <div class="whalestack-col-6">
                                    <p class="whalestack-label">' . esc_html(__('Tax ID', 'whalestack')) . ' <span class="whalestack-tip">(' . esc_html(__('optional', 'whalestack')) . ')</span></p>
                                    <input type="text" class="whalestack-input" name="whalestack_tax_id" maxlength="32" >
                                </div>
                            </div>

                            <div class="whalestack-row">
                                <div class="whalestack-col-6 whalestack-margin-right-4percent">
                                    <p class="whalestack-label">' . esc_html(__('Address Line 1', 'whalestack')) . '</p>
                                    <input type="text" class="whalestack-input" name="whalestack_adr1" maxlength="32">
                                </div>
                                <div class="whalestack-col-6">
                                    <p class="whalestack-label">' . esc_html(__('Address Line 2', 'whalestack')) . ' <span class="whalestack-tip">(' . esc_html(__('optional', 'whalestack')) . ')</span></p>
                                    <input type="text" class="whalestack-input" name="whalestack_adr2" maxlength="32" >
                                </div>
                            </div>

                            <div class="whalestack-row">
                                <div class="whalestack-col-6 whalestack-margin-right-4percent">
                                    <p class="whalestack-label">' . esc_html(__('ZIP code (and State)', 'whalestack')) . '</p>
                                    <input type="text" class="whalestack-input" name="whalestack_zip" maxlength="12">
                                </div>
                                <div class="whalestack-col-6">
                                    <p class="whalestack-label">' . esc_html(__('City', 'whalestack')) . '</p>
                                    <input type="text" class="whalestack-input" name="whalestack_city" maxlength="64" >
                                </div>
                            </div>

                            <div class="whalestack-row">
                                <div class="whalestack-col-6 whalestack-margin-right-4percent">
                                    <p class="whalestack-label">' . esc_html(__('Country', 'whalestack')) . '</p>
                                  	' . $countries . '
                                </div>
                                <div class="whalestack-col-6">
                                    <p class="whalestack-label">' . esc_html(__('Phone number', 'whalestack')) . ' <span class="whalestack-tip">(' . esc_html(__('optional', 'whalestack')) . ')</span></p>
                                    <input type="text" class="whalestack-input" name="whalestack_phone_number" maxlength="16" >
                                </div>
                            </div>';

                        }

                        $output .= '
                        <div class="whalestack-row whalestack-show-button">
                            <div class="whalestack-col-12">
                                <button class="whalestack-blue-button" type="submit">' . esc_html(__('Pay Now', 'whalestack')) . '</button>
                            </div>
                        </div>

                        <div class="whalestack-row whalestack-show-loader whalestack-hide">
                            <div class="whalestack-col-12">
                                <div class="whalestack-gray-button">
                                    <img src="' . esc_url($this->plugin_name_url) . 'assets/images/ajax-loader-for-forms.gif" width="18" height="18" class="whalestack-loader">
                                </div>
                            </div>
                        </div>

                    </form>

                    <div class="whalestack-row">
                        <div class="whalestack-col-6 whalestack-margin-right-4percent whalestack-center-xs">
                            <a href="#" rel="whalestackmodal:close" class="whalestack-cancel">' . esc_html(__('Cancel Payment', 'whalestack')) . '</a>
                        </div>
                        <div class="whalestack-col-6 whalestack-center-xs">
                            <img class="whalestack-logo" src="' . esc_url($this->plugin_name_url) . 'assets/images/whalestack-logo.png" width="100px">
                            <div class="whalestack-clear-both"></div>
                        </div>
                    </div>

                </div>

            </div>

        </div>
        
        <script>jQuery("a[href=#whalestack-' . $params['hashid'] . ']").click(function(e){e.preventDefault(),jQuery(this).whalestack_modal()});</script>';

	    return $output;
	}


	/**
	 * Processes the checkout form submit
	 */

	public function process_checkout() {

        /**
         * Sanitize input parameters
         */

        $id = absint($_POST['whalestack_checkout_id']);
        $email = isset($_POST['whalestack_email']) ? sanitize_email($_POST['whalestack_email']) : null;
        $first_name = isset($_POST['whalestack_first_name']) ? sanitize_text_field($_POST['whalestack_first_name']) : null;
        $last_name = isset($_POST['whalestack_last_name']) ? sanitize_text_field($_POST['whalestack_last_name']) : null;
        $user_name = isset($_POST['whalestack_user_name']) ? sanitize_text_field($_POST['whalestack_user_name']) : null;
        $company = isset($_POST['whalestack_company']) ? sanitize_text_field($_POST['whalestack_company']) : null;
        $tax_id = isset($_POST['whalestack_tax_id']) ? sanitize_text_field($_POST['whalestack_tax_id']) : null;
        $adr1 = isset($_POST['whalestack_adr1']) ? sanitize_text_field($_POST['whalestack_adr1']) : null;
        $adr2 = isset($_POST['whalestack_adr2']) ? sanitize_text_field($_POST['whalestack_adr2']) : null;
        $zip = isset($_POST['whalestack_zip']) ? sanitize_text_field($_POST['whalestack_zip']) : null;
        $city = isset($_POST['whalestack_city']) ? sanitize_text_field($_POST['whalestack_city']) : null;
        $country_code = isset($_POST['whalestack_country']) ? sanitize_text_field($_POST['whalestack_country']) : null;
        $phone_number = isset($_POST['whalestack_phone_number']) ? sanitize_text_field($_POST['whalestack_phone_number']) : null;
        $is_ajax = isset( $_POST['ajaxrequest']) && $_POST['ajaxrequest'] === 'true';

        /**
         * Input Validation
         */

		// validate that button hashid exists
		global $wpdb;
		$table_name = $wpdb->prefix . 'whalestack_payment_buttons';

		$row = $wpdb->get_row("SELECT hashid, status, json FROM " . $table_name . " WHERE hashid = " . $id);

		if (!$row) {
            $message = esc_html(sprintf(__('Payment button id %s does not exist.', 'whalestack'), $id));
            Frontend_Helpers::renderErrorMessage($message);
		}

		// validate that button is active
		if ($row->status != 1) {
            $message = esc_html(sprintf(__('Payment button id %s is inactive.', 'whalestack'), $id));
            Frontend_Helpers::renderErrorMessage($message);
		}

		$settings = unserialize(get_option('whalestack_settings'));

		// none, minimal or compliant
		$customer_info = $settings['customer_info'];

		if (in_array($customer_info, array('minimal', 'compliant'))) {

		    $requiredFields = array();

		    if ($customer_info == 'minimal') {

			    $requiredFields = array('whalestack_first_name', 'whalestack_last_name', 'whalestack_email');

            } elseif ($customer_info == 'compliant') {

			    $requiredFields = array('whalestack_first_name', 'whalestack_last_name', 'whalestack_email', 'whalestack_adr1', 'whalestack_zip', 'whalestack_city', 'whalestack_country');
            }

			$errors = Common_Helpers::validate_required_form_fields($requiredFields, $_POST);
			if ($errors) {
                $message = esc_html(__('Please provide all highlighted fields.', 'whalestack'));
                Frontend_Helpers::renderErrorMessage($message, $errors);
			}

			if (!is_email($email)) {
                $message = esc_html(__('Please provide a valid email address.', 'whalestack'));
                Frontend_Helpers::renderErrorMessage($message, ['whalestack_email']);
			}

		}

		/**
		 * Init the Whalestack API
		 */

		$client = new Api\Whalestack_Merchant_Client($settings['api_key'], $settings['api_secret'], true);

		/**
		 * Create a customer first
		 */

		$customer_id = null;

		if (in_array($customer_info, array('minimal', 'compliant'))) {

			$response = $client->post('/customer', array('customer' => array(
				'email' => $email,
				'firstname' => $first_name,
				'lastname' => $last_name,
                'company' => $company,
                'adr1' => $adr1,
                'adr2' => $adr2,
                'zip' => $zip,
                'city' => $city,
                'countrycode' => $country_code,
				'phonenumber' => $phone_number,
				'taxid' => $tax_id,
				'meta' => array(
					'source' => 'Wordpress',
					'username' => $user_name
				)
			)));

			if ($response->httpStatusCode != 200) {
                $message = esc_html(__('Failed to create customer. Please try again later.', 'whalestack'));
                Frontend_Helpers::renderErrorMessage($message);
			}

			$data = json_decode($response->responseBody, true);
			$customer_id = $data['customerId']; // use this to associate a checkout with this customer

		}

		/**
		 * Build the checkout array
		 * Important: Global settings overwrite JSON parameters
		 */

		$checkout = json_decode($row->json, true);

		$checkout = Common_Helpers::useNewParameterNaming($checkout);

		$checkout['charge']['customerId'] = $customer_id;

		if (isset($settings['settlement_currency']) && $settings['settlement_currency'] != "0") {
			$checkout['settlementAsset'] = $settings['settlement_currency'];
		}

        if (isset($settings['checkout_language']) && $settings['checkout_language'] != "0") {
            $checkout['checkoutLanguage'] = $settings['checkout_language'];
        }

		if (isset($settings['webhook_url']) && !empty($settings['webhook_url'])) {
			$checkout['webhook'] = $settings['webhook_url'];
		}

		if (isset($settings['cancel_url']) && !empty($settings['cancel_url'])) {
			$checkout['pageSettings']['cancelUrl'] = $settings['cancel_url'];
		}

		if (isset($settings['return_url']) && !empty($settings['return_url'])) {
			$checkout['pageSettings']['returnUrl'] = $settings['return_url'];
		}

        /**
         * Send the checkout
         */

		$response = $client->post('/checkout/hosted', $checkout);
		if ($response->httpStatusCode != 200) {
            $message = esc_html(__('Failed to create checkout. Please try again later.', 'whalestack'));
            Frontend_Helpers::renderErrorMessage($message);
		}

		/**
		 * The checkout was created, redirect user to hosted checkout page
		 */

		$data = json_decode($response->responseBody, true);
		$url = $data['url'];

        if ($is_ajax === true) {
            $message = esc_html(__('Success. You will be redirected to the checkout page.', 'whalestack'));
            Frontend_Helpers::renderSuccessMessage($message, $url);
		}

		wp_redirect($url);

	}

}