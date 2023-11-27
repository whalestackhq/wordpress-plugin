<?php
namespace Whalestack\Inc\Admin;
use Whalestack\Inc\Libraries\API;
use Whalestack\Inc\Common\Common_Helpers;
use Whalestack\Inc\Libraries\Api\Whalestack_Logging_Service;

class Edit_Payment_Button {

	public function __construct() {
        ini_set('serialize_precision', 7);
	}

	public function render_edit_payment_button_page(){

		$id = absint($_GET['id']);

		global $wpdb;
		$table_name = $wpdb->prefix . 'whalestack_payment_buttons';
		$row = $wpdb->get_row("SELECT name, hashid, status, cssclass, buttontext, json FROM " . $table_name . " WHERE hashid = " . $id);

		if (!$row) {
			echo '<p class="whalestack_payments_error_msg" style="color: #dc3232;">' . esc_html(__('Requested button id doesn\'t exist.', 'whalestack' )) . '</p>';
		    exit;
		}

        $button_text = !is_null($row->buttontext) ? $row->buttontext : null;

		?>

        <div class="wrap">

            <h2><?php echo esc_html(__('Edit Payment Button', 'whalestack'))?></h2>

            <p>
	            <?php echo esc_html(__('Build the JSON object according to the Whalestack API documentation', 'whalestack'))?>: <br><a href="https://www.whalestack.com/en/api-docs#post-checkout-hosted" target="_blank">https://www.whalestack.com/en/api-docs#post-checkout-hosted</a>
            </p>

            <div id="whalestack_form_feedback"></div>

            <form action="<?=esc_url(admin_url('admin-post.php'));?>" method="POST" id="whalestack_ajax_form">

                <input type="hidden" name="action" value="whalestack_admin_form_response">
                <?php wp_nonce_field( 'editPaymentButton-dfs!%sd' ); ?>
                <input type="hidden" name="task" value="edit_payment_button">
                <input type="hidden" name="checkout_id" value="<?=esc_attr($row->hashid)?>">

                <table class="form-table" role="presentation">

                    <tr>
                        <th scope="row"><?php echo esc_html(__('Button Name', 'whalestack'))?></th>
                        <td><input name="whalestack_button_name" type="text" id="whalestack_button_name" value="<?=esc_attr($row->name)?>" class="regular-text" /></td>
                    </tr>

                    <tr>
                        <th scope="row"><?php echo esc_html(__('JSON Object', 'whalestack'))?></th>
                        <td>
                            <textarea name="whalestack_button_json" rows="15" style="width:500px"><?=json_encode(json_decode($row->json), JSON_PRETTY_PRINT)?></textarea>

                            <p class="description"><?php echo esc_html(__('Example of a minimal JSON object:', 'whalestack'))?></p>

                            <pre class="json"><?=Common_Helpers::pretty_json_example()?></pre>

                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><?php echo esc_html(__('Button Text', 'whalestack'))?> <span class="optional">(<?php echo esc_html(__('optional', 'whalestack'))?>)</span></th>
                        <td>
                            <input name="whalestack_button_text" type="text" id="whalestack_button_text" value="<?=esc_attr($button_text)?>" placeholder="<?php echo esc_html(__('Buy Now', 'whalestack'))?>" class="regular-text" />
                            <p class="description"><?php echo esc_html(__('Customize the button text. Default is "Buy Now".', 'whalestack'))?></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><?php echo esc_html(__('Button CSS Class', 'whalestack'))?> <span class="optional">(<?php echo esc_html(__('optional', 'whalestack'))?>)</span></th>
                        <td>
                            <input name="whalestack_button_css_class" type="text" id="whalestack_button_css_class" value="<?=esc_attr($row->cssclass)?>" placeholder="my-custom-button-class" class="regular-text" />
                            <p class="description"><?php echo esc_html(__('Customize the button style. Add a CSS class here.', 'whalestack'))?></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><?php echo esc_html(__('Status', 'whalestack'))?></th>
                        <td><input name="whalestack_button_status" type="checkbox" id="whalestack_button_status" <?=$row->status == 1 ? "checked" : null ?>  /> <?php echo esc_html(__('active', 'whalestack'))?></td>
                    </tr>

                </table>

                <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo esc_html(__('Save', 'whalestack'))?>"  /></p>

            </form>

        </div>

		<?php

	}

	public function submit_form_edit_payment_button() {

        $page = "whalestack-edit-payment-button";

        /**
         * Sanitize input parameters
         */

        $id = absint($_POST['checkout_id']);
        $name = !empty($_POST['whalestack_button_name']) ? sanitize_text_field($_POST['whalestack_button_name']) : null;
        $css_class = !empty($_POST['whalestack_button_css_class']) ? Common_Helpers::clean(sanitize_text_field($_POST['whalestack_button_css_class'])) : null;
        $status = isset($_POST['whalestack_button_status']) ? 1 : 0;
        $json = !empty($_POST['whalestack_button_json']) ? sanitize_text_field($_POST['whalestack_button_json']) : null;
        $is_ajax = (isset($_POST['ajaxrequest']) && $_POST['ajaxrequest'] === 'true') ? true : false;

        if (!empty($_POST['whalestack_button_text'])) {
            if ($_POST['whalestack_button_text'] == __('Buy Now', 'whalestack')) {
                $button_text = null;
            } else {
                $button_text = sanitize_text_field($_POST['whalestack_button_text']);
            }
        } else {
            $button_text = null;
        }

		/**
		 * Input validation
		 */

		global $wpdb;
		$table_name = $wpdb->prefix . 'whalestack_payment_buttons';
		$row = $wpdb->get_row("SELECT name, hashid, status, cssclass, buttontext, json FROM " . $table_name . " WHERE hashid = " . $id);

		if (!$row) {
			$message = esc_html(sprintf(__('Payment button id %s does not exist.', 'whalestack'), $id));
			$page = "whalestack-payment-buttons";
            Admin_Helpers::renderAdminErrorMessage($message, $page, $is_ajax);
		}

		if (is_null($name) || is_null($json)) {
            $message = esc_html(__('Please provide button name and JSON object.', 'whalestack'));
            Admin_Helpers::renderAdminErrorMessage($message, $page, $is_ajax);
		}

		if (strlen($name) > 56) {
			$message = esc_html(sprintf(__('Name is too long. Max. %s characters.', 'whalestack'), 56));
            Admin_Helpers::renderAdminErrorMessage($message, $page, $is_ajax);
		}

        if (!is_null($button_text) && strlen($button_text) > 50) {
			$message = esc_html(sprintf(__('Button text is too long. Max. %s characters.', 'whalestack'), 50));
            Admin_Helpers::renderAdminErrorMessage($message, $page, $is_ajax);
		}

		/**
		 * Load API key and secret
		 */

		$api_settings = get_option('whalestack_settings');
		$api_settings = unserialize($api_settings);

		if (empty($api_settings['api_key']) || empty($api_settings['api_secret'])) {
			$message = esc_html(__('API key and API secret do not exist.', 'whalestack'));
            Admin_Helpers::renderAdminErrorMessage($message, $page, $is_ajax);
		}

        /**
         * Init Whalestack API
         */

		$client = new Api\Whalestack_Merchant_Client($api_settings['api_key'], $api_settings['api_secret'], true);

        $json = stripslashes($json);
        $json_array = json_decode($json, true);

        /**
         * Validate the charge
         */

        $response = $client->post('/checkout/validate-checkout-charge', $json_array);
		if ($response->httpStatusCode != 200) {
			$message = esc_html("Status Code: " . $response->httpStatusCode . " - " . $response->responseBody);
            Admin_Helpers::renderAdminErrorMessage($message, $page, $is_ajax);
		}

        $response = json_decode($response->responseBody);

		/**
		 * Save to database
		 */

        $total = Common_Helpers::numberFormat($response->total, $response->decimals);
        $billingCurrency = isset($json_array['charge']['currency']) ? sanitize_text_field($json_array['charge']['currency']) : sanitize_text_field($json_array['charge']['billingCurrency']);

        global $wpdb;
		$table_name = $wpdb->prefix . 'whalestack_payment_buttons';

		$wpdb->update(
			$table_name,
			array(
				'status' => $status,
				'name' => $name,
                'total' => $total,
                'decimals' => $response->decimals,
                'currency' => $billingCurrency,
				'json' => $json,
                'cssclass' => $css_class,
                'buttontext' => $button_text
			),
			array('hashid' => $row->hashid)
		);

		$message = esc_html(__('Payment button edited successfully.', 'whalestack'));
        Admin_Helpers::renderAdminSuccessMessage($message, $page, $is_ajax, true);

	}
}