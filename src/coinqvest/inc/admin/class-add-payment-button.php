<?php
namespace COINQVEST\Inc\Admin;
use COINQVEST\Inc\Common\Common_Helpers;
use COINQVEST\Inc\Libraries\API;

class Add_Payment_Button {

	private $redirect;

	public function __construct() {

	}

	public function render_add_payment_button_page(){

	    $helpers = new Common_Helpers();
	    $api_credentials = $helpers->get_coinqvest_credentials();

		?>

        <div class="wrap">

            <h2><?php _e('Add New Payment Button', 'coinqvest')?></h2>

            <?php if (is_null($api_credentials)) { ?>
                <div class="notice notice-error"><?php _e('Please enter your API key and API secret first before you can create a payment button:', 'coinqvest')?> <a href="/wp-admin/admin.php?page=coinqvest-settings"><?php _e('API Settings', 'coinqvest')?></a></div>
            <?php } ?>

            <p>
	            <?php _e('Provide a name and a JSON object, according to the COINQVEST API documentation', 'coinqvest')?>: <br><a href="https://www.coinqvest.com/en/api-docs#post-checkout-hosted" target="_blank">https://www.coinqvest.com/en/api-docs#post-checkout-hosted</a>
            </p>

            <div id="coinqvest_form_feedback"></div>

            <form action="<?=esc_url(admin_url('admin-post.php'));?>" method="POST" id="coinqvest_ajax_form">

                <input type="hidden" name="action" value="coinqvest_admin_form_response">
                <?php wp_nonce_field( 'addPaymentButton-dfs!%sd' ); ?>
                <input type="hidden" name="task" value="add_payment_button">

                <table class="form-table" role="presentation">

                    <tr>
                        <th scope="row"><?php _e('Button Name', 'coinqvest')?></th>
                        <td><input name="cq_button_name" type="text" id="cq_button_name" value="" class="regular-text" /></td>
                    </tr>

                    <tr>
                        <th scope="row"><?php _e('JSON Object', 'coinqvest')?></th>
                        <td>
                            <textarea name="cq_button_json" rows="5" style="width:500px"></textarea>

                            <p class="description"><?php _e('Example of a minimal JSON object:', 'coinqvest')?></p>

                            <pre class="json"><?=Common_Helpers::pretty_json_example()?></pre>

                            <p class="description"><?php _e('To get started, just copy and paste this example and adjust parameters to your requirements.', 'coinqvest')?></p>

                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><?php _e('Button Text', 'coinqvest')?> <span class="optional">(<?php _e('optional', 'coinqvest')?>)</span></th>
                        <td>
                            <input name="cq_button_text" type="text" id="cq_button_text" value="" placeholder="<?php _e('Buy Now', 'coinqvest')?>" class="regular-text" />
                            <p class="description">
                                <?php _e('Customize the button text. Default is "Buy Now".', 'coinqvest')?>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><?php _e('Button CSS Class', 'coinqvest')?> <span class="optional">(<?php _e('optional', 'coinqvest')?>)</span></th>
                        <td>
                            <input name="cq_button_css_class" type="text" id="cq_button_css_class" value="" placeholder="my-custom-button-class" class="regular-text" />
                            <p class="description"><?php _e('Customize the button style. Add a CSS class here.', 'coinqvest')?></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><?php _e('Status', 'coinqvest')?></th>
                        <td><input name="cq_button_status" type="checkbox" id="cq_button_status" checked /> <?php _e('active', 'coinqvest')?></td>
                    </tr>

                </table>

                <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save', 'coinqvest')?>" /></p>

            </form>

        </div>

		<?php

	}

	public function submit_form_add_payment_button() {

		/**
		 * Input validation
		 */

		if (empty($_POST['cq_button_name']) || empty($_POST['cq_button_json'])) {
			$result = "error";
			$message = __('Please fill in all fields.', 'coinqvest');
			$page = "coinqvest-create-payment-button";

			if (isset($_POST['ajaxrequest']) && $_POST['ajaxrequest'] === 'true') {
				echo json_encode(
					array(
						"success" => false,
						"message" => $message
					)
				);
			} else {
				$this->redirect = new Admin_Helpers();
				$this->redirect->custom_redirect($result, $message, $page);
			}
			exit;
		}

		if (strlen($_POST['cq_button_name']) > 56) {
			$result = "error";
			$message = sprintf(__('Name is too long. Max. %s characters.', 'coinqvest'), 56);
			$page = "coinqvest-create-payment-button";

			if (isset($_POST['ajaxrequest']) && $_POST['ajaxrequest'] === 'true') {
				echo json_encode(
					array(
						"success" => false,
						"message" => $message
					)
				);
			} else {
				$this->redirect = new Admin_Helpers();
				$this->redirect->custom_redirect($result, $message, $page);
			}
			exit;
		}

		if (isset($_POST['cq_button_text']) && strlen($_POST['cq_button_text']) > 50) {
			$result = "error";
			$message = sprintf(__('Button text is too long. Max. %s characters.', 'coinqvest'), 50);
			$page = "coinqvest-create-payment-button";

			if (isset($_POST['ajaxrequest']) && $_POST['ajaxrequest'] === 'true') {
				echo json_encode(
					array(
						"success" => false,
						"message" => $message
					)
				);
			} else {
				$this->redirect = new Admin_Helpers();
				$this->redirect->custom_redirect($result, $message, $page);
			}
			exit;
		}

		/**
		 * Load API key and secret
		 */
		$api_settings = get_option('coinqvest_settings');
		$api_settings = unserialize($api_settings);

		if (empty($api_settings['api_key']) || empty($api_settings['api_secret'])) {
			$result = "error";
			$message = __('API key and API secret don\'t exist.', 'coinqvest');;
			$page = "coinqvest-create-payment-button";

			if (isset($_POST['ajaxrequest']) && $_POST['ajaxrequest'] === 'true') {
				echo json_encode(
					array(
						"success" => false,
						"message" => $message
					)
				);
			} else {
				$this->redirect = new Admin_Helpers();
				$this->redirect->custom_redirect($result, $message, $page);
			}
			exit;
		}

		/**
		 * Sanitize input parameters
		 */

		$name = sanitize_text_field($_POST['cq_button_name']);
		$css_class = !empty($_POST['cq_button_css_class']) ? $this->clean(sanitize_text_field($_POST['cq_button_css_class'])) : '';
		$button_text = !empty($_POST['cq_button_text']) ? sanitize_text_field($_POST['cq_button_text']) : __('Buy Now', 'coinqvest');
		$status = isset($_POST['cq_button_status']) ? 1 : 0;
		$json = sanitize_text_field($_POST['cq_button_json']);
		$json = str_replace("\\", "", $json);

		/**
		 * JSON object validation
		 */

		$client = new API\CQMerchantClient(
			$api_settings['api_key'],
			$api_settings['api_secret'],
            true
		);

		$response = $client->post('/checkout/validate', json_decode($json, true));

		if ($response->httpStatusCode != 200) {

			$result = "error";
			$message = "Status Code: " . $response->httpStatusCode . " - " . $response->responseBody;
			$page = "coinqvest-create-payment-button";

			$log = new API\CQLoggingService();
			$log::write("[CQ Add Payment Button] " . $message);

			if (isset($_POST['ajaxrequest']) && $_POST['ajaxrequest'] === 'true') {
				echo json_encode(
					array(
						"success" => false,
						"message" => $message
					)
				);
			} else {
				$this->redirect = new Admin_Helpers();
				$this->redirect->custom_redirect($result, $message, $page);
			}
			exit;

		}

		/**
		 * Save to database
		 */

		global $wpdb;
		$table_name = $wpdb->prefix . 'coinqvest_payment_buttons';

		$wpdb->insert(
			$table_name,
			array(
				'time' => current_time( 'mysql' ),
				'status' => $status,
				'name' => $name,
				'json' => $json,
                'cssclass' => $css_class,
                'buttontext' => $button_text
			)
		);

		$last_id = $wpdb->insert_id;

		$hash_id = rand(2533, 9563) . $last_id;

		$wpdb->update(
			$table_name,
			array(
                'hashid' => $hash_id
            ),
			array('id' => $last_id)
		);


		$message = __('Payment button created successfully.', 'coinqvest');

		if (isset($_POST['ajaxrequest']) && $_POST['ajaxrequest'] === 'true') {
			echo json_encode(
				array(
					"success" => true,
					"message" => $message,
                    "redirect" => "/wp-admin/admin.php?page=coinqvest-payment-buttons",
                    "clear" => true
				)
			);
		} else {
			$result = "success";
			$page = "coinqvest-payment-buttons";
			$this->redirect = new Admin_Helpers();
			$this->redirect->custom_redirect($result, $message, $page);
		}
		exit;

	}


	public function clean($string) {
		$string = preg_replace('/[^A-Za-z0-9\-_\s]/', '', $string); // Removes special chars except - and _ and blanks
		return $string;
	}
}