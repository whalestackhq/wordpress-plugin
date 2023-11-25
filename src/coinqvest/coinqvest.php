<?php

/**
 * Plugin Name: COINQVEST
 * Description: Enterprise Cryptocurrency Payment Processor - Accept cryptocurrencies (BTC, LTC, XLM) and stablecoins (USDC, EURC) from your clients and instantly settle in your preferred payout currency like USD, EUR, or BRL.
 * Author: COINQVEST
 * Author URI: https://www.coinqvest.com/
 * Version: 1.1.9
 * License: Apache 2.0
 * License URI: https://www.apache.org/licenses/LICENSE-2.0
 * Text domain: coinqvest
 * Domain Path: /languages
 */

namespace COINQVEST;

defined('ABSPATH') or exit;

/**
 * Define Constants
 */
define(__NAMESPACE__ . '\NS', __NAMESPACE__ . '\\');
define(NS . 'PLUGIN_NAME', 'coinqvest');
define(NS . 'PLUGIN_VERSION', '1.1.9');
define(NS . 'PLUGIN_NAME_DIR', plugin_dir_path( __FILE__ ));
define(NS . 'PLUGIN_NAME_URL', plugin_dir_url( __FILE__ ));
define(NS . 'PLUGIN_BASENAME', plugin_basename( __FILE__ ));
define(NS . 'PLUGIN_TEXT_DOMAIN', 'coinqvest');

/**
 * Autoload Classes
 */
require_once(PLUGIN_NAME_DIR . 'inc/libraries/autoloader.php');


/**
 * Register Activation and Deactivation Hooks
 */
register_activation_hook(__FILE__, array(NS . 'Inc\Core\Activator', 'activate'));
register_deactivation_hook(__FILE__, array(NS . 'Inc\Core\Deactivator', 'deactivate'));


/**
 * Plugin Singleton Container
 */
class COINQVEST {

	static $init;

	public static function init() {

		if ( null == self::$init ) {
			self::$init = new Inc\Core\Init();
			self::$init->run();
		}

		return self::$init;
	}

}

function cqInit() {
	return COINQVEST::init();
}

$min_php = '5.6.0';

// Check the minimum required PHP version and run the plugin.
if ( version_compare( PHP_VERSION, $min_php, '>=' ) ) {
	cqInit();
}