<?php
namespace COINQVEST\Inc\Libraries\Api;

defined( 'ABSPATH' ) or exit;

/**
 * Class CQLoggingService
 *
 * A logging service
 */
class CQLoggingService {

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