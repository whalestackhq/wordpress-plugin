<?php
namespace Whalestack\Inc\Frontend;
use Whalestack\Inc\Common\Common_Helpers;
use Whalestack\Inc\Libraries\Api\Whalestack_Logging_Service;

class Frontend_Helpers {

	public static function renderErrorMessage($message, $highlightFields = array()) {

        Whalestack_Logging_Service::write('[Whalestack Frontend Submit Checkout] ' . $message);

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