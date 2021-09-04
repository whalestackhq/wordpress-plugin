<?php
namespace COINQVEST\Inc\Frontend;
use COINQVEST\Inc\Common\Common_Helpers;
use COINQVEST\Inc\Libraries\Api\CQ_Logging_Service;

class Frontend_Helpers {

	public static function renderErrorMessage($message, $highlightFields = array()) {

	    CQ_Logging_Service::write('[CQ Frontend Submit Checkout] ' . $message);

	    $response = array(
            "success" => false,
            "message" => $message
        );

	    if (!empty($highlightFields)) {
	        $response['highlightFields'] = $highlightFields;
        }

        Common_Helpers::renderResponse($response);

    }


    public static function renderSuccessMessage($message, $url) {

        $response = array(
            "success" => true,
            "message" => $message,
            "redirect" => $url
        );

        Common_Helpers::renderResponse($response);

    }

}