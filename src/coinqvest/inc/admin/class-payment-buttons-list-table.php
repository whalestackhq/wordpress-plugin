<?php
namespace COINQVEST\Inc\Admin;
use COINQVEST\Inc\Libraries;
use COINQVEST\Inc\Common\Common_Helpers;

ob_start();

class Payment_Buttons_List_Table extends Libraries\WP_List_Table  {

	protected $plugin_text_domain;
	private $redirect;

	/** Class constructor */
	public function __construct($plugin_text_domain) {

		$this->plugin_text_domain = $plugin_text_domain;

		parent::__construct( [
			'singular' => esc_html(__( 'Item', $this->plugin_text_domain )), //singular name of the listed records
			'plural'   => esc_html(__( 'Items', $this->plugin_text_domain )), //plural name of the listed records
			'ajax'     => false //does this table support ajax?
		] );

	}

	/**
	 * Handles data query and filter, sorting, and pagination.
	 */
	public function prepare_items() {

		if (!session_id()) {
			session_start();
		}

		$this->_column_headers = $this->get_column_info();

		// check and process any actions such as bulk actions.
		$this->handle_table_actions();

		if (isset($_REQUEST['wp_screen_options']['value'])) {
			$_SESSION['cq-buttons-list-value'] = sanitize_text_field($_REQUEST['wp_screen_options']['value']);
		}
		$items = isset($_SESSION['cq-buttons-list-value']) ? $_SESSION['cq-buttons-list-value'] : 20;

		$items_per_page = $this->get_items_per_page( 'buttons_per_page', $items );

		$current_page = $this->get_pagenum();
		$total_items = self::record_count();

		$this->set_pagination_args( [
			'total_items' => $total_items, //WE have to calculate the total number of items
			'per_page'    => $items_per_page, //WE have to determine how many items to show on a page
		] );

		$this->items = self::get_items( $items_per_page, $current_page );
	}


	/**
	 * Retrieve items from the database
	 *
	 * @param int $per_page
	 * @param int $page_number
	 *
	 * @return mixed
	 */
	public static function get_items( $per_page = 20, $page_number = 1 ) {

		global $wpdb;

		$sql = "SELECT * FROM {$wpdb->prefix}coinqvest_payment_buttons";

		if ( ! empty( $_REQUEST['orderby'] ) ) {
			$sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
			$sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
		}

		$sql .= " LIMIT $per_page";
		$sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;

        return $wpdb->get_results( $sql, 'ARRAY_A' );

	}


	/**
	 * Returns the count of records in the database.
	 *
	 * @return null|string
	 */
	public static function record_count() {
		global $wpdb;

		$sql = "SELECT COUNT(*) FROM {$wpdb->prefix}coinqvest_payment_buttons";

		return $wpdb->get_var( $sql );
	}


	/** Text displayed when no data is available */
	public function no_items() {
		echo esc_html(__('No payment buttons available yet. Add a new one first.'));
	}


	/** Render a column when no column specific method exist. */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'name':
			case 'shortcode':
			case 'status':
			case 'price':
			case 'time':
				return date('Y-m-d', strtotime($item[ $column_name ]));
			case 'cssclass':
				return $item[ $column_name ];
			case 'buttontext':
			default:
				return print_r( $item, true ); //Show the whole array for troubleshooting purposes
		}
	}

	/** Custom Method for name column */
	function column_name( $item ) {

		$delete_nonce = wp_create_nonce( 'cq_delete_item' );

		$title = '<strong>' . esc_html($item['name']) . '</strong>';

		$actions = [
			'edit' => sprintf('<a href="/wp-admin/admin.php?page=coinqvest-edit-payment-button&id=%s">' . esc_html(__('Edit', 'coinqvest')) .'</a>', absint( $item['hashid'] ) ),
			'delete' => sprintf( '<a href="?page=%s&action=%s&item=%s&_wpnonce=%s">' . esc_html(__('Delete', 'coinqvest')) .'</a>', sanitize_text_field( $_REQUEST['page'] ), 'delete', absint( $item['hashid'] ), $delete_nonce )
		];

		return $title . $this->row_actions( $actions );
	}

	/** Custom Method for shortcode column */
	function column_shortcode( $item ) {
		$shortcode =  '<input type="text" class="terminal" onfocus="this.select();" value="[COINQVEST_checkout id=&quot;' . absint($item['hashid']). '&quot;]" readonly="readonly" />';
		return $shortcode;
	}

	/** Custom Method for price column */
	function column_price( $item ) {
	    $price = number_format_i18n($item['total'], $item['decimals']) . ' ' . $item['currency'];
		return $price;
	}

	/** Custom Method for status column */
	function column_status( $item ) {
		return ($item['status'] == 1) ? '<span class="active">active</span>' : '<span class="inactive">inactive</span>';
	}

    /** Custom Method for button text column */
    function column_buttontext( $item ) {
        $button_text = is_null($item['buttontext']) ? __('Buy Now', 'coinqvest') : $item['buttontext'];
        return '<span class="button">' . esc_html($button_text) . '</span>';
    }

	/** Associative array of columns */
	function get_columns() {
		$columns = [
			'name'      => esc_html(__( 'Name', $this->plugin_text_domain )),
			'status'    => esc_html(__( 'Status', $this->plugin_text_domain )),
			'price'    => esc_html(__( 'Price', $this->plugin_text_domain )),
			'shortcode' => esc_html(__( 'Shortcode', $this->plugin_text_domain )),
			'buttontext'    => esc_html(__( 'Button text', $this->plugin_text_domain )),
			'cssclass'    => esc_html(__( 'CSS class', $this->plugin_text_domain )),
			'time'    => esc_html(__( 'Time', $this->plugin_text_domain ))
		];

		return $columns;
	}

	/** Columns to make sortable. */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'name' => array( 'name', true ),
			'status' => array('status', true)
		);
		return $sortable_columns;
	}


	public function handle_table_actions() {

		if ( 'delete' === $this->current_action() ) {

			// In our file that handles the request, verify the nonce.
			$nonce = sanitize_text_field( $_REQUEST['_wpnonce'] );

			if ( ! wp_verify_nonce( $nonce, 'cq_delete_item' ) ) {
				die( 'Go get a life script kiddies' );
			}
			else {

			    $id = absint( $_GET['item']);

				// can only be deleted when status is inactive
				global $wpdb;
				$table_name = $wpdb->prefix . 'coinqvest_payment_buttons';
				$row = $wpdb->get_row("SELECT hashid, status FROM ".$table_name." WHERE hashid = " . $id);

				if ($row->status == 1) {
					$result = "error";
					$message = esc_html(__('Cannot be deleted when status is active', 'coinqvest'));
					$page = "coinqvest-payment-buttons";
					$this->redirect = new Admin_Helpers();
					$this->redirect->custom_redirect($result, $message, $page);
					exit;
				}

				self::delete_item($id);

				$result = "success";
				$message = esc_html(__('Button successfully deleted', 'coinqvest'));
				$page = "coinqvest-payment-buttons";
				$this->redirect = new Admin_Helpers();
				$this->redirect->custom_redirect($result, $message, $page);
				exit;
			}

		}

	}

	/**
	 * Delete a record.
	 *
	 * @param int $id item ID
	 */
	public static function delete_item( $id ) {

		global $wpdb;

		$wpdb->delete(
			"{$wpdb->prefix}coinqvest_payment_buttons",
			[ 'hashid' => $id ],
			[ '%d' ]
		);
	}
	
}
