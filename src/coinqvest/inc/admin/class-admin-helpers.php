<?php
namespace COINQVEST\Inc\Admin;
class Admin_Helpers {

	public function custom_redirect( $result, $message, $page ) {

		wp_redirect(
			esc_url_raw(
				add_query_arg(
					array(
						"result" => $result,
						"message" => $message
					),
					admin_url('admin.php?page=' . $page )
				)
			)
		);
	}


	public function print_plugin_admin_notices() {

		if (isset( $_REQUEST['result']) ) {

		    $html = '';

		    $message = sanitize_text_field($_GET['message']);

			if ($_REQUEST['result'] === "success") {

				$html =	'<div class="notice notice-success is-dismissible"><p>'.$message.'</p></div>';

			} elseif ($_REQUEST['result'] === "error") {

				$html =	'<div class="notice notice-error is-dismissible"><p>'.$message.'</p></div>';

			}

			echo $html;

		}  else {

			return;
		}

	}

}