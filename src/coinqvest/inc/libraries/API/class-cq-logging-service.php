<?php
namespace COINQVEST\Inc\Libraries\Api;

defined( 'ABSPATH' ) or exit;

/**
 * Class CQ_Logging_Service
 *
 * A logging service
 */
class CQ_Logging_Service {

    /**
     * Writes to a log file and prepends current time stamp
     *
     * @param $message
     */
    public static function write($message) {

	    global $wpdb;
	    $table_name = $wpdb->prefix . 'coinqvest_logs';

	    $wpdb->insert(
		    $table_name,
		    array(
			    'message' => $message,
		    	'time' => current_time( 'mysql' )
		    )
	    );

    }

}