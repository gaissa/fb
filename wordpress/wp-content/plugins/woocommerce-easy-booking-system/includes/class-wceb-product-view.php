<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WCEB_Product_View' ) ) :

class WCEB_Product_View {

	public function __construct() {
        // Get plugin options values
        $this->options = get_option('easy_booking_settings');
        
        add_filter( 'woocommerce_available_variation', array( $this, 'wceb_add_variation_bookable_attribute' ), 10, 3 );
		add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'wceb_before_add_to_cart_button' ), 20 );
        add_action( 'woocommerce_before_variations_form', array( $this, 'wceb_display_for_variable_product' ), 10 );
        add_filter( 'woocommerce_show_variation_price', array( $this, 'wceb_variation_price_html' ), 10, 3 );
        add_filter( 'woocommerce_get_price_html', array( $this, 'wceb_add_price_html' ), 10, 2 );
	}

    /**
    *
    * Adds is_bookable attribute to bookable variations.
    *
    * @return array $available_variations
    *
    **/
    public function wceb_add_variation_bookable_attribute( $available_variations, $product, $variation ) {
        $available_variations['is_bookable'] = wceb_is_bookable( $variation );
        $available_variations['has_dates'] = wceb_get_product_booking_dates( $variation );
        $available_variations['info_text'] = apply_filters( 'easy_booking_information_text', '', $variation );
        return $available_variations;
    }

    /**
    *
    * Adds a custom form to the product page.
    *
    **/
    public function wceb_before_add_to_cart_button() {
        global $post, $product;
        
        // Product is bookable
        if ( wceb_is_bookable( $product ) ) {

            $start_date_text = apply_filters( 'easy_booking_start_text', __( 'Start', 'easy_booking' ), $product );
            $end_date_text   = apply_filters( 'easy_booking_end_text', __( 'End', 'easy_booking' ), $product );
            
            $tax_display_mode = get_option( 'woocommerce_tax_display_shop' );

            // Product price (Regular or sale)
            if ( $tax_display_mode === 'incl' ) {

                $product_price = function_exists( 'wc_get_price_including_tax' ) ? wc_get_price_including_tax( $product ) : $product->get_price_including_tax();

            } else {

                $product_price =  function_exists( 'wc_get_price_excluding_tax' ) ? wc_get_price_excluding_tax( $product ) : $product->get_price_excluding_tax();

            }
            
            $args = apply_filters( 'easy_booking_new_price_args', array() );
            
            $dates = wceb_get_product_booking_dates( $product );
            
            $template = wceb_load_template( 'includes/views', 'wceb-html-product-view.php' );

            include_once( $template );
           
        }
    }

    /**
    *
    * Adds a custom form to the variable product page. Credit to woocommerce product addons for the tweak.
    *
    **/
    public function wceb_display_for_variable_product() {
        remove_action( 'woocommerce_before_add_to_cart_button', array( $this, 'wceb_before_add_to_cart_button' ), 20 );
        add_action( 'woocommerce_single_variation', array( $this, 'wceb_before_add_to_cart_button' ), 18 );        
    }

    /**
    *
    * Hide variation price on the product page if variation is bookable
    *
    * @param bool display
    * @param WC_Product $product
    * @param WC_Product_Variation $variation
    * @return bool $display
    *
    **/
    public function wceb_variation_price_html( $display, $product, $variation ) {

        if ( wceb_is_bookable( $variation ) ) {
            $display = false;
        }

        return $display;
    }

    /**
    *
    * Displays a custom price if the product is bookable on the product page
    *
    * @param str $content - Product price
    * @return str $content - Custom or base price
    *
    **/
    public function wceb_add_price_html( $content, $product ) {

        if ( ! $product ) return;

        // If bookable, return a price / day. If not, return normal price
        if ( wceb_is_bookable( $product ) ) {

            $price_text = wceb_get_price_html( $product );
            
            $content = apply_filters(
                'easy_booking_display_price',
                $content . '<span class="wceb-price-format">' . $price_text . '</span>',
                $product
            );
        }
        
        return $content;

    }
}

return new WCEB_Product_View();

endif;