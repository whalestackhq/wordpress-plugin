<?php
namespace COINQVEST\Inc\Admin;
use COINQVEST\Inc\Libraries\API;
use COINQVEST\Inc\Common\Common_Helpers;

class Edit_Payment_Button {

	private $redirect;

	public function __construct() {

	}

	public function render_edit_payment_button_page(){

		$id = absint($_GET['id']);

		global $wpdb;
		$table_name = $wpdb->prefix . 'coinqvest_payment_buttons';
		$row = $wpdb->get_row("SELECT name, hashid, status, cssclass, buttontext, json FROM ".$table_name." WHERE hashid = " . $id);

		if (!$row) {
			echo '<p class="coinqvest_payments_error_msg" style="color: #dc3232;">' . esc_html(__('Requested button id doesn\'t exist.', 'coinqvest' )) . '</p>';
		    exit;
		}

        $button_text = !is_null($row->buttontext) ? $row->buttontext : null;

		?>

        <div class="wrap">

            <h2><?php echo esc_html(__('Edit Payment Button', 'coinqvest'))?></h2>

            <p>
	            <?php echo esc_html(__('Build the JSON object according to the COINQVEST API documentation', 'coinqvest'))?>: <br><a href="https://www.coinqvest.com/en/api-docs#post-checkout-hosted" target="_blank">https://www.coinqvest.com/en/api-docs#post-checkout-hosted</a>
            </p>

            <div id="coinqvest_form_feedback"></div>

            <form action="<?=esc_url(admin_url('admin-post.php'));?>" method="POST" id="coinqvest_ajax_form">

                <input type="hidden" name="action" value="coinqvest_admin_form_response">
                <?php wp_nonce_field( 'editPaymentButton-dfs!%sd' ); ?>
                <input type="hidden" name="task" value="edit_payment_button">
                <input type="hidden" name="checkout_id" value="<?=esc_attr($row->hashid)?>">

                <table class="form-table" role="presentation">

                    <tr>
                        <th scope="row"><?php echo esc_html(__('Button Name', 'coinqvest'))?></th>
                        <td><input name="cq_button_name" type="text" id="cq_button_name" value="<?=esc_attr($row->name)?>" class="regular-text" /></td>
                    </tr>

                    <tr>
                        <th scope="row"><?php echo esc_html(__('JSON Object', 'coinqvest'))?></th>
                        <td>
                            <textarea name="cq_button_json" rows="15" style="width:500px"><?=json_encode(json_decode($row->json), JSON_PRETTY_PRINT)?></textarea>

                            <p class="description"><?php echo esc_html(__('Example of a minimal JSON object:', 'coinqvest'))?></p>

                            <pre class="json"><?=Common_Helpers::pretty_json_example()?></pre>

                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><?php echo esc_html(__('Button Text', 'coinqvest'))?> <span class="optional">(<?php echo esc_html(__('optional', 'coinqvest'))?>)</span></th>
                        <td>
                            <input name="cq_button_text" type="text" id="cq_button_text" value="<?=esc_attr($button_text)?>" placeholder="<?php echo esc_html(__('Buy Now', 'coinqvest'))?>" class="regular-text" />
                            <p class="description"><?php echo esc_html(__('Customize the button text. Default is "Buy Now".', 'coinqvest'))?></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><?php echo esc_html(__('Button CSS Class', 'coinqvest'))?> <span class="optional">(<?php echo esc_html(__('optional', 'coinqvest'))?>)</span></th>
                        <td>
                            <input name="cq_button_css_class" type="text" id="cq_button_css_class" value="<?=esc_attr($row->cssclass)?>" placeholder="my-custom-button-class" class="regular-text" />
                            <p class="description"><?php echo esc_html(__('Customize the button style. Add a CSS class here.', 'coinqvest'))?></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><?php echo esc_html(__('Status', 'coinqvest'))?></th>
                        <td><input name="cq_button_status" type="checkbox" id="cq_button_status" <?=$row->status == 1 ? "checked" : null ?>  /> <?php echo esc_html(__('active', 'coinqvest'))?></td>
                    </tr>

                </table>

                <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo esc_html(__('Save', 'coinqvest'))?>"  /></p>

            </form>

        </div>

		<?php

	}

	public function submit_form_edit_payment_button() {

        /**
         * Sanitize input parameters
         */

        $id = absint($_POST['checkout_id']);
        $name = !empty($_POST['cq_button_name']) ? sanitize_text_field($_POST['cq_button_name']) : null;
        $css_class = !empty($_POST['cq_button_css_class']) ? $this->clean(sanitize_text_field($_POST['cq_button_css_class'])) : null;
        $status = isset($_POST['cq_button_status']) ? 1 : 0;
        $json = !empty($_POST['cq_button_json']) ? sanitize_text_field($_POST['cq_button_json']) : null;
        $is_ajax = (isset( $_POST['ajaxrequest']) && $_POST['ajaxrequest'] === 'true') ? true : false;

        if (!empty($_POST['cq_button_text'])) {
            if ($_POST['cq_button_text'] == __('Buy Now', 'coinqvest')) {
                $button_text = null;
            } else {
                $button_text = sanitize_text_field($_POST['cq_button_text']);
            }
        } else {
            $button_text = null;
        }

		/**
		 * Input validation
		 */

		global $wpdb;
		$table_name = $wpdb->prefix . 'coinqvest_payment_buttons';
		$row = $wpdb->get_row("SELECT name, hashid, status, cssclass, buttontext, json FROM ".$table_name." WHERE hashid = " . $id);

		if (!$row) {
			$result = "error";
			$message = esc_html(sprintf(__('Payment button id %s does not exist.', 'coinqvest'), $id));
			$page = "coinqvest-payment-buttons";

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

		if (is_null($name) || is_null($json)) {
			$result = "error";
            $message = esc_html(__('Please provide button name and JSON object.', 'coinqvest'));
			$page = "coinqvest-create-payment-button";

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
			$page = "coinqvest-create-payment-button";

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
			$page = "coinqvest-create-payment-button";

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
			$message = esc_html(__('API key and API secret do not exist.', 'coinqvest'));
			$page = "coinqvest-create-payment-button";

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

		$client = new Api\CQ_Merchant_Client(
			$api_settings['api_key'],
			$api_settings['api_secret'],
            true
		);

        $json = stripslashes($json);
        $json_array = json_decode($json, true);

		$response = $client->post('/checkout/validate-for-wordpress', $json_array);

		if ($response->httpStatusCode != 200) {

			$result = "error";
			$message = esc_html("Status Code: " . $response->httpStatusCode . " - " . $response->responseBody);
			$page = "coinqvest-create-payment-button";

			$log = new Api\CQ_Logging_Service();
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

		$wpdb->update(
			$table_name,
			array(
				'status' => $status,
				'name' => $name,
                'total' => $response->total,
                'decimals' => $response->decimals,
                'currency' => $currency,
				'json' => $json,
                'cssclass' => $css_class,
                'buttontext' => $button_text
			),
			array('hashid' => $row->hashid)
		);


		$message = esc_html(__('Payment button edited successfully.', 'coinqvest'));

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