<?php
namespace COINQVEST\Inc\Common;

class Common_Helpers {

	public function get_coinqvest_credentials() {

		$api_settings = get_option('coinqvest_settings');
		$api_settings = unserialize($api_settings);

		$api_key = isset($api_settings['api_key']) ? $api_settings['api_key'] : null;
		$api_secret = isset($api_settings['api_secret']) ? $api_settings['api_secret'] : null;

		if (is_null($api_key) || is_null($api_secret)) {
			return null;
		}

		return array(
			"api_key" => $api_key,
			"api_secret" => $api_secret
		);

	}

	public static function validate_required_form_fields($requiredFields, $params) {

		$errors = array();
		foreach ($requiredFields as $field) {

			if (!$params[$field] || $params[$field] == '') {
				$errors[] = esc_attr($field);
			}

		}

		if (!empty($errors)) {
			return $errors;
		}

		return false;
	}

	public static function pretty_json_example() {

		$example_json = '{"charge":{"billingCurrency":"USD","lineItems":[{"description":"T-Shirt","netAmount":"9.99"}]},"settlementAsset":"USDC"}';
		return json_encode(json_decode($example_json), JSON_PRETTY_PRINT);
	}

    /**
     * Renders the HTTP response body for form submissions.
     * @param array $data
     */
	public static function renderResponse($data = array()) {
        echo json_encode($data);
        exit;

    }

    public static function format_display_price($total, $currency, $decimals) {

        if (in_array($currency, array('BTC', 'ETH', 'LTC', 'XRP', 'XLM'))) {

            // remove trailing zeros
            $total = floatval($total);

            // count number of decimals
            $no_of_decimals = strlen(substr(strrchr($total, "."), 1));

            return number_format_i18n($total, $no_of_decimals) . ' ' . $currency;

        }

        return number_format_i18n($total, 2) . ' ' . $currency;

    }

    public static function numberFormat($number, $decimals) {
	    return number_format($number, $decimals, '.', '');
    }

    public static function clean($string) {
        $string = preg_replace('/[^A-Za-z0-9\-_\s]/', '', $string); // Removes special chars except - and _ and blanks
        return $string;
    }

    public static function useNewParameterNaming($checkout) {

        if (isset($checkout['charge']['currency'])) {
            $checkout['charge']['billingCurrency'] = $checkout['charge']['currency'];
            unset($checkout['charge']['currency']);
        }

        if (isset($checkout['settlementCurrency'])) {
            $checkout['settlementAsset'] = $checkout['settlementCurrency'];
            unset($checkout['settlementCurrency']);
        }

        return $checkout;

    }



}