<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
*
* Gets all booked products from the existing orders
*
* @return array $booked - An array of all booked products
*
**/
function wceb_get_booked_items_from_orders() {

	// Query orders
    $args = array(
        'post_type'      => 'shop_order',
        'post_status'    => apply_filters( 
                            'easy_booking_get_order_statuses',
                            array(
                                'wc-pending',
                                'wc-processing',
                                'wc-on-hold',
                                'wc-completed',
                                'wc-refunded'
                            ) ),
        'posts_per_page' => -1
    );

    $query_orders = new WP_Query( $args );

    $products = array();
    foreach ( $query_orders->posts as $post ) :

		$order_id = $post->ID;
		$order    = new WC_Order( $order_id );
		$items    = $order->get_items();

        $data = array();
        if ( $items ) foreach ( $items as $item_id => $item ) {

			$product_id   = $item['product_id'];
			$variation_id = $item['variation_id'];

            if ( is_callable( array( $item, 'get_product' ) ) ) {
                $product = $item->get_product();
            } else {
                $product = $order->get_product_from_item( $item );
            }

            if ( ! $product ) {
            	continue;
            }

            if ( wceb_is_bookable( $product ) ) {

            	// If start date is set
                if ( isset( $item['ebs_start_format'] ) ) {

					$id       = empty( $variation_id ) || $variation_id === '0' ? $product_id : $variation_id;
					$start    = $item['ebs_start_format'];

                    // Check date format to avoid errors (yyyy-mm-dd)
                    if ( ! preg_match( '/^([0-9]{4}\-[0-9]{2}\-[0-9]{2})$/', $start ) ) {
                        continue;
                    }

					$quantity = intval( $item['qty'] );

					// Check if product or variation still exists
					if ( ! wc_get_product( $id ) ) {
						continue;
					}

					// If a refund of the product has been made, get the refunded quantity
                    $refunded_qty = $order->get_qty_refunded_for_item( $item_id );

                    // Removed refunded items
                    if ( $refunded_qty > 0 ) {
                        $quantity = $quantity - $refunded_qty;
                    }

                    // If 0 items are left, return
                    if ( $quantity <= 0 ) {
                        continue;
                    }

                	$data = array(
                		'product_id' => $id,
						'order_id'   => $order_id,
						'start'      => $start,
						'qty'        => $quantity
                    );

                }

                // If end date is set
                if ( isset( $item['ebs_end_format'] ) ) {

                    $end = $item['ebs_end_format'];

                    // Check date format to avoid errors (yyyy-mm-dd)
                    if ( ! preg_match( '/^([0-9]{4}\-[0-9]{2}\-[0-9]{2})$/', $end ) ) {
                        continue;
                    }

                    $data['end'] = $end;

                }

                if ( ! empty( $data ) && isset( $data['product_id'] ) ) {
                    $products[] = apply_filters( 'easy_booking_booked_reports', $data );
                }

            }
        
        }

    endforeach;
    
    // Sort array by product IDs
    usort( $products, 'wceb_sort_by_product_id' );
    
    return $products;

}

/**
*
* Sorts array by product ID
*
* @return bool
*
**/
function wceb_sort_by_product_id( $a, $b ) {
	return ( $a['product_id'] > $b['product_id'] );
}

/**
*
* Minifies CSS on-the-fly
* @param str $css - Not minified CSS
*
* @return str $css - Minified CSS
*
**/
function wceb_minify_css( $css ) {
    // Remove comments
    $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);

    // Remove space after colons
    $css = str_replace(': ', ':', $css);

    // Remove space before brackets
    $css = str_replace(' {', '{', $css);

    // Remove whitespace
    $css = str_replace( array( "\r\n", "\r", "\n", "\t", '  ', '    ', '    ' ), '', $css );

    return $css;
}

/**
*
* Returns the path to a file
* Loads the file from the theme if it exists (path: easy-booking/$path/$file or easy-booking/$file)
* If it doesn't exist in the theme, loads the file from the plugin
*
* @param str $path - Path to the file (relative to the plugin directory)
* @param str $file - File name
* @return str $template - Complete path to the file
*
**/
function wceb_load_template( $path, $file ) {
    $template_path = 'easy-booking/';
    $template = '';

    // Get the file n the template if it exists
    $template = locate_template( 
        array( $template_path . trailingslashit( $path ) . $file,
        trailingslashit( $template_path ) . $file
    ) );

    // If it doesn't, get it from the plugin
    if ( ! $template || empty( $template ) ) {
        $template = plugin_dir_path( WCEB_PLUGIN_FILE ) . trailingslashit( $path ) . $file;
    }

    return $template;
}

/**
*
* Returns the path to a script (minified or not)
*
* @param str $path - Admin or empty
* @param str $file - File name
* @param str $extension - File extension (js or css)
* @param constant - The plugin file (default: Easy Booking)
* @return str path to the file
*
**/
function wceb_get_file_path( $path, $file, $extension, $plugin = WCEB_PLUGIN_FILE ) {
    $path = empty( $path ) ? '' : trailingslashit( $path );

    return plugins_url( 'assets/' . trailingslashit( $extension ) . $path . WCEB_PATH . $file . WCEB_SUFFIX . '.' . $extension, $plugin );
}

/**
*
* Adjusts a given color
* Credits to someone on Stackoverflow for this function
*
* @param str $hex - The color
* @param int $steps
* @return str - New hex color
*
**/
function wceb_adjust_brightness( $hex, $steps ) {
    // Steps should be between -255 and 255. Negative = darker, positive = lighter
    $steps = max( -255, min( 255, $steps ) );

    // Format the hex color string
    $hex = str_replace( '#', '', $hex );
    if ( strlen( $hex ) == 3) {
        $hex = str_repeat( substr( $hex, 0, 1 ), 2 ) . str_repeat( substr( $hex, 1, 1 ), 2 ) . str_repeat( substr( $hex, 2, 1 ), 2 );
    }

    // Get decimal values
    $r = hexdec( substr( $hex, 0, 2 ) );
    $g = hexdec( substr( $hex, 2, 2 ) );
    $b = hexdec( substr( $hex, 4, 2 ) );

    // Adjust number of steps and keep it inside 0 to 255
    $r = max( 0, min( 255, $r + $steps ) );
    $g = max( 0, min( 255, $g + $steps ) );  
    $b = max( 0, min( 255, $b + $steps ) );

    $r_hex = str_pad( dechex( $r ), 2, '0', STR_PAD_LEFT );
    $g_hex = str_pad( dechex( $g ), 2, '0', STR_PAD_LEFT );
    $b_hex = str_pad( dechex( $b ), 2, '0', STR_PAD_LEFT );

    return '#' . $r_hex . $g_hex . $b_hex;
}