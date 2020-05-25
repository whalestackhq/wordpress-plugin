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
			'singular' => __( 'Item', $this->plugin_text_domain ), //singular name of the listed records
			'plural'   => __( 'Items', $this->plugin_text_domain ), //plural name of the listed records
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
			$_SESSION['cq-buttons-list-value'] = $_REQUEST['wp_screen_options']['value'];
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

		$sql = "SELECT hashid, name, status, json, time, cssclass, buttontext FROM {$wpdb->prefix}coinqvest_payment_buttons";

		if ( ! empty( $_REQUEST['orderby'] ) ) {
			$sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
			$sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
		}

		$sql .= " LIMIT $per_page";
		$sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;

		$result = $wpdb->get_results( $sql, 'ARRAY_A' );

		return $result;
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
		echo sprintf(__( 'No payment buttons available yet. <a href="%s">Create one here</a>.', $this->plugin_text_domain ), '/wp-admin/admin.php?page=coinqvest-add-payment-button');
	}


	/** Render a column when no column specific method exist. */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'name':
			case 'shortcode':
			case 'status':
			case 'price':
			case 'time':
				return $item[ $column_name ];
			case 'cssclass':
				return $item[ $column_name ];
			case 'buttontext':
				return '<span class="button">'.$item[ $column_name ].'</span>';
			default:
				return print_r( $item, true ); //Show the whole array for troubleshooting purposes
		}
	}

	/** Custom Method for name column */
	function column_name( $item ) {

		$delete_nonce = wp_create_nonce( 'cq_delete_item' );
		$edit_nonce = wp_create_nonce( 'cq_edit_item' );

		$title = '<strong>' . $item['name'] . '</strong>';

		$actions = [
			'edit' => sprintf('<a href="/wp-admin/admin.php?page=coinqvest-edit-payment-button&id=%s">' . __('Edit', 'coinqvest') .'</a>', absint( $item['hashid'] ) ),
			'delete' => sprintf( '<a href="?page=%s&action=%s&item=%s&_wpnonce=%s">' . __('Delete', 'coinqvest') .'</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['hashid'] ), $delete_nonce )
		];

		return $title . $this->row_actions( $actions );
	}

	/** Custom Method for shortcode column */
	function column_shortcode( $item ) {
		$shortcode =  '<input type="text" class="terminal" onfocus="this.select();" value="[COINQVEST_checkout id=&quot;' . $item['hashid']. '&quot;]" readonly="readonly" />';
		return $shortcode;
	}

	/** Custom Method for price column */
	function column_price( $item ) {
		return Common_Helpers::calculate_price($item['json']);
	}

	/** Custom Method for status column */
	function column_status( $item ) {
		return ($item['status'] == 1) ? '<span class="active">active</span>' : '<span class="inactive">inactive</span>';
	}

	/** Associative array of columns */
	function get_columns() {
		$columns = [
			'name'      => __( 'Name', $this->plugin_text_domain ),
			'status'    => __( 'Status', $this->plugin_text_domain ),
			'price'    => __( 'Price', $this->plugin_text_domain ),
			'shortcode' => __( 'Shortcode', $this->plugin_text_domain ),
			'buttontext'    => __( 'Button text', $this->plugin_text_domain ),
			'cssclass'    => __( 'CSS class', $this->plugin_text_domain ),
			'time'    => __( 'Time', $this->plugin_text_domain )
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
			$nonce = esc_attr( $_REQUEST['_wpnonce'] );

			if ( ! wp_verify_nonce( $nonce, 'cq_delete_item' ) ) {
				die( 'Go get a life script kiddies' );
			}
			else {

				// can only be deleted when status is inactive
				global $wpdb;
				$table_name = $wpdb->prefix . 'coinqvest_payment_buttons';
				$row = $wpdb->get_row("SELECT hashid, status FROM ".$table_name." WHERE hashid = " . absint( $_GET['item']));

				if ($row->status == 1) {
					$result = "error";
					$message = __('Cannot be deleted when status is active', 'coinqvest');
					$page = "coinqvest-payment-buttons";
					$this->redirect = new Admin_Helpers();
					$this->redirect->custom_redirect($result, $message, $page);
					exit;
				}

				self::delete_item( absint( $_GET['item'] ) );

				$result = "success";
				$message = __('Button successfully deleted', 'coinqvest');
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
