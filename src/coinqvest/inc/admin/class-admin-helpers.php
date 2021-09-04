<?php
namespace COINQVEST\Inc\Admin;
use COINQVEST\Inc\Common\Common_Helpers;
use COINQVEST\Inc\Libraries\Api\CQ_Logging_Service;

class Admin_Helpers {

	public static function custom_redirect($result, $message, $page) {

		wp_redirect(
			esc_url_raw(
				add_query_arg(
					array(
						"result" => $result,
						"message" => $message
					),
					admin_url('admin.php?page=' . $page)
				)
			)
		);
	}


	public function print_plugin_admin_notices() {

		if (isset($_REQUEST['result'])) {

		    $html = '';

		    $message = sanitize_text_field($_GET['message']);

			if ($_REQUEST['result'] === "success") {

				$html =	'<div class="notice notice-success is-dismissible"><p>' . esc_html($message) . '</p></div>';

			} elseif ($_REQUEST['result'] === "error") {

				$html =	'<div class="notice notice-error is-dismissible"><p>' . esc_html($message) . '</p></div>';

			}

			echo $html;

		}  else {

			return;
		}

	}

    public static function renderAdminErrorMessage($message, $page, $is_ajax = false) {

        CQ_Logging_Service::write("Page: " . $page . " -- " . $message);

        if ($is_ajax === true) {
            Common_Helpers::renderResponse(array(
                "success" => false,
                "message" => $message
            ));
        } else {
            self::custom_redirect('error', $message, $page);
        }
        exit;

    }

    public static function renderAdminSuccessMessage($message, $page, $is_ajax) {

        if ($is_ajax === true) {
            Common_Helpers::renderResponse(array(
                "success" => true,
                "message" => $message,
                "redirect" => "/wp-admin/admin.php?page=coinqvest-payment-buttons"
            ));
        } else {
            self::custom_redirect('success', $message, $page);
        }
        exit;

    }

}