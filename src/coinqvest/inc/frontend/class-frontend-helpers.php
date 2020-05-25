<?php
namespace COINQVEST\Inc\Frontend;
class Frontend_Helpers {

	public function custom_redirect( $result, $message, $page ) {

		wp_redirect(
			esc_url_raw(
				add_query_arg(
					array(
						"result" => $result,
						"message" => $message
					),
					home_url($page)
				)
			)
		);
	}


}