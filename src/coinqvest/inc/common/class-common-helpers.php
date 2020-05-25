<?php
namespace COINQVEST\Inc\Common;

use COINQVEST\Inc\Libraries\API\CQLoggingService;

class Common_Helpers {

	public function get_coinqvest_api_key_secret() {

		$api_settings = get_option('coinqvest_settings');
		$api_settings = unserialize($api_settings);

		$api_key = $api_settings['api_key'];
		$api_secret = $api_settings['api_secret'];

		if (!isset($api_key) || !isset($api_secret)) {
			return false;
		}

		return array(
			"api_key" => $api_key,
			"api_secret" => $api_secret
		);

	}

	public static function calculate_price($json) {

		$json = json_decode($json, true);

		$total = 0;
		foreach ($json['charge']['lineItems'] as $lineItem) {

			$quantity = (isset($lineItem['quantity']) ? $lineItem['quantity'] : 1);

			$price = $lineItem['netAmount'] * $quantity;

			$total += $price;
		}

		return number_format_i18n($total, 2) . ' ' . $json['charge']['currency'];

	}

	public static function validate_required_form_fields($requiredFields, $params) {

		$errors = array();
		foreach ($requiredFields as $field) {

			if (!$params[$field] || $params[$field] == '') {
				$errors[] = $field;
			}

		}

		if (!empty($errors)) {
			return $errors;
		}

		return false;
	}

	public static function pretty_json_example() {

		$example_json = '{"charge":{"currency":"USD","lineItems":[{"description":"T-Shirt","netAmount":10}]}}';
		return json_encode(json_decode($example_json), JSON_PRETTY_PRINT);
	}

//	public static function log($message) {
//
//		$log = new CQLoggingService();
//		$log::write($message);
//
//		return true;
//
//	}


}