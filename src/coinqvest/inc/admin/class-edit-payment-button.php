<?php
namespace COINQVEST\Inc\Admin;
use COINQVEST\Inc\Libraries\API;
use COINQVEST\Inc\Common\Common_Helpers;

class Edit_Payment_Button {

	private $redirect;

	public function __construct() {

	}

	public function render_edit_payment_button_page(){

		$id = $_GET['id'];

		global $wpdb;
		$table_name = $wpdb->prefix . 'coinqvest_payment_buttons';
		$row = $wpdb->get_row("SELECT name, hashid, status, cssclass, buttontext, json FROM ".$table_name." WHERE hashid = " . $id);

		if (!$row) {
			echo '<p class="coinqvest_payments_error_msg" style="color: #dc3232;">' . __('Requested button id doesn\'t exist.', 'coinqvest' ) . '</p>';
		    exit;
		}

        $button_text = !is_null($row->buttontext) ? $row->buttontext : null;

		?>

        <div class="wrap">

            <h2><?php _e('Edit Payment Button', 'coinqvest')?></h2>

            <p>
	            <?php _e('Build the JSON object according to the COINQVEST API documentation', 'coinqvest')?>: <br><a href="https://www.coinqvest.com/en/api-docs#post-checkout-hosted" target="_blank">https://www.coinqvest.com/en/api-docs#post-checkout-hosted</a>
            </p>

            <div id="coinqvest_form_feedback"></div>

            <form action="<?=esc_url(admin_url('admin-post.php'));?>" method="POST" id="coinqvest_ajax_form">

                <input type="hidden" name="action" value="coinqvest_admin_form_response">
                <?php wp_nonce_field( 'editPaymentButton-dfs!%sd' ); ?>
                <input type="hidden" name="task" value="edit_payment_button">
                <input type="hidden" name="checkout_id" value="<?=$row->hashid?>">

                <table class="form-table" role="presentation">

                    <tr>
                        <th scope="row"><?php _e('Button Name', 'coinqvest')?></th>
                        <td><input name="cq_button_name" type="text" id="cq_button_name" value="<?=$row->name?>" class="regular-text" /></td>
                    </tr>

                    <tr>
                        <th scope="row"><?php _e('JSON Object', 'coinqvest')?></th>
                        <td>
                            <textarea name="cq_button_json" rows="15" style="width:500px"><?=json_encode(json_decode($row->json), JSON_PRETTY_PRINT)?></textarea>

                            <p class="description"><?php _e('Example of a minimal JSON object:', 'coinqvest')?></p>

                            <pre class="json"><?=Common_Helpers::pretty_json_example()?></pre>

                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><?php _e('Button Text', 'coinqvest')?> <span class="optional">(<?php _e('optional', 'coinqvest')?>)</span></th>
                        <td>
                            <input name="cq_button_text" type="text" id="cq_button_text" value="<?=$button_text?>" placeholder="<?php _e('Buy Now', 'coinqvest')?>" class="regular-text" />
                            <p class="description"><?php _e('Customize the button text. Default is "Buy Now".', 'coinqvest')?></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><?php _e('Button CSS Class', 'coinqvest')?> <span class="optional">(<?php _e('optional', 'coinqvest')?>)</span></th>
                        <td>
                            <input name="cq_button_css_class" type="text" id="cq_button_css_class" value="<?=$row->cssclass?>" placeholder="my-custom-button-class" class="regular-text" />
                            <p class="description"><?php _e('Customize the button style. Add a CSS class here.', 'coinqvest')?></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><?php _e('Status', 'coinqvest')?></th>
                        <td><input name="cq_button_status" type="checkbox" id="cq_button_status" <?=$row->status == 1 ? "checked" : null ?>  /> <?php _e('active', 'coinqvest')?></td>
                    </tr>

                </table>

                <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save', 'coinqvest')?>"  /></p>

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
            if ($_POST['cq_button_text'] == esc_attr(__('Buy Now', 'coinqvest'))) {
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
			$message = esc_attr(sprintf(__('Payment button id %s does not exist.', 'coinqvest'), absint($id)));
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
            $message = esc_attr(__('Please provide button name and JSON object.', 'coinqvest'));
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
			$message = esc_attr(sprintf(__('Name is too long. Max. %s characters.', 'coinqvest'), 56));
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
			$message = esc_attr(sprintf(__('Button text is too long. Max. %s characters.', 'coinqvest'), 50));
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
			$message = esc_attr(__('API key and API secret do not exist.', 'coinqvest'));
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

		$client = new API\CQMerchantClient(
			$api_settings['api_key'],
			$api_settings['api_secret'],
            true
		);

        $json = str_replace("\\", "", $json);

		$response = $client->post('/checkout/validate', json_decode($json, true));

		if ($response->httpStatusCode != 200) {

			$result = "error";
			$message = esc_attr("Status Code: " . $response->httpStatusCode . " - " . $response->responseBody);
			$page = "coinqvest-create-payment-button";

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
				'json' => $json,
                'cssclass' => $css_class,
                'buttontext' => $button_text
			),
			array('hashid' => $row->hashid)
		);


		$message = esc_attr(__('Payment button edited successfully.', 'coinqvest'));

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