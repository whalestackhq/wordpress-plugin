<?php

/**
 * Plugin Name: Whalestack
 * Description: A digital currency payment gateway - Accept cryptocurrencies (BTC, LTC, XLM, Lightning) and stablecoins (USDC, EURC) from your customers and instantly settle in your preferred payout currency like USD, EUR, or BRL.
 * Author: Whalestack LLC
 * Author URI: https://www.whalestack.com/
 * Version: 2.0.0
 * License: Apache 2.0
 * License URI: https://www.apache.org/licenses/LICENSE-2.0
 * Text domain: whalestack
 * Domain Path: /languages
 */

namespace Whalestack;

defined('ABSPATH') or exit;

/**
 * Define Constants
 */
define(__NAMESPACE__ . '\NS', __NAMESPACE__ . '\\');
define(NS . 'PLUGIN_NAME', 'whalestack');
define(NS . 'PLUGIN_VERSION', '2.0.0');
define(NS . 'PLUGIN_NAME_DIR', plugin_dir_path( __FILE__ ));
define(NS . 'PLUGIN_NAME_URL', plugin_dir_url( __FILE__ ));
define(NS . 'PLUGIN_BASENAME', plugin_basename( __FILE__ ));
define(NS . 'PLUGIN_TEXT_DOMAIN', 'whalestack');

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
class Whalestack {

	static $init;

	public static function init() {

		if ( null == self::$init ) {
			self::$init = new Inc\Core\Init();
			self::$init->run();
		}

		return self::$init;
	}

}

function wsInit() {
	return Whalestack::init();
}

$min_php = '5.6.0';

// Check the minimum required PHP version and run the plugin.
if ( version_compare( PHP_VERSION, $min_php, '>=' ) ) {
	wsInit();
}