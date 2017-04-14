<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Easy_Booking_List_Reports extends WP_List_Table {

	protected $max_items;

	public function __construct() {

		parent::__construct( array(
			'singular'  => __( 'Report', 'woocommerce' ),
			'plural'    => __( 'Reports', 'woocommerce' ),
			'ajax'		=> true
		) );

	}

	/**
	 * Display filters and pagination
	 *
	 */
	protected function display_tablenav( $which ) {

		$filter_id         = isset( $_GET['wceb_report_product_ids'] ) ? stripslashes( $_GET['wceb_report_product_ids'] ) : '';
		$filter_start_date = isset( $_GET['wceb_report_start_date_submit'] ) ? stripslashes( $_GET['wceb_report_start_date_submit'] ) : '';
		$filter_end_date   = isset( $_GET['wceb_report_end_date_submit'] ) ? stripslashes( $_GET['wceb_report_end_date_submit'] ) : '';
		
		if ( ! empty( $filter_id ) ) {
			$_product = wc_get_product( $filter_id );
		}

		$product = isset( $_product ) && is_object( $_product ) ? $_product->get_formatted_name() : '';

		include_once( 'views/html-wceb-reports-filters.php' );
	}

	/**
	 * Set reports columns
	 *
	 */
	public function get_columns() {

		$columns = apply_filters( 'easy_booking_reports_columns', array(
			'order_status' => '<span class="status_head tips" data-tip="' . esc_attr__( 'Status', 'woocommerce' ) . '">' . esc_attr__( 'Status', 'woocommerce' ) . '</span>',
			'order_id'     => __( 'Order', 'woocommerce' ),
			'product'      => __( 'Product', 'woocommerce' ),
			'start_date'   => esc_html( apply_filters( 'easy_booking_start_text', __( 'Start', 'easy_booking' ) ) ),
			'end_date'     => esc_html( apply_filters( 'easy_booking_end_text', __( 'End', 'easy_booking' ) ) ),
			'qty_booked'   => __( 'Quantity booked', 'easy_booking' )
		) );

		return $columns;
	}

	/**
	 * Set reports sortable columns
	 *
	 */
	protected function get_sortable_columns() {

		$sortable_columns = array(
			'order_id'   => array( 'order_id', true ),
			'product'    => array( 'product_id', true ),
			'start_date' => array( 'start_date', false ),
			'end_date'   => array( 'end_date', false )
		);

		return $sortable_columns;
	}

	/**
	 * Reports columns content
	 *
	 */
	public function column_default( $item, $column_name ) {
		global $post;

		if ( ! empty( $item['order_id'] ) ) {
			$order = wc_get_order( $item['order_id'] );
		}

		if ( ! $order ) {
			return;
		}

		$product = wc_get_product( $item['product_id'] );

		if ( ! $product ) {
			return;
		}

		switch ( $column_name ) {

			case 'order_status' :
				printf( '<mark class="%s tips" data-tip="%s">%s</mark>', esc_attr( sanitize_html_class( $order->get_status() ) ), esc_attr( wc_get_order_status_name( $order->get_status() ) ), esc_html( wc_get_order_status_name( $order->get_status() ) ) );
			break;

			case 'order_id' :

				if ( version_compare( WC_VERSION, '2.7', '<' ) ) {

					$customer_tip = array();

					if ( $address = $order->get_formatted_billing_address() ) {
						$customer_tip[] = __( 'Billing:', 'woocommerce' ) . ' ' . $address . '<br/><br/>';
					}

					if ( $order->billing_phone ) {
						$customer_tip[] = __( 'Tel:', 'woocommerce' ) . ' ' . $order->billing_phone;
					}

					echo '<div class="tips" data-tip="' . wc_sanitize_tooltip( implode( "<br/>", $customer_tip ) ) . '">';

					if ( $order->user_id ) {
						$user_info = get_userdata( $order->user_id );
					}

					if ( ! empty( $user_info ) ) {

						$username = '<a href="user-edit.php?user_id=' . absint( $user_info->ID ) . '">';

						if ( $user_info->first_name || $user_info->last_name ) {
							$username .= esc_html( ucfirst( $user_info->first_name ) . ' ' . ucfirst( $user_info->last_name ) );
						} else {
							$username .= esc_html( ucfirst( $user_info->display_name ) );
						}

						$username .= '</a>';

					} else {
						if ( $order->billing_first_name || $order->billing_last_name ) {
							$username = trim( $order->billing_first_name . ' ' . $order->billing_last_name );
						} else {
							$username = __( 'Guest', 'woocommerce' );
						}
					}

					printf( _x( '%s by %s', 'Order number by X', 'woocommerce' ), '<a href="' . admin_url( 'post.php?post=' . absint( $item['order_id'] ) . '&action=edit' ) . '"><strong>#' . esc_attr( $order->get_order_number() ) . '</strong></a>', $username );

					if ( $order->billing_email ) {
						echo '<small class="meta email"><a href="' . esc_url( 'mailto:' . $order->billing_email ) . '">' . esc_html( $order->billing_email ) . '</a></small>';
					}

					echo '</div>';
					
				} else {

					if ( $order->get_customer_id() ) {
						$user     = get_user_by( 'id', $order->get_customer_id() );
						$username = '<a href="user-edit.php?user_id=' . absint( $order->get_customer_id() ) . '">';
						$username .= esc_html( ucwords( $user->display_name ) );
						$username .= '</a>';
					} elseif ( $order->get_billing_first_name() || $order->get_billing_last_name() ) {
						/* translators: 1: first name 2: last name */
						$username = trim( sprintf( _x( '%1$s %2$s', 'full name', 'woocommerce' ), $order->get_billing_first_name(), $order->get_billing_last_name() ) );
					} elseif ( $order->get_billing_company() ) {
						$username = trim( $order->get_billing_company() );
					} else {
						$username = __( 'Guest', 'woocommerce' );
					}

					/* translators: 1: order and number (i.e. Order #13) 2: user name */
					printf(
						__( '%1$s by %2$s', 'woocommerce' ),
						'<a href="' . admin_url( 'post.php?post=' . absint( $order->get_id() ) . '&action=edit' ) . '" class="row-title"><strong>#' . esc_attr( $order->get_order_number() ) . '</strong></a>',
						$username
					);

					if ( $order->get_billing_email() ) {
						echo '<small class="meta email"><a href="' . esc_url( 'mailto:' . $order->get_billing_email() ) . '">' . esc_html( $order->get_billing_email() ) . '</a></small>';
					}

					echo '<button type="button" class="toggle-row"><span class="screen-reader-text">' . __( 'Show more details', 'woocommerce' ) . '</span></button>';

				}

			break;

			case 'product' :
				$product_name = $product->get_formatted_name();
				echo $product_name;
			break;

			case 'start_date' :
				echo '<input type="text" data-value="' . esc_attr( $item['start'] ) . '" disabled="true" class="datepicker datepicker_start">';
			break;

			case 'end_date' :

				if ( isset( $item['end'] ) ) {
					echo '<input type="text" data-value="' . esc_attr( $item['end'] ) . '" disabled="true" class="datepicker datepicker_end">';
				}

			break;

			case 'qty_booked' :
				echo esc_html( $item['qty'] );
			break;

		}
		
	}

	/**
	 * Sort reports
	 *
	 */
	function usort_reorder( $a, $b ) {

		// If no sort, default to product ID
		$orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'product_id';

		// If no order, default to asc
		$order = ( ! empty($_GET['order'] ) ) ? $_GET['order'] : 'asc';

		// Determine sort order
		switch ( $orderby ) {

			case 'order_id' :
				$result = ( $a['order_id'] > $b['order_id'] ) ? -1 : 1;
			break;

			case 'product_id' :
				$result = ( $a['product_id'] > $b['product_id'] ) ? -1 : 1;
			break;

			case 'start_date' :
				$a_start_date = strtotime( $a['start'] );
				$b_start_date = strtotime( $b['start'] );

				$result = ( $a_start_date > $b_start_date ) ? -1 : 1;
			break;

			case 'end_date' :
				$a_end_date = strtotime( $a['end'] );
				$b_end_date = strtotime( $b['end'] );

				$result = ( $a_end_date > $b_end_date ) ? -1 : 1;
			break;

		}
		
		// Send final sort direction to usort
		return ( $order === 'asc' ) ? $result : -$result;
	}

	/**
	 * Prepare items
	 *
	 */
	public function prepare_items() {

		$this->_column_headers = array( $this->get_columns(), array(), $this->get_sortable_columns() );
		$current_page          = absint( $this->get_pagenum() );
		$per_page              = 20;

		$this->get_items( $current_page, $per_page );

		/**
		 * Pagination
		 */
		$this->set_pagination_args( array(
			'total_items' => $this->max_items,
			'per_page'    => $per_page,
			'total_pages' => ceil( $this->max_items / $per_page )
		) );
	}

	/**
	 * Get items
	 *
	 */
	public function get_items( $current_page, $per_page ) {
		global $wpdb;

		$this->max_items = 0;
		$this->items     = array();

		// Get all booked items from orders
		$booked_products = wceb_get_booked_items_from_orders();

		$filter_id         = isset( $_GET['wceb_report_product_ids'] ) ? stripslashes( $_GET['wceb_report_product_ids'] ) : '';
		$filter_start_date = isset( $_GET['wceb_report_start_date_submit'] ) ? stripslashes( $_GET['wceb_report_start_date_submit'] ) : '';
		$filter_end_date   = isset( $_GET['wceb_report_end_date_submit'] ) ? stripslashes( $_GET['wceb_report_end_date_submit'] ) : '';

		$filters = array(
			'start' => $filter_start_date,
			'end'   => $filter_end_date
		);

		// If is filtered by ID
		if ( ! empty( $filter_id ) ) {

			foreach ( $booked_products as $index => $booked_date ) {

				if ( $booked_date['product_id'] != $filter_id ) {
					unset( $booked_products[$index] ); // Remove unfiltered IDs
					continue;
				}

			}

		}

		// If is filtered by start and end date
		if ( ! empty( $filter_start_date ) && ! empty( $filter_end_date ) ) {

			foreach ( $booked_products as $index => $booked_date ) {

				$start = strtotime( $booked_date['start'] );
				$end   = isset( $booked_date['end'] ) ? strtotime( $booked_date['end'] ) : $start;

				$start_filter = strtotime( $filter_start_date );
				$end_filter   = strtotime( $filter_end_date );

				if ( $start < $start_filter || $end > $end_filter ) {
					unset( $booked_products[$index] );
					continue;
				}
			}

		} else { // If is filter by one date only

			foreach ( $filters as $filter => $filtered ) {

				if ( ! empty( $filtered ) ) {

					foreach ( $booked_products as $index => $booked_date ) {

						if ( $booked_date[$filter] != $filtered ) {
							unset( $booked_products[$index] );
							continue;
						}

					}

				}

			}

		}

		// Sort results
		usort( $booked_products, array( $this, 'usort_reorder' ) );

		$total_items = count( $booked_products );
		$min         = ( $current_page - 1 ) * $per_page;
		$max         = $min + $per_page;

		$set_max = $total_items < $max ? $total_items : $max;

		$items = array();
		for ( $i = $min; $i < $set_max; $i++ ) {
			$items[] = $booked_products[$i];
		}

		$this->items     = $items;
		$this->max_items = $total_items;
	}

}