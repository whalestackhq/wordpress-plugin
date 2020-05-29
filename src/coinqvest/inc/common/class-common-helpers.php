<?php
namespace COINQVEST\Inc\Common;

class Common_Helpers {

	public function get_coinqvest_credentials() {

		$api_settings = get_option('coinqvest_settings');
		$api_settings = unserialize($api_settings);

		$api_key = $api_settings['api_key'];
		$api_secret = $api_settings['api_secret'];

		if (!isset($api_key) || !isset($api_secret)) {
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

		$example_json = '{"charge":{"currency":"USD","lineItems":[{"description":"T-Shirt","netAmount":10}]}}';
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


}