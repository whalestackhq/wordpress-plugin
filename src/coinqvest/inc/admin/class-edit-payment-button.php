<?php
namespace COINQVEST\Inc\Admin;
use COINQVEST\Inc\Libraries\API;
use COINQVEST\Inc\Common\Common_Helpers;
use COINQVEST\Inc\Libraries\Api\CQ_Logging_Service;

class Edit_Payment_Button {

	public function __construct() {
        ini_set('serialize_precision', 7);
	}

	public function render_edit_payment_button_page(){

		$id = absint($_GET['id']);

		global $wpdb;
		$table_name = $wpdb->prefix . 'coinqvest_payment_buttons';
		$row = $wpdb->get_row("SELECT name, hashid, status, cssclass, buttontext, json FROM " . $table_name . " WHERE hashid = " . $id);

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

        $page = "coinqvest-edit-payment-button";

        /**
         * Sanitize input parameters
         */

        $id = absint($_POST['checkout_id']);
        $name = !empty($_POST['cq_button_name']) ? sanitize_text_field($_POST['cq_button_name']) : null;
        $css_class = !empty($_POST['cq_button_css_class']) ? Common_Helpers::clean(sanitize_text_field($_POST['cq_button_css_class'])) : null;
        $status = isset($_POST['cq_button_status']) ? 1 : 0;
        $json = !empty($_POST['cq_button_json']) ? sanitize_text_field($_POST['cq_button_json']) : null;
        $is_ajax = (isset($_POST['ajaxrequest']) && $_POST['ajaxrequest'] === 'true') ? true : false;

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
		$row = $wpdb->get_row("SELECT name, hashid, status, cssclass, buttontext, json FROM " . $table_name . " WHERE hashid = " . $id);

		if (!$row) {
			$message = esc_html(sprintf(__('Payment button id %s does not exist.', 'coinqvest'), $id));
			$page = "coinqvest-payment-buttons";
            Admin_Helpers::renderAdminErrorMessage($message, $page, $is_ajax);
		}

		if (is_null($name) || is_null($json)) {
            $message = esc_html(__('Please provide button name and JSON object.', 'coinqvest'));
            Admin_Helpers::renderAdminErrorMessage($message, $page, $is_ajax);
		}

		if (strlen($name) > 56) {
			$message = esc_html(sprintf(__('Name is too long. Max. %s characters.', 'coinqvest'), 56));
            Admin_Helpers::renderAdminErrorMessage($message, $page, $is_ajax);
		}

        if (!is_null($button_text) && strlen($button_text) > 50) {
			$message = esc_html(sprintf(__('Button text is too long. Max. %s characters.', 'coinqvest'), 50));
            Admin_Helpers::renderAdminErrorMessage($message, $page, $is_ajax);
		}

		/**
		 * Load API key and secret
		 */

		$api_settings = get_option('coinqvest_settings');
		$api_settings = unserialize($api_settings);

		if (empty($api_settings['api_key']) || empty($api_settings['api_secret'])) {
			$message = esc_html(__('API key and API secret do not exist.', 'coinqvest'));
            Admin_Helpers::renderAdminErrorMessage($message, $page, $is_ajax);
		}

        /**
         * Init COINQVEST API
         */

		$client = new Api\CQ_Merchant_Client($api_settings['api_key'], $api_settings['api_secret'], true);

        $json = stripslashes($json);
        $json_array = json_decode($json, true);
        $baseCurrency = sanitize_text_field($json_array['charge']['currency']);
        $exchangeRate = null;

        /**
         * Check if billing currency is a supported fiat or blockchain currency
         * If not, require a settlement currency
         * The settlement currency will then be used as the new billing currency
         */

        $isFiat = Common_Helpers::isFiat($client, $baseCurrency);
        $isBlockchain = Common_Helpers::isBlockchain($client, $baseCurrency);

        if (!$isFiat && !$isBlockchain) {

            if (!isset($json_array['settlementCurrency'])) {
                $message = esc_html("Please define a parameter \"settlementCurrency\". See example ") . "<a href='https://www.coinqvest.com/en/api-docs#post-checkout-hosted' target='_blank'>here</a>.";
                Admin_Helpers::renderAdminErrorMessage($message, $page, $is_ajax);
            }

            /**
             * Get the exchange rate between billing and settlement currency
             */

            $pair = array(
                'baseCurrency' => $baseCurrency,
                'quoteCurrency' => $json_array['settlementCurrency']
            );

            $response = $client->get('/exchange-rate-global', $pair);

            if ($response->httpStatusCode != 200) {
                $message = esc_html("Status Code: " . $response->httpStatusCode . " - " . $response->responseBody);
                Admin_Helpers::renderAdminErrorMessage($message, $page, $is_ajax);
            }

            $response = json_decode($response->responseBody);
            $exchangeRate = $response->exchangeRate;

            if ($exchangeRate == null || $exchangeRate == 0) {
                $message = esc_html(sprintf(__('Could not convert %1s to %2s. Please choose a different settlement currency.', 'coinqvest'), $baseCurrency, $json_array['settlementCurrency']));
                Admin_Helpers::renderAdminErrorMessage($message, $page, $is_ajax);
            }

            /**
             * Override the charge object with new currency values
             */

            $json_array = Common_Helpers::overrideCheckoutValues($json_array, $exchangeRate);

        }

        /**
         * Validate the charge
         */

        $response = $client->post('/checkout/validate-for-wordpress', $json_array);

		if ($response->httpStatusCode != 200) {
			$message = esc_html("Status Code: " . $response->httpStatusCode . " - " . $response->responseBody);
            Admin_Helpers::renderAdminErrorMessage($message, $page, $is_ajax);
		}

        $response = json_decode($response->responseBody);

		/**
		 * Save to database
		 */

        $total = is_null($exchangeRate) ? Common_Helpers::numberFormat($response->total, $response->decimals) : Common_Helpers::numberFormat($response->total / $exchangeRate, $response->decimals);

        global $wpdb;
		$table_name = $wpdb->prefix . 'coinqvest_payment_buttons';

		$wpdb->update(
			$table_name,
			array(
				'status' => $status,
				'name' => $name,
                'total' => $total,
                'decimals' => $response->decimals,
                'currency' => $baseCurrency,
				'json' => $json,
                'cssclass' => $css_class,
                'buttontext' => $button_text
			),
			array('hashid' => $row->hashid)
		);


		$message = esc_html(__('Payment button edited successfully.', 'coinqvest'));
        Admin_Helpers::renderAdminSuccessMessage($message, $page, $is_ajax);

	}
}