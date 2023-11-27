<?php

namespace Whalestack\Inc\Admin;

use Whalestack\Inc\Libraries\API;
use Whalestack\Inc\Common\Common_Helpers;

class Settings {

	private $page = "whalestack-settings";

	public function __construct(  ) {

	}

	public function render_settings_page() {

        $settings = get_option('whalestack_settings');
        $settings = unserialize($settings);

        $api_key = isset($settings['api_key']) ? $settings['api_key'] : null;
        $api_secret = isset($settings['api_secret']) ? $settings['api_secret'] : null;
        $webhook_url = isset($settings['webhook_url']) ? $settings['webhook_url'] : null;
		$return_url = isset($settings['return_url']) ? $settings['return_url'] : null;
		$cancel_url = isset($settings['cancel_url']) ? $settings['cancel_url'] : null;
		$settlement_currency = isset($settings['settlement_currency']) ? $settings['settlement_currency'] : null;
        $checkout_language = isset($settings['checkout_language']) ? $settings['checkout_language'] : null;
		$customer_info = isset($settings['customer_info']) ? $settings['customer_info'] : null;
        $debug_log = isset($settings['debug_log']) ? $settings['debug_log'] : null;

		$settlement_assets = array();
		$checkout_languages = array();
        $checkout_languages['auto'] = 'Automatic';

		if (!empty($api_key) && !empty($api_secret)) {

			$client = new Api\Whalestack_Merchant_Client($api_key, $api_secret, true);

            /**
             * Get settlement currencies/assets
             */

            $response = $client->get('/assets');
            if ($response->httpStatusCode == 200) {
                $assets = json_decode($response->responseBody);
                foreach ($assets->assets as $asset) {
                    $settlement_assets[$asset->id] = esc_html($asset->name);
                }
            }

            /**
             * Get checkout page languages
             */

            $response = $client->get('/languages');
            if ($response->httpStatusCode == 200) {
                $languages = json_decode($response->responseBody);
                foreach ($languages->languages as $language) {
                    $checkout_languages[$language->languageCode] = esc_html($language->name);
                }
            }

        }

		?>

        <div class="wrap">

            <h1><?php echo esc_html(__( 'Whalestack API and Global Settings', 'whalestack' ))?></h1>

            <h3><?php echo esc_html(__( 'API Settings', 'whalestack' ))?></h3>

            <p><a href="https://www.whalestack.com/en/api-settings" target="_blank"><?php echo esc_html(sprintf(__('Get your API Keys on www.whalestack.com', 'whalestack')))?></a></p>

            <div id="whalestack_form_feedback"></div>

            <form action="<?=esc_url(admin_url('admin-post.php'));?>" method="POST" id="whalestack_ajax_form">

                <input type="hidden" name="action" value="whalestack_admin_form_response">
                <?php wp_nonce_field( 'submitApiSettings-23iyj@h!' ); ?>
                <input type="hidden" name="task" value="submit_api_settings">

                <table class="form-table" role="presentation">

                    <tr>
                        <th scope="row"><?php echo esc_html(__('API Key', 'whalestack'))?></th>
                        <td><input name="whalestack_api_key" type="text" id="whalestack_api_key" value="<?=esc_attr($api_key)?>" class="regular-text" /></td>
                    </tr>

                    <tr>
                        <th scope="row"><?php echo esc_html(__('API Secret', 'whalestack'))?></th>
                        <td><input name="whalestack_api_secret" type="text" id="whalestack_api_secret" value="<?=esc_attr($api_secret)?>" class="regular-text" /></td>
                    </tr>

                </table>

                <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo esc_html(__('Save', 'whalestack'))?>"  /></p>

            </form>

            <hr />

            <h3><?php echo esc_html(__('Global Settings', 'whalestack'))?></h3>

            <p><?php echo esc_html(__('Global settings overwrite JSON parameters in payment buttons.', 'whalestack'))?></p>

            <div id="whalestack_form_feedback"></div>

            <form action="<?=esc_url(admin_url('admin-post.php'));?>" method="POST" id="whalestack_ajax_form">

                <input type="hidden" name="action" value="whalestack_admin_form_response">
		        <?php wp_nonce_field( 'submitGlobalSettings-abg3@9' ); ?>
                <input type="hidden" name="task" value="submit_global_settings">

                <table class="form-table" role="presentation">

                    <tr>
                        <th scope="row"><?php echo esc_html(__('Return URL', 'whalestack'))?> <span class="optional">(<?php _e('optional', 'whalestack')?>)</span></th>
                        <td>
                            <input name="whalestack_return_url" type="text" id="whalestack_return_url" value="<?=esc_url($return_url)?>" placeholder="https://www.your-domain.com/return-url" class="regular-text" />
                            <p class="description"><?php echo esc_html(__('Specifies where to send the customer when the payment successfully completed. Also requires to set a `Cancel URL`.', 'whalestack'))?></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><?php echo esc_html(__('Cancel URL', 'whalestack'))?> <span class="optional">(<?php echo esc_html(__('optional', 'whalestack'))?>)</span></th>
                        <td>
                            <input name="whalestack_cancel_url" type="text" id="whalestack_cancel_url" value="<?=esc_url($cancel_url)?>" placeholder="https://www.your-domain.com/cancel-url" class="regular-text" />
                            <p class="description"><?php echo esc_html(__('Specifies where to send the customer when he wishes to cancel the checkout process. Also requires to set a `Return URL`.', 'whalestack'))?></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><?php echo esc_html(__('Webhook URL', 'whalestack'))?> <span class="optional">(<?php echo esc_html(__('optional', 'whalestack'))?>)</span></th>
                        <td>
                            <input name="whalestack_webhook_url" type="text" id="whalestack_webhook_url" value="<?=esc_url($webhook_url)?>" placeholder="https://www.your-domain.com/webhook-url" class="regular-text" />
                            <p class="description"><?php echo esc_html(__('A webhook URL on your server that listens for payment events.', 'whalestack'))?></p>
                        </td>
                    </tr>

                    <?php if (!empty($settlement_assets)) { ?>

                    <tr>
                        <th scope="row"><?php echo esc_html(__('Settlement Currency', 'whalestack'))?> <span class="optional">(<?php echo esc_html(__('optional', 'whalestack'))?>)</span></th>
                        <td>
                            <select name="whalestack_settlement_currency" id="whalestack_settlement_currency">

                                <option value="0" <?=($settlement_currency == "0") ? 'selected="selected"' : null?>>=== <?php echo esc_html(__('Select currency', 'whalestack'))?> ===</option>
                                <option value="ORIGIN" <?=($settlement_currency == "ORIGIN") ? 'selected="selected"' : null?>>ORIGIN</option>

                                <?php foreach ($settlement_assets as $key => $value) { ?>

                                <option value="<?=esc_attr($key)?>" <?=($settlement_currency == $key) ? 'selected="selected"' : null?>><?=esc_html($value)?></option>

                                <?php } ?>

                            </select>

                            <p class="description">
                                <?php echo esc_html(__('The currency that the crypto payments get converted to. ', 'whalestack'))?><br />
                                <?php echo esc_html(__('- If you don\'t choose a currency, you will be credited in the billing currency.', 'whalestack'))?><br />
                                <?php echo esc_html(__('- Choose ORIGIN if you want to get credited in the exact same currency your customer paid in (without any conversion).', 'whalestack'))?>
                            </p>
                        </td>
                    </tr>

                    <?php } ?>

                    <?php if (!empty($checkout_languages)) { ?>

                        <tr>
                            <th scope="row"><?php echo esc_html(__('Checkout Language', 'whalestack'))?> <span class="optional">(<?php echo esc_html(__('optional', 'whalestack'))?>)</span></th>
                            <td>
                                <select name="whalestack_checkout_language" id="whalestack_checkout_language">

                                    <option value="0" <?=($checkout_language == "0") ? 'selected="selected"' : null?>>=== <?php echo esc_html(__('Select language', 'whalestack'))?> ===</option>

                                    <?php foreach ($checkout_languages as $key => $value) { ?>

                                        <option value="<?=esc_attr($key)?>" <?=($checkout_language == $key) ? 'selected="selected"' : null?>><?=esc_html($key)?> - <?=esc_html($value)?></option>

                                    <?php } ?>

                                </select>

                                <p class="description"><?php echo esc_html(__('The language that your checkout page will display in. Choose \'auto\' to automatically detect the customer\'s main browser language. Fallback language code is \'en\'.', 'whalestack'))?></p>
                            </td>
                        </tr>

                    <?php } ?>

                    <tr>
                        <th scope="row"><?php echo esc_html(__('Required customer info', 'whalestack'))?></th>
                        <td>
                            <select name="whalestack_customer_info" id="whalestack_customer_info">
                                <option value="minimal" <?=($customer_info != "compliant") ? 'selected="selected"' : null?>><?php echo esc_html(__('Minimal', 'whalestack'))?></option>
                                <option value="compliant" <?=($customer_info == "compliant") ? 'selected="selected"' : null?>><?php echo esc_html(__('Compliant', 'whalestack'))?></option>
                            </select>
                            <p class="description">
	                            <?php echo esc_html(__('Defines what customer data to collect.', 'whalestack'))?><br />
	                            <?php echo esc_html(__('- Minimal (default): Email and firstname + lastname', 'whalestack'))?><br />
	                            <?php echo esc_html(__('- Compliant: All data that is required to generate invoices', 'whalestack'))?>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><?php echo esc_html(__('Debug Log', 'whalestack'))?></th>
                        <td>
                            <select name="whalestack_debug_log" id="whalestack_debug_log">
                                <option value="yes" <?=($debug_log != "no") ? 'selected="selected"' : null?>><?php echo esc_html(__('Yes', 'whalestack'))?></option>
                                <option value="no" <?=($debug_log == "no") ? 'selected="selected"' : null?>><?php echo esc_html(__('No', 'whalestack'))?></option>
                            </select>
                            <p class="description">
                                <?php echo esc_html(__('Log Whalestack API events.', 'whalestack'))?><br />
                            </p>
                        </td>
                    </tr>

                </table>

                <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo esc_html(__('Save', 'whalestack'))?>"  /></p>

            </form>

        </div>

		<?php

	}

	public function submit_form_api_settings() {

        /**
         * Sanitize input parameters
         */

        $api_key = !empty($_POST['whalestack_api_key']) ? sanitize_text_field($_POST['whalestack_api_key']) : null;
        $api_secret = !empty($_POST['whalestack_api_secret']) ? sanitize_text_field($_POST['whalestack_api_secret']) : null;
        $is_ajax = (isset( $_POST['ajaxrequest']) && $_POST['ajaxrequest'] === 'true') ? true : false;

        /**
         * Input validation
         */

		if (is_null($api_key) || is_null($api_secret)) {
			$message = esc_html(__('Please provide API Key and API Secret', 'whalestack'));
            Admin_Helpers::renderAdminErrorMessage($message, $this->page, $is_ajax);
		}

		if (strlen($api_key) != 12) {
			$message = esc_html(__('API key seems to be wrong. Please double check.', 'whalestack'));
            Admin_Helpers::renderAdminErrorMessage($message, $this->page, $is_ajax);
		}

		if (strlen($api_secret) != 29) {
			$message = esc_html(__('API secret seems to be wrong. Please double check.', 'whalestack'));
            Admin_Helpers::renderAdminErrorMessage($message, $this->page, $is_ajax);
		}

        /**
         * Init Whalestack API
         */

		$client = new Api\Whalestack_Merchant_Client($api_key, $api_secret, true);

		$response = $client->get('/auth-test');

		if ($response->httpStatusCode != 200) {
            $message = esc_html(__('API key and/or API secret are wrong.', 'whalestack'));
            Admin_Helpers::renderAdminErrorMessage($message, $this->page, $is_ajax);
        }

		$settings = array(
		    "api_key" => $api_key,
            "api_secret" => $api_secret
        );

		$this->build_settings_string($settings);

		$message = esc_html(__('API settings saved successfully.', 'whalestack'));
        Admin_Helpers::renderAdminSuccessMessage($message, $this->page, $is_ajax);
	}

	public function submit_form_global_settings() {

		$webhook_url = !empty($_POST['whalestack_webhook_url']) ? esc_url_raw($_POST['whalestack_webhook_url']) : null;
		$cancel_url = !empty($_POST['whalestack_cancel_url']) ? esc_url_raw($_POST['whalestack_cancel_url']) : null;
		$return_url =  !empty($_POST['whalestack_return_url']) ? esc_url_raw($_POST['whalestack_return_url']) : null;
		$settlement_currency =  sanitize_text_field($_POST['whalestack_settlement_currency']);
        $checkout_language =  sanitize_text_field($_POST['whalestack_checkout_language']);
		$customer_info = sanitize_text_field($_POST['whalestack_customer_info']);
        $debug_log = sanitize_text_field($_POST['whalestack_debug_log']);
        $is_ajax = (isset( $_POST['ajaxrequest']) && $_POST['ajaxrequest'] === 'true') ? true : false;

        /**
         * Input validation
         */

        if (!is_null($return_url) && is_null($cancel_url)) {
            $message = esc_html(__('Please set a Cancel URL as well.', 'whalestack'));
            Admin_Helpers::renderAdminErrorMessage($message, $this->page, $is_ajax);
        }

        if (!is_null($cancel_url) && is_null($return_url)) {
            $message = esc_html(__('Please set a Return URL as well.', 'whalestack'));
            Admin_Helpers::renderAdminErrorMessage($message, $this->page, $is_ajax);
        }

        /**
         * Save
         */

		$settings = array(
			"webhook_url" => $webhook_url,
			"cancel_url" => $cancel_url,
			"return_url" => $return_url,
            "settlement_currency" => $settlement_currency,
            "checkout_language" => $checkout_language,
            "customer_info" => $customer_info,
            "debug_log" => $debug_log
		);

		$this->build_settings_string($settings);

		$message = esc_html(__('Global settings saved successfully.', 'whalestack'));
        Admin_Helpers::renderAdminSuccessMessage($message, $this->page, $is_ajax);

	}

	protected function build_settings_string($params) {

		$settings_exist = get_option('whalestack_settings');

		if (!empty($settings_exist)) {

		    $settings = unserialize($settings_exist);

		    foreach ($params as $key => $value) {
		        $settings[$key] = $value;
            }

			update_option('whalestack_settings', serialize($settings));

		} else {

		    $settings = $params;

			add_option('whalestack_settings', serialize($settings));
        }

		return true;

    }

}