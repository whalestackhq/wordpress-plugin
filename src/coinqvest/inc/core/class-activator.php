<?php

namespace COINQVEST\Inc\Core;

class Activator {

	public static function activate() {

		$min_php = '5.6.0';

		// Check PHP Version and deactivate & die if it doesn't meet minimum requirements.
		if ( version_compare( PHP_VERSION, $min_php, '<' ) ) {
            deactivate_plugins( plugin_basename( __FILE__ ) );
			wp_die( 'This plugin requires a minimum PHP Version of ' . $min_php );
		}

		/**
		 * Create tables
		 */

		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		$table_name = $wpdb->prefix . 'coinqvest_payment_buttons';

		$query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like($table_name));

		if (!$wpdb->get_var($query) == $table_name ) {

			$sql = "CREATE TABLE $table_name (
				id mediumint(9) NOT NULL AUTO_INCREMENT,
				hashid int(20) NOT NULL,
				time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
				status smallint(1) DEFAULT 1 NOT NULL,
				name tinytext NOT NULL,
				total decimal(20,7) DEFAULT NULL,
				decimals int(1) DEFAULT NULL,
				currency varchar(5) DEFAULT NULL,
				json text NOT NULL,
				cssclass varchar(100) DEFAULT NULL,
				buttontext varchar(50) DEFAULT NULL,
				PRIMARY KEY (id)
			) $charset_collate;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );

		}


		$table_name = $wpdb->prefix . 'coinqvest_logs';

		$query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like($table_name));

		if (!$wpdb->get_var($query) == $table_name ) {

			$sql = "CREATE TABLE $table_name (
				id int(20) NOT NULL AUTO_INCREMENT,
				message text NOT NULL,
				time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
				PRIMARY KEY (id)
			) $charset_collate;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
		}



		/**
		 * Create default settings
		 */
		$settings = array(
			"customer_info" => "minimal"
		);

		$settings_exist = get_option('coinqvest_settings');
		if (empty($settings_exist)) {
			add_option('coinqvest_settings', serialize($settings));
		}


	}


}
