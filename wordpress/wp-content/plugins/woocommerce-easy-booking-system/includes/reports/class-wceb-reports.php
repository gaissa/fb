<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WCEB_Reports {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'easy_booking_add_reports_pages' ), 10 );
	}

	/**
	 * Add reports page
	 *
	 */
	public function easy_booking_add_reports_pages() {
		// Add reports page under the "Easy Booking" menu
		$reports_page = add_submenu_page(
			'easy-booking',
			__('Reports', 'easy_booking'),
			__('Reports', 'easy_booking'),
			apply_filters( 'easy_booking_settings_capability', 'manage_options' ),
			'easy-booking-reports',
			array( $this, 'easy_booking_reports_page' )
		);

		// Load scripts on this page only
		add_action( 'admin_print_scripts-'. $reports_page, array( $this, 'easy_booking_load_admin_reports_scripts' ) );

		// Output page content
		add_action( 'easy_booking_reports_bookings', array( $this, 'easy_booking_reports_content' ) );
	}

	/**
	 * Load scripts on the reports page
	 *
	 */
	public function easy_booking_load_admin_reports_scripts() {
		
		// WooCommerce scripts
		wp_enqueue_script( 'select2' );
		wp_enqueue_script( 'wc-enhanced-select' );
		wp_enqueue_script( 'jquery-tiptip' );
		wp_enqueue_script( 'wc-admin-meta-boxes' );

		// WooCommerce styles
		wp_enqueue_style( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css', array(), WC_VERSION );

		// Easy Booking scripts
		wp_enqueue_script( 'pickadate' );

		wp_enqueue_script(
			'easy_booking_reports',
			wceb_get_file_path( 'admin', 'wceb-reports-functions', 'js' ),
			array( 'jquery' ),
			'1.0',
			true
		);

		wp_enqueue_script( 'datepicker.language' );

		// Easy Booking styles
		wp_enqueue_style(
			'easy_booking_reports_styles',
			wceb_get_file_path( 'admin', 'wceb-reports', 'css' ),
			array(),
			1.0
		);

		wp_enqueue_style( 'picker' );

		// Action hook to load extra scripts on the reports page
		do_action( 'easy_booking_load_report_scripts' );

	}

	/**
	 * Reports page
	 *
	 */
	public function easy_booking_reports_page() {
		include_once( 'views/html-wceb-reports.php' );
	}

	/**
	 * Reports page content
	 *
	 */
	public function easy_booking_reports_content() {
		$wceb_report_list = new Easy_Booking_List_Reports();
		$wceb_report_list->prepare_items();
		$wceb_report_list->display();
	}
}

return new WCEB_Reports();