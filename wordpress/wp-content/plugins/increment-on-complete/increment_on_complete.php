<?php
/**
 * @package Increment_on_Complete
 * @version 0.1
 */
/*
Plugin Name: Increment on Complete
Plugin URI: https://github.com/gaissa
Description: A plugin for WooCommerce which increments a product stock back to one (1) when an order is completed. Perfect for rental and booking systems.
Author: Janne Kähkonen
Version: 0.1
Author URI: https://github.com/gaissa
*/

add_action( 'woocommerce_order_status_completed', 'edit_quantity' );

function edit_quantity( $order_id ) {

    // Only continue if have $order_id
    if ( ! $order_id ) {
        return;
    }

    // Get order
    $order = wc_get_order( $order_id );
    $items = $order->get_items();
    
    foreach ( $items as $item ) {
        $product_id = $item[ 'product_id' ];
        #$old_stock = wc_get_product( $product_id )->get_stock_quantity();
        wc_get_product( $product_id )->set_stock_quantity( 1 );
        
        ## MAKE SO THAT THE STOCK IS DECREMENTENT ON OTHER BUTTON ACTIONS
        #$old_stock = wc_get_product( $product_id )->get_stock_quantity();
        #wc_get_product( $product_id )->set_stock_quantity( $old_Stock + 1 );
    }
    #die(); ##DEBUG WITH FORCE
}

?>
