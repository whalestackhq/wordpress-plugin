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

            <h2><?php echo esc_html(__('Add New Payment Button', 'coinqvest'))?></h2>

            <?php if (is_null($api_credentials)) { ?>
                <div class="notice notice-error"><?php echo esc_html(__('Please enter your API key and API secret first before you can create a payment button:', 'coinqvest'))?> <a href="/wp-admin/admin.php?page=coinqvest-settings"><?php echo esc_html(__('API Settings', 'coinqvest'))?></a></div>
            <?php } ?>

            <p>
	            <?php echo esc_html(__('Provide a name and a JSON object, according to the COINQVEST API documentation', 'coinqvest'))?>: <br><a href="https://www.coinqvest.com/en/api-docs#post-checkout-hosted" target="_blank">https://www.coinqvest.com/en/api-docs#post-checkout-hosted</a>
            </p>

            <div id="coinqvest_form_feedback"></div>

            <form action="<?=esc_url(admin_url('admin-post.php'));?>" method="POST" id="coinqvest_ajax_form">

                <input type="hidden" name="action" value="coinqvest_admin_form_response">
                <?php wp_nonce_field( 'addPaymentButton-dfs!%sd' ); ?>
                <input type="hidden" name="task" value="add_payment_button">

                <table class="form-table" role="presentation">

                    <tr>
                        <th scope="row"><?php echo esc_html(__('Button Name', 'coinqvest'))?></th>
                        <td><input name="cq_button_name" type="text" id="cq_button_name" value="" class="regular-text" /></td>
                    </tr>

                    <tr>
                        <th scope="row"><?php echo esc_html(__('JSON Object', 'coinqvest'))?></th>
                        <td>
                            <textarea name="cq_button_json" rows="5" style="width:500px"></textarea>

                            <p class="description"><?php echo esc_html(__('Example of a minimal JSON object:', 'coinqvest'))?></p>

                            <pre class="json"><?=Common_Helpers::pretty_json_example()?></pre>

                            <p class="description"><?php echo esc_html(__('To get started, just copy and paste this example and adjust parameters to your requirements.', 'coinqvest'))?></p>

                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><?php echo esc_html(__('Button Text', 'coinqvest'))?> <span class="optional">(<?php echo esc_html(__('optional', 'coinqvest'))?>)</span></th>
                        <td>
                            <input name="cq_button_text" type="text" id="cq_button_text" value="" placeholder="<?php echo esc_html(__('Buy Now', 'coinqvest'))?>" class="regular-text" />
                            <p class="description">
                                <?php echo esc_html(__('Customize the button text. Default is "Buy Now".', 'coinqvest'))?>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><?php echo esc_html(__('Button CSS Class', 'coinqvest'))?> <span class="optional">(<?php echo esc_html(__('optional', 'coinqvest'))?>)</span></th>
                        <td>
                            <input name="cq_button_css_class" type="text" id="cq_button_css_class" value="" placeholder="my-custom-button-class" class="regular-text" />
                            <p class="description"><?php echo esc_html(__('Customize the button style. Add a CSS class here.', 'coinqvest'))?></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><?php echo esc_html(__('Status', 'coinqvest'))?></th>
                        <td><input name="cq_button_status" type="checkbox" id="cq_button_status" checked /> <?php echo esc_html(__('active', 'coinqvest'))?></td>
                    </tr>

                </table>

                <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo esc_html(__('Save', 'coinqvest'))?>" /></p>

            </form>

        </div>

		<?php

	}

	public function submit_form_add_payment_button() {

        /**
         * Sanitize input parameters
         */

        $name = !empty($_POST['cq_button_name']) ? sanitize_text_field($_POST['cq_button_name']) : null;
        $css_class = !empty($_POST['cq_button_css_class']) ? $this->clean(sanitize_text_field($_POST['cq_button_css_class'])) : null;
        $button_text = !empty($_POST['cq_button_text']) ? sanitize_text_field($_POST['cq_button_text']) : null;
        $status = isset($_POST['cq_button_status']) ? 1 : 0;
        $json = !empty($_POST['cq_button_json']) ? sanitize_text_field($_POST['cq_button_json']) : null;
        $is_ajax = (isset( $_POST['ajaxrequest']) && $_POST['ajaxrequest'] === 'true') ? true : false;

		/**
		 * Input validation
		 */

		if (is_null($name) || is_null($json)) {
			$result = "error";
			$message = esc_html(__('Please provide button name and JSON object.', 'coinqvest'));
			$page = "coinqvest-add-payment-button";

            if ($is_ajax === true) {
                Common_Helpers::renderResponse(array(
                    "success" => false,
                    "message" => $message
                ));
			} else {
				$this->redirect = new Admin_Helpers();
				$this->redirect->custom_redirect($result, $message, $page);
			}
			exit;
		}

		if (strlen($name) > 56) {
			$result = "error";
			$message = esc_html(sprintf(__('Name is too long. Max. %s characters.', 'coinqvest'), 56));
			$page = "coinqvest-add-payment-button";

            if ($is_ajax === true) {
                Common_Helpers::renderResponse(array(
                    "success" => false,
                    "message" => $message
                ));
			} else {
				$this->redirect = new Admin_Helpers();
				$this->redirect->custom_redirect($result, $message, $page);
			}
			exit;
		}

		if (!is_null($button_text) && strlen($button_text) > 50) {
			$result = "error";
			$message = esc_html(sprintf(__('Button text is too long. Max. %s characters.', 'coinqvest'), 50));
			$page = "coinqvest-add-payment-button";

            if ($is_ajax === true) {
                Common_Helpers::renderResponse(array(
                    "success" => false,
                    "message" => $message
                ));
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
			$message = esc_html(__('API key and API secret don\'t exist.', 'coinqvest'));
			$page = "coinqvest-add-payment-button";

            if ($is_ajax === true) {
                Common_Helpers::renderResponse(array(
                    "success" => false,
                    "message" => $message
                ));
			} else {
				$this->redirect = new Admin_Helpers();
				$this->redirect->custom_redirect($result, $message, $page);
			}
			exit;
		}



		/**
		 * JSON object validation
		 */

		$client = new API\CQMerchantClient(
			$api_settings['api_key'],
			$api_settings['api_secret'],
            true
		);

        $json = str_replace("\\", "", $json);
        $json_array = json_decode($json, true);

		$response = $client->post('/checkout/validate-for-wordpress', $json_array);

		if ($response->httpStatusCode != 200) {

			$result = "error";
			$message = esc_html("Status Code: " . $response->httpStatusCode . " - " . $response->responseBody);
			$page = "coinqvest-add-payment-button";

			$log = new API\CQLoggingService();
			$log::write("[CQ Add Payment Button] " . $message);

            if ($is_ajax === true) {
                Common_Helpers::renderResponse(array(
                    "success" => false,
                    "message" => $message
                ));
			} else {
				$this->redirect = new Admin_Helpers();
				$this->redirect->custom_redirect($result, $message, $page);
			}
			exit;

		}

        $response = json_decode($response->responseBody);

        $currency = sanitize_text_field($json_array['charge']['currency']);

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
				'total' => $response->total,
				'decimals' => $response->decimals,
				'currency' => $currency,
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


		$message = esc_html(__('Payment button created successfully.', 'coinqvest'));

        if ($is_ajax === true) {
            Common_Helpers::renderResponse(array(
                "success" => true,
                "message" => $message,
                "redirect" => "/wp-admin/admin.php?page=coinqvest-payment-buttons"
            ));
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