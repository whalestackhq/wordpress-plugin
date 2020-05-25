<?php

namespace COINQVEST\Inc\Admin;

class Logs {

	public function __construct(  ) {

	}

	function render_logs_page() {

		global $wpdb;
		$table_name = $wpdb->prefix . 'coinqvest_logs';

		$rows = $wpdb->get_results("SELECT message, time FROM ".$table_name." WHERE id > 0 ORDER BY id DESC LIMIT 50");

		$log_content = '';
		foreach ($rows as $row) {

			$message = strtr($row->message, array(
				"\\r\\n" => "",
				"\r" => "",
				"\n" => "",
				"\t" => " ",
                "\\" => ""
            ));

			$log_content .= $row->time . " " . $message . "\n\n";
        }

		?>

        <div class="wrap">

            <h1><?php _e('COINQVEST Debug Log', 'coinqvest')?></h1>

            <p><?php _e('Errors log and debugging', 'coinqvest')?></p>

            <div class="logs">

                <textarea class="terminal"><?=$log_content?></textarea>

            </div>

		<?php

	}

}