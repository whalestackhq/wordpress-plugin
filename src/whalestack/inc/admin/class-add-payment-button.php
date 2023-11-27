<?php
namespace Whalestack\Inc\Admin;
use Whalestack\Inc\Common\Common_Helpers;
use Whalestack\Inc\Libraries\API;

class Add_Payment_Button {

	public function __construct() {

	}

	public function render_add_payment_button_page(){

	    $helpers = new Common_Helpers();
	    $api_credentials = $helpers->get_whalestack_credentials();

		?>

        <div class="wrap">

            <h2><?php echo esc_html(__('Add New Payment Button', 'whalestack'))?></h2>

            <?php if (is_null($api_credentials)) { ?>
                <div class="notice notice-error"><?php echo esc_html(__('Please enter your API key and API secret first before you can create a payment button:', 'whalestack'))?> <a href="/wp-admin/admin.php?page=whalestack-settings"><?php echo esc_html(__('API Settings', 'whalestack'))?></a></div>
            <?php } ?>

            <p>
	            <?php echo esc_html(__('Provide a name and a JSON object, according to the Whalestack API documentation', 'whalestack'))?>: <br><a href="https://www.whalestack.com/en/api-docs#post-checkout-hosted" target="_blank">https://www.whalestack.com/en/api-docs#post-checkout-hosted</a>
            </p>

            <div id="whalestack_form_feedback"></div>

            <form action="<?=esc_url(admin_url('admin-post.php'));?>" method="POST" id="whalestack_ajax_form">

                <input type="hidden" name="action" value="whalestack_admin_form_response">
                <?php wp_nonce_field( 'addPaymentButton-dfs!%sd' ); ?>
                <input type="hidden" name="task" value="add_payment_button">

                <table class="form-table" role="presentation">

                    <tr>
                        <th scope="row"><?php echo esc_html(__('Button Name', 'whalestack'))?></th>
                        <td><input name="whalestack_button_name" type="text" id="whalestack_button_name" value="" class="regular-text" /></td>
                    </tr>

                    <tr>
                        <th scope="row"><?php echo esc_html(__('JSON Object', 'whalestack'))?></th>
                        <td>
                            <textarea name="whalestack_button_json" rows="5" style="width:500px"></textarea>

                            <p class="description"><?php echo esc_html(__('Example of a minimal JSON object:', 'whalestack'))?></p>

                            <pre class="json"><?=Common_Helpers::pretty_json_example()?></pre>

                            <p class="description"><?php echo esc_html(__('To get started, just copy and paste this example and adjust parameters to your requirements.', 'whalestack'))?></p>

                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><?php echo esc_html(__('Button Text', 'whalestack'))?> <span class="optional">(<?php echo esc_html(__('optional', 'whalestack'))?>)</span></th>
                        <td>
                            <input name="whalestack_button_text" type="text" id="whalestack_button_text" value="" placeholder="<?php echo esc_html(__('Buy Now', 'whalestack'))?>" class="regular-text" />
                            <p class="description">
                                <?php echo esc_html(__('Customize the button text. Default is "Buy Now".', 'whalestack'))?>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><?php echo esc_html(__('Button CSS Class', 'whalestack'))?> <span class="optional">(<?php echo esc_html(__('optional', 'whalestack'))?>)</span></th>
                        <td>
                            <input name="whalestack_button_css_class" type="text" id="whalestack_button_css_class" value="" placeholder="my-custom-button-class" class="regular-text" />
                            <p class="description"><?php echo esc_html(__('Customize the button style. Add a CSS class here.', 'whalestack'))?></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><?php echo esc_html(__('Status', 'whalestack'))?></th>
                        <td><input name="whalestack_button_status" type="checkbox" id="whalestack_button_status" checked /> <?php echo esc_html(__('active', 'whalestack'))?></td>
                    </tr>

                </table>

                <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo esc_html(__('Save', 'whalestack'))?>" /></p>

            </form>

        </div>

		<?php

	}

	public function submit_form_add_payment_button() {

        $page = "whalestack-add-payment-button";

        /**
         * Sanitize input parameters
         */

        $name = !empty($_POST['whalestack_button_name']) ? sanitize_text_field($_POST['whalestack_button_name']) : null;
        $css_class = !empty($_POST['whalestack_button_css_class']) ? Common_Helpers::clean(sanitize_text_field($_POST['whalestack_button_css_class'])) : null;
        $button_text = !empty($_POST['whalestack_button_text']) ? sanitize_text_field($_POST['whalestack_button_text']) : null;
        $status = isset($_POST['whalestack_button_status']) ? 1 : 0;
        $json = !empty($_POST['whalestack_button_json']) ? sanitize_textarea_field($_POST['whalestack_button_json']) : null;
        $is_ajax = (isset( $_POST['ajaxrequest']) && $_POST['ajaxrequest'] === 'true') ? true : false;

		/**
		 * Input validation
		 */

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
			$message = esc_html(__('API key and API secret don\'t exist.', 'whalestack'));
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

		$message = esc_html(__('Payment button created successfully.', 'whalestack'));
		Admin_Helpers::renderAdminSuccessMessage($message, $page, $is_ajax, true);

	}

}