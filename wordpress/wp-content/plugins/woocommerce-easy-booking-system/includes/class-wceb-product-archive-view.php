<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WCEB_Product_Archive_View' ) ) :

class WCEB_Product_Archive_View {

	public function __construct() {
        add_filter( 'woocommerce_loop_add_to_cart_link', array( $this, 'wceb_custom_loop_add_to_cart' ), 10, 2 );
	}

    /**
    *
    * Adds a custom text link on product archive page
    *
    * @param str $content - Current text
    * @param WC_Product $product
    * @return str $content - Custom or current text
    *
    **/
    public function wceb_custom_loop_add_to_cart( $content, $product ) {

        if ( ! $product ) return;

        // If product is bookable
        if ( wceb_is_bookable( $product ) ) {

            $product_id = is_callable( array( $product, 'get_id' ) ) ? $product->get_id() : $product->id;

            $link    = get_permalink( $product_id );
            $label   = __( 'Select date(s)', 'easy_booking' );
            $content = apply_filters(
                'easy_booking_loop_add_to_cart_link',
                '<a href="' . esc_url( $link ) . '" rel="nofollow" class="button">' . esc_html( $label  ) . '</a>',
                $product
            );
            
        }
        
        return $content;
    }
}

return new WCEB_Product_Archive_View();

endif;