<?php
namespace COINQVEST\Inc\Admin;
use COINQVEST\Inc\Common\Common_Helpers;
use COINQVEST\Inc\Libraries\API;

class Add_Payment_Button {

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
	            <?php echo esc_html(__('Provide a name and a JSON object, according to the COINQVEST API documentation', 'coinqvest'))?>: <br><a href="https://www.coinqvest.com/en/api-docs#post-checkout-hosted?utm_source=wordpress&utm_medium=<?php echo esc_html($_SERVER['SERVER_NAME'])?>" target="_blank">https://www.coinqvest.com/en/api-docs#post-checkout-hosted</a>
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

        $page = "coinqvest-add-payment-button";

        /**
         * Sanitize input parameters
         */

        $name = !empty($_POST['cq_button_name']) ? sanitize_text_field($_POST['cq_button_name']) : null;
        $css_class = !empty($_POST['cq_button_css_class']) ? Common_Helpers::clean(sanitize_text_field($_POST['cq_button_css_class'])) : null;
        $button_text = !empty($_POST['cq_button_text']) ? sanitize_text_field($_POST['cq_button_text']) : null;
        $status = isset($_POST['cq_button_status']) ? 1 : 0;
        $json = !empty($_POST['cq_button_json']) ? sanitize_textarea_field($_POST['cq_button_json']) : null;
        $is_ajax = (isset( $_POST['ajaxrequest']) && $_POST['ajaxrequest'] === 'true') ? true : false;

		/**
		 * Input validation
		 */

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
			$message = esc_html(__('API key and API secret don\'t exist.', 'coinqvest'));
            Admin_Helpers::renderAdminErrorMessage($message, $page, $is_ajax);
		}

		/**
		 * Init COINQVEST API
		 */

		$client = new Api\CQ_Merchant_Client($api_settings['api_key'], $api_settings['api_secret'], true);

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
		$table_name = $wpdb->prefix . 'coinqvest_payment_buttons';

		$wpdb->insert(
			$table_name,
			array(
				'time' => current_time('mysql'),
				'status' => $status,
				'name' => $name,
				'total' => $total,
				'decimals' => $response->decimals,
				'currency' => $billingCurrency,
				'json' => $json,
                'cssclass' => $css_class,
                'buttontext' => $button_text
			)
		);

		$last_id = $wpdb->insert_id;
		$hash_id = rand(2533, 9563) . $last_id;

		$wpdb->update(
			$table_name,
			array('hashid' => $hash_id),
			array('id' => $last_id)
		);

		$message = esc_html(__('Payment button created successfully.', 'coinqvest'));
		Admin_Helpers::renderAdminSuccessMessage($message, $page, $is_ajax, true);

	}

}