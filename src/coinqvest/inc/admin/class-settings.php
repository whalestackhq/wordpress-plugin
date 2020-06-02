<?php

namespace COINQVEST\Inc\Admin;
use COINQVEST\Inc\Libraries\API;
use COINQVEST\Inc\Common\Common_Helpers;

class Settings {

	private $redirect;

	public function __construct(  ) {

	}

	public function render_settings_page() {

        $settings = get_option('coinqvest_settings');
        $settings = unserialize($settings);

        $api_key = isset($settings['api_key']) ? $settings['api_key'] : null;
        $api_secret = isset($settings['api_secret']) ? $settings['api_secret'] : null;
        $webhook_url = isset($settings['webhook_url']) ? $settings['webhook_url'] : null;
		$return_url = isset($settings['return_url']) ? $settings['return_url'] : null;
		$cancel_url = isset($settings['cancel_url']) ? $settings['cancel_url'] : null;
		$settlement_currency = isset($settings['settlement_currency']) ? $settings['settlement_currency'] : null;
		$customer_info = isset($settings['customer_info']) ? $settings['customer_info'] : null;

		$fiat_currencies = array();
		if (!empty($api_key) && !empty($api_secret)) {

			$client = new Api\CQ_Merchant_Client(
				$api_key,
				$api_secret,
				true
			);

			$response = $client->get('/fiat-currencies');
			$fiat_currencies = json_decode($response->responseBody);

			if ($response->httpStatusCode != 200) {

				$result = "error";
				$message = esc_html("Status Code: " . $response->httpStatusCode . " - " . $response->responseBody);
				$page = "coinqvest-settings";

				if (isset($_POST['ajaxrequest']) && $_POST['ajaxrequest'] === 'true') {
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

        }

		?>

        <div class="wrap">

            <h1><?php echo esc_html(__( 'COINQVEST API and Global Settings', 'coinqvest' ))?></h1>

            <h3><?php echo esc_html(__( 'API Settings', 'coinqvest' ))?></h3>

            <p><a href="https://www.coinqvest.com/en/api-settings" target="_blank"><?php echo esc_html(sprintf(__('Get your API Keys on www.coinqvest.com', 'coinqvest')))?></a></p>

            <div id="coinqvest_form_feedback"></div>

            <form action="<?=esc_url(admin_url('admin-post.php'));?>" method="POST" id="coinqvest_ajax_form">

                <input type="hidden" name="action" value="coinqvest_admin_form_response">
                <?php wp_nonce_field( 'submitApiSettings-23iyj@h!' ); ?>
                <input type="hidden" name="task" value="submit_api_settings">

                <table class="form-table" role="presentation">

                    <tr>
                        <th scope="row"><?php echo esc_html(__('API Key', 'coinqvest'))?></th>
                        <td><input name="cq_api_key" type="text" id="cq_api_key" value="<?=esc_attr($api_key)?>" class="regular-text" /></td>
                    </tr>

                    <tr>
                        <th scope="row"><?php echo esc_html(__('API Secret', 'coinqvest'))?></th>
                        <td><input name="cq_api_secret" type="text" id="cq_api_secret" value="<?=esc_attr($api_secret)?>" class="regular-text" /></td>
                    </tr>

                </table>

                <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo esc_html(__('Save', 'coinqvest'))?>"  /></p>

            </form>

            <hr />

            <h3><?php echo esc_html(__('Global Settings', 'coinqvest'))?></h3>

            <p><?php echo esc_html(__('Global settings overwrite JSON parameters in payment buttons.', 'coinqvest'))?></p>

            <div id="coinqvest_form_feedback"></div>

            <form action="<?=esc_url(admin_url('admin-post.php'));?>" method="POST" id="coinqvest_ajax_form">

                <input type="hidden" name="action" value="coinqvest_admin_form_response">
		        <?php wp_nonce_field( 'submitGlobalSettings-abg3@9' ); ?>
                <input type="hidden" name="task" value="submit_global_settings">

                <table class="form-table" role="presentation">

                    <tr>
                        <th scope="row"><?php echo esc_html(__('Return URL', 'coinqvest'))?> <span class="optional">(<?php _e('optional', 'coinqvest')?>)</span></th>
                        <td>
                            <input name="cq_return_url" type="text" id="cq_return_url" value="<?=esc_url($return_url)?>" placeholder="https://www.your-domain.com/return-url" class="regular-text" />
                            <p class="description"><?php echo esc_html(__('Specifies where to send the customer when the payment successfully completed.', 'coinqvest'))?></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><?php echo esc_html(__('Cancel URL', 'coinqvest'))?> <span class="optional">(<?php echo esc_html(__('optional', 'coinqvest'))?>)</span></th>
                        <td>
                            <input name="cq_cancel_url" type="text" id="cq_cancel_url" value="<?=esc_url($cancel_url)?>" placeholder="https://www.your-domain.com/cancel-url" class="regular-text" />
                            <p class="description"><?php echo esc_html(__('Specifies where to send the customer when he wishes to cancel the checkout process.', 'coinqvest'))?></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><?php echo esc_html(__('Webhook URL', 'coinqvest'))?> <span class="optional">(<?php echo esc_html(__('optional', 'coinqvest'))?>)</span></th>
                        <td>
                            <input name="cq_webhook_url" type="text" id="cq_webhook_url" value="<?=esc_url($webhook_url)?>" placeholder="https://www.your-domain.com/webhook-url" class="regular-text" />
                            <p class="description"><?php echo esc_html(__('A webhook URL on your server that listens for payment events.', 'coinqvest'))?></p>
                        </td>
                    </tr>

                    <?php if (!empty($fiat_currencies)) { ?>

                    <tr>
                        <th scope="row"><?php echo esc_html(__('Settlement Currency', 'coinqvest'))?> <span class="optional">(<?php echo esc_html(__('optional', 'coinqvest'))?>)</span></th>
                        <td>
                            <select name="cq_settlement_currency" id="cq_settlement_currency">

                                <option value="0" <?=($settlement_currency == "0") ? 'selected="selected"' : null?>>=== <?php echo esc_html(__('Select currency', 'coinqvest'))?> ===</option>

                                <?php foreach ($fiat_currencies->fiatCurrencies as $currency) { ?>

                                <option value="<?=esc_attr($currency->assetCode)?>" <?=($settlement_currency == $currency->assetCode) ? 'selected="selected"' : null?>><?=esc_html($currency->assetCode)?> - <?=esc_html($currency->assetName)?></option>

                                <?php } ?>

                            </select>

                            <p class="description"><?php echo esc_html(__('The currency that the crypto payments get converted to. If you don\'t choose a currency here, the settlement currency will be the billing currency.', 'coinqvest'))?></p>
                        </td>
                    </tr>

                    <?php } ?>

                    <tr>
                        <th scope="row"><?php echo esc_html(__('Required customer info', 'coinqvest'))?></th>
                        <td>
                            <select name="cq_customer_info" id="cq_customer_info">
                                <option value="none" <?=($customer_info == "none") ? 'selected="selected"' : null?>><?php echo esc_html(__('None', 'coinqvest'))?></option>
                                <option value="minimal" <?=($customer_info == "minimal") ? 'selected="selected"' : null?>><?php echo esc_html(__('Minimal', 'coinqvest'))?></option>
                                <option value="compliant" <?=($customer_info == "compliant") ? 'selected="selected"' : null?>><?php echo esc_html(__('Compliant', 'coinqvest'))?></option>
                            </select>
                            <p class="description">
	                            <?php echo esc_html(__('Defines what customer data to collect. Main purpose is to connect a payment to a customer and staying tax compliant.', 'coinqvest'))?><br />
	                            <?php echo esc_html(__('- None: No customer data required. You will receive the payment amount, but will not know from whom. Can be used for donations.', 'coinqvest'))?><br />
	                            <?php echo esc_html(__('- Minimal (default): Email and firstname + lastname', 'coinqvest'))?><br />
	                            <?php echo esc_html(__('- Compliant: All data that is required to generate invoices', 'coinqvest'))?>
                            </p>
                        </td>
                    </tr>

                </table>

                <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo esc_html(__('Save', 'coinqvest'))?>"  /></p>

            </form>

        </div>

		<?php

	}

	public function submit_form_api_settings() {

        /**
         * Sanitize input parameters
         */

        $api_key = !empty($_POST['cq_api_key']) ? sanitize_text_field($_POST['cq_api_key']) : null;
        $api_secret = !empty($_POST['cq_api_secret']) ? sanitize_text_field($_POST['cq_api_secret']) : null;
        $is_ajax = (isset( $_POST['ajaxrequest']) && $_POST['ajaxrequest'] === 'true') ? true : false;

        /**
         * Input validation
         */

		if (is_null($api_key) || is_null($api_secret)) {
			$result = "error";
			$message = esc_html(__('Please provide API Key and API Secret', 'coinqvest'));
			$page = "coinqvest-settings";

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

		if (strlen($api_key) != 12) {
			$result = "error";
			$message = esc_html(__('API key seems to be wrong. Please double check.', 'coinqvest'));
			$page = "coinqvest-settings";

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

		if (strlen($api_secret) != 29) {
			$result = "error";
			$message = esc_html(__('API secret seems to be wrong. Please double check.', 'coinqvest'));
			$page = "coinqvest-settings";

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
         * Init COINQVEST API
         */
		$client = new Api\CQ_Merchant_Client(
			$api_key,
			$api_secret,
			true
		);

		$response = $client->get('/auth-test');

		if ($response->httpStatusCode != 200) {

            $result = "error";
            $message = esc_html(__('API key and/or API secret are wrong.', 'coinqvest'));
            $page = "coinqvest-settings";

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

		$settings = array(
		    "api_key" => $api_key,
            "api_secret" => $api_secret
        );

		$this->build_settings_string($settings);

		$result = "success";
		$message = esc_html(__('API settings saved successfully.', 'coinqvest'));
		$page = "coinqvest-settings";

        if ($is_ajax === true) {
            Common_Helpers::renderResponse(array(
                "success" => true,
                "message" => $message
            ));
		} else {
			$this->redirect = new Admin_Helpers();
			$this->redirect->custom_redirect($result, $message, $page);
		}
		exit;
	}

	public function submit_form_global_settings() {

		$webhook_url = !empty($_POST['cq_webhook_url']) ? esc_url_raw($_POST['cq_webhook_url']) : null;
		$cancel_url = !empty($_POST['cq_cancel_url']) ? esc_url_raw($_POST['cq_cancel_url']) : null;
		$return_url =  !empty($_POST['cq_return_url']) ? esc_url_raw($_POST['cq_return_url']) : null;
		$settlement_currency =  sanitize_text_field($_POST['cq_settlement_currency']);
		$customer_info = sanitize_text_field($_POST['cq_customer_info']);
        $is_ajax = (isset( $_POST['ajaxrequest']) && $_POST['ajaxrequest'] === 'true') ? true : false;

		$settings = array(
			"webhook_url" => $webhook_url,
			"cancel_url" => $cancel_url,
			"return_url" => $return_url,
            "settlement_currency" => $settlement_currency,
            "customer_info" => $customer_info
		);

		$this->build_settings_string($settings);

		$result = "success";
		$message = esc_html(__('Global settings saved successfully.', 'coinqvest'));
		$page = "coinqvest-settings";

        if ($is_ajax === true) {
            Common_Helpers::renderResponse(array(
                "success" => true,
                "message" => $message
            ));
		} else {
			$this->redirect = new Admin_Helpers();
			$this->redirect->custom_redirect($result, $message, $page);
		}
		exit;

	}

	protected function build_settings_string($params) {

		$settings_exist = get_option('coinqvest_settings');

		if (!empty($settings_exist)) {

		    $settings = unserialize($settings_exist);

		    foreach ($params as $key => $value) {
		        $settings[$key] = $value;
            }

			update_option('coinqvest_settings', serialize($settings));

		} else {

		    $settings = $params;

			add_option('coinqvest_settings', serialize($settings));
        }

		return true;

    }

}