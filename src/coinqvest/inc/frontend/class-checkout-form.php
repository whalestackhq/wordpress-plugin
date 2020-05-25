<?php

namespace COINQVEST\Inc\Frontend;
use COINQVEST\Inc\Libraries\API;
use COINQVEST\Inc\Common\Common_Helpers;

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

		$id = $params['id'];

		/**
		 * Input Validation
		 */

		global $wpdb;
		$table_name = $wpdb->prefix . 'coinqvest_payment_buttons';

		$row = $wpdb->get_row("SELECT hashid, status, cssclass, buttontext, json FROM ".$table_name." WHERE hashid = " . $id);

		if (!$row) {
			$log = new API\CQLoggingService();
			$log::write('[CQ Frontend Shortcode Display] [COINQVEST_checkout id="' . absint($id) . '"] doesn\'t exist but is embedded on your website.');
			return '<div class="coinqvest_payments_error_msg coinqvest-text-color-red">' . __('Something is wrong with your COINQVEST checkout shortcode.', 'coinqvest' ) . '</div>';
		}

		// validate that button is active
		if ($row->status != 1) {
			$log = new API\CQLoggingService();
			$log::write('[CQ Frontend Shortcode Display] [COINQVEST_checkout id="' . absint($id) . '"] is not active but is embedded on your website.');
			return "<p class='coinqvest-text-color-red'>" . __('Your COINQVEST checkout button is not active.', 'coinqvest' ) . "</p>";
		}

		/**
		 * Define parameters
		 */

        // get current user to pre-populate form fields
		$user = wp_get_current_user();
		$user_meta = get_user_meta($user->ID);

		// get the customer info to generate the correct form (none, minimal or compliant)
		$settings = unserialize(get_option('coinqvest_settings'));
		$customer_info = $settings['customer_info'];

		$params = array();
		$params['hashid'] = $row->hashid;
		$params['nonce'] = 'submit_coinqvest_checkout_8b%kj@';
		$params['user_name'] = isset($user_meta['nickname'][0]) ? $user_meta['nickname'][0] : null;
		$params['first_name'] = isset($user_meta['first_name'][0]) ? $user_meta['first_name'][0] : null;
		$params['last_name'] = isset($user_meta['last_name'][0]) ? $user_meta['last_name'][0] : null;
		$params['email'] = isset($user->user_email) ? $user->user_email : null;
		$params['css_class'] = !empty($row->cssclass) ? 'class="' . $row->cssclass . '"' : null;
		$params['button_text'] = !empty($row->buttontext) ? $row->buttontext : "Pay Now";
		$params['customer_info'] = $customer_info;
		$params['display_price'] = Common_Helpers::calculate_price($row->json);

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
        <div id="coinqvest_checkout_button">
            <form action="' . esc_url(admin_url("admin-post.php")) . '" method="POST">
                <input type="hidden" name="action" value="submit_coinqvest_checkout">
		        ' . wp_nonce_field($params['nonce']) . '
                <input type="hidden" name="cq_checkout_id" value="' . $params['hashid'] . '">
                <button type="submit" ' . $params['css_class'] . '>' . __('Buy Now', 'coinqvest') . '</button>
            </form>
        </div>';

	    return $output;

    }

	public function render_checkout_modal($params) {

		$countries = '';
		include("views/partials-wp-country-list.php");

	    $output = '
        <div id="coinqvest-checkout-button">
            <a class="button" href="#coinqvest-' . $params['hashid'] . '" ' . $params['css_class'] . '>' . __('Buy Now', 'coinqvest') . '</a>
        </div>

        <div id="coinqvest-' . $params['hashid'] . '" class="coinqvest-modal">

            <div class="coinqvest-checkout">

                <div class="cq-grid">

                    <div class="cq-row">
                        <div class="cq-col-12">
                            <p class="cq-price">' . $params['display_price'] . '</p>
                        </div>
                    </div>

                    <form id="coinqvest-checkout-form-' . $params['hashid'] . '" action="' . esc_url(admin_url('admin-post.php')) . '" method="POST">

                        <input type="hidden" name="action" value="submit_coinqvest_checkout">
						' . wp_nonce_field( 'submit_coinqvest_checkout_8b%kj@' ) . '
                        <input type="hidden" name="cq_checkout_id" value="' . $params['hashid'] . '">
                        <input type="hidden" name="cq_user_name" value="' . $params['user_name'] . '">

                        <div class="cq-row cq-feedback-row cq-hide">
                            <div class="cq-col-12">
                                <div id="cq-feedback"></div>
                            </div>
                        </div>

                        <div class="cq-row">
                            <div class="cq-col-6 cq-margin-right-4percent">
                                <p class="cq-label">' . __('First name', 'coinqvest') . '</p>
                                <input type="text" class="cq-input" name="cq_first_name" maxlength="32" value="' . $params['first_name'] .'">
                            </div>
                            <div class="cq-col-6">
                                <p class="cq-label">' . __('Last name', 'coinqvest') . '</p>
                                <input type="text" class="cq-input" name="cq_last_name" maxlength="32" value="' . $params['last_name'] . '" >
                            </div>
                        </div>

                        <div class="cq-row">
                            <div class="cq-col-12">
                                <p class="cq-label">' . __('Email', 'coinqvest' ) . '</p>
                                <input type="text" class="cq-input" name="cq_email" maxlength="64" value="' . $params['email'] .'" >
                            </div>
                        </div>';

						if ($params['customer_info'] == 'compliant') {

						    $output .= '
						    <div class="cq-row">
                                <div class="cq-col-6 cq-margin-right-4percent">
                                    <p class="cq-label">' . __('Company', 'coinqvest') . ' <span class="cq-tip">(' . __('optional', 'coinqvest') . ')</span></p>
                                    <input type="text" class="cq-input" name="cq_company" maxlength="64">
                                </div>
                                <div class="cq-col-6">
                                    <p class="cq-label">' . __('Tax ID', 'coinqvest') . ' <span class="cq-tip">(' . __('optional', 'coinqvest') . ')</span></p>
                                    <input type="text" class="cq-input" name="cq_tax_id" maxlength="32" >
                                </div>
                            </div>

                            <div class="cq-row">
                                <div class="cq-col-6 cq-margin-right-4percent">
                                    <p class="cq-label">' . __('Address Line 1', 'coinqvest') . '</p>
                                    <input type="text" class="cq-input" name="cq_adr1" maxlength="32">
                                </div>
                                <div class="cq-col-6">
                                    <p class="cq-label">' . __('Address Line 2', 'coinqvest') . ' <span class="cq-tip">(' . __('optional', 'coinqvest') . ')</span></p>
                                    <input type="text" class="cq-input" name="cq_adr2" maxlength="32" >
                                </div>
                            </div>

                            <div class="cq-row">
                                <div class="cq-col-6 cq-margin-right-4percent">
                                    <p class="cq-label">' . __('ZIP code (and State)', 'coinqvest') . '</p>
                                    <input type="text" class="cq-input" name="cq_zip" maxlength="12">
                                </div>
                                <div class="cq-col-6">
                                    <p class="cq-label">' . __('City', 'coinqvest') . '</p>
                                    <input type="text" class="cq-input" name="cq_city" maxlength="64" >
                                </div>
                            </div>

                            <div class="cq-row">
                                <div class="cq-col-6 cq-margin-right-4percent">
                                    <p class="cq-label">' . __('Country', 'coinqvest') . '</p>
                                  	' . $countries . '
                                </div>
                                <div class="cq-col-6">
                                    <p class="cq-label">' . __('Mobile number', 'coinqvest') . ' <span class="cq-tip">(' . __('optional', 'coinqvest') . ')</span></p>
                                    <input type="text" class="cq-input" name="cq_mobile_number" maxlength="16" >
                                </div>
                            </div>';

                        }

                        $output .= '
                        <div class="cq-row cq-show-button">
                            <div class="cq-col-12">
                                <button class="cq-blue-button" type="submit">' . __('Pay Now', 'coinqvest') . '</button>
                            </div>
                        </div>

                        <div class="cq-row cq-show-loader cq-hide">
                            <div class="cq-col-12">
                                <div class="cq-gray-button">
                                    <img src="' . $this->plugin_name_url . 'assets/images/ajax-loader-for-forms@2x.gif" width="18" height="18" class="cq-loader">
                                </div>
                            </div>
                        </div>

                    </form>

                    <div class="cq-row">
                        <div class="cq-col-6 cq-margin-right-4percent cq-center-xs">
                            <a href="#" rel="modal:close" class="cq-cancel">' . __('Cancel Payment', 'coinqvest') . '</a>
                        </div>
                        <div class="cq-col-6 cq-center-xs">
                            <img src="' . $this->plugin_name_url . 'assets/images/coinqvest-logo.png">
                            <div class="coinqvest-clear-both"></div>
                        </div>
                    </div>

                </div>

            </div>

        </div>
        

        <script>jQuery("a[href=#coinqvest-' . $params['hashid'] . ']").click(function(e){e.preventDefault(),jQuery(this).modal({escapeClose:!1,clickClose:!1,modalClass:"coinqvest-modal",blockerClass:"coinqvest-jquery-modal"})});</script>';

	    return $output;
	}


	/**
	 * Processes the checkout form submit
	 */

	public function process_checkout() {

		/**
		 * Input Validation
		 */

		$id = $_POST['cq_checkout_id'];

		// validate that button hashid exists
		global $wpdb;
		$table_name = $wpdb->prefix . 'coinqvest_payment_buttons';

		$row = $wpdb->get_row("SELECT hashid, status, json FROM ".$table_name." WHERE hashid = " . $id);

		if (!$row) {
			$log = new API\CQLoggingService();
			$log::write('[CQ Frontend Submit Checkout] [COINQVEST_checkout id="' . absint($id) . '"] doesn\'t exist.');
            Common_Helpers::renderResponse(array(
                "success" => false,
                "message" => sprintf(__('Payment button id %s does not exist.', 'coinqvest'), absint($id))
            ));
		}

		// validate that button is active
		if ($row->status != 1) {
			$log = new API\CQLoggingService();
			$log::write('[CQ Frontend Submit Checkout] [COINQVEST_checkout id="' . absint($id) . '"] is not active.');
            Common_Helpers::renderResponse(array(
                "success" => false,
                "message" => sprintf(__('Payment button id %s is inactive.', 'coinqvest'), absint($id))
            ));
		}

		$settings = unserialize(get_option('coinqvest_settings'));

		// none, minimal or compliant
		$customer_info = $settings['customer_info'];

		if (in_array($customer_info, array('minimal', 'compliant'))) {

		    $requiredFields = array();

		    if ($customer_info == 'minimal') {

			    $requiredFields = array('cq_first_name', 'cq_last_name', 'cq_email');

            } elseif ($customer_info == 'compliant') {

			    $requiredFields = array('cq_first_name', 'cq_last_name', 'cq_email', 'cq_adr1', 'cq_zip', 'cq_city', 'cq_country');
            }

			$errors = Common_Helpers::validate_required_form_fields($requiredFields, $_POST);
			if ($errors) {
                Common_Helpers::renderResponse(array(
                    "success" => false,
                    "message" => __('Please provide all highlighted fields.', 'coinqvest'),
                    "highlightFields" => $errors
                ));
			}

			if (!is_email($_POST['cq_email'])) {
                Common_Helpers::renderResponse(array(
                    "success" => false,
                    "message" => __('Please provide a valid email address.', 'coinqvest'),
                    "highlightFields" => ['cq_email']
                ));
			}

		}

		/**
		 * Sanitize input parameters
		 */

		$email = isset($_POST['cq_email']) ? $_POST['cq_email'] : null;
		$first_name = isset($_POST['cq_first_name']) ? sanitize_text_field($_POST['cq_first_name']) : null;
		$last_name = isset($_POST['cq_last_name']) ? sanitize_text_field($_POST['cq_last_name']) : null;
		$user_name = isset($_POST['cq_user_name']) ? sanitize_text_field($_POST['cq_user_name']) : null;
		$company = isset($_POST['cq_company']) ? sanitize_text_field($_POST['cq_company']) : null;
		$tax_id = isset($_POST['cq_tax_id']) ? sanitize_text_field($_POST['cq_tax_id']) : null;
		$adr1 = isset($_POST['cq_adr1']) ? sanitize_text_field($_POST['cq_adr1']) : null;
		$adr2 = isset($_POST['cq_adr2']) ? sanitize_text_field($_POST['cq_adr2']) : null;
		$zip = isset($_POST['cq_zip']) ? sanitize_text_field($_POST['cq_zip']) : null;
		$city = isset($_POST['cq_city']) ? sanitize_text_field($_POST['cq_city']) : null;
		$country_code = isset($_POST['cq_country']) ? sanitize_text_field($_POST['cq_country']) : null;
		$mobile_number = isset($_POST['cq_mobile_number']) ? sanitize_text_field($_POST['cq_mobile_number']) : null;

		/**
		 * Init the COINQVEST API
		 */

		$client = new API\CQMerchantClient(
			$settings['api_key'],
			$settings['api_secret'],
			true
		);

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
				'mobilenumber' => $mobile_number,
				'taxid' => $tax_id,
				'meta' => array(
					'source' => 'Wordpress',
					'username' => $user_name
				)
			)));

			if ($response->httpStatusCode != 200) {
                Common_Helpers::renderResponse(array(
                    "success" => false,
                    "message" => __('Failed to create customer. Please try again later. [Error Code: CCF2]', 'coinqvest')
                ));
			}

			$data = json_decode($response->responseBody, true);
			$customer_id = $data['customerId']; // use this to associate a checkout with this customer

		}

		/**
		 * Build the checkout array
		 * A global setting overwrites a JSON parameter
		 */

		$checkout = json_decode($row->json, true);

		$checkout['charge']['customerId'] = $customer_id;

		if (isset($settings['settlement_currency']) && $settings['settlement_currency'] != "0") {
			$checkout['settlementCurrency'] = $settings['settlement_currency'];
		}

		if (isset($settings['webhook_url']) && !empty($settings['webhook_url'])) {
			$checkout['webhook'] = $settings['webhook_url'];
		}

		if (isset($settings['cancel_url']) && !empty($settings['cancel_url'])) {
			$checkout['links']['cancelUrl'] = $settings['cancel_url'];
		}

		if (isset($settings['return_url']) && !empty($settings['return_url'])) {
			$checkout['links']['returnUrl'] = $settings['return_url'];
		}


		$response = $client->post('/checkout/hosted', $checkout);

		if ($response->httpStatusCode != 200) {
            Common_Helpers::renderResponse(array(
                "success" => false,
                "message" => __('Failed to create checkout. Please try again later.', 'coinqvest')
            ));
		}

		/**
		 * The checkout was created, redirect user to hosted checkout page
		 */

		$data = json_decode($response->responseBody, true);
		$url = $data['url'];

		if (isset($_POST['ajaxrequest']) && $_POST['ajaxrequest'] === 'true') {
            Common_Helpers::renderResponse(array(
                "success" => true,
                "message" => __('Success. You will be redirected to the checkout page.', 'coinqvest'),
                "redirect" => $url
            ));
		}

		wp_redirect($url);

	}

}