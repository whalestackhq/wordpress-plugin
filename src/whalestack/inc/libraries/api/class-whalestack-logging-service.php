<?php
namespace Whalestack\Inc\Libraries\Api;

defined('ABSPATH') or exit;

/**
 * Class Whalestack_Logging_Service
 *
 * A logging service
 */
class Whalestack_Logging_Service {

    /**
     * Writes to a log file and prepends current time stamp
     *
     * @param $message
     */
    public static function write($message) {

        $ws_settings = get_option('whalestack_settings');
        $ws_settings = unserialize($ws_settings);
        $log = isset($ws_settings['debug_log']) ? $ws_settings['debug_log'] : null;

        if ($log && $log == 'no') {
            return;
        }

	    global $wpdb;
	    $table_name = $wpdb->prefix . 'whalestack_logs';

	    $wpdb->insert(
		    $table_name,
		    array(
			    'message' => $message,
		    	'time' => current_time('mysql')
		    )
	    );
    }

}