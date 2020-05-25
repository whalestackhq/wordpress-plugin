<?php

namespace COINQVEST\Inc\Core;

class Deactivator {

	public static function deactivate() {

		chmod(plugin_dir_path( __FILE__ ) . '../logs/', 0644);

	}

}
