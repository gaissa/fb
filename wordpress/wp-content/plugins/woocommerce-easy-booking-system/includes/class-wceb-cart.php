<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! class_exists( 'WCEB_Cart' ) ) :

class WCEB_Cart {

    public function __construct() {

        // get plugin options values
        $this->options = get_option('easy_booking_settings');
        
        add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'wceb_check_dates_before_add_to_cart' ), 20, 5 );
        add_filter( 'woocommerce_add_cart_item_data', array( $this, 'wceb_add_cart_item_data' ), 10, 3 );
        add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'wceb_get_cart_item_from_session' ), 98, 2 );
        add_filter( 'woocommerce_get_item_data', array( $this, 'wceb_get_item_data' ), 10, 2 );
        add_filter( 'woocommerce_add_cart_item', array( $this, 'wceb_add_cart_item_booking_price' ), 10, 1 );
        
        if ( version_compare( WC_VERSION, '2.7', '<' ) ) {
            add_filter( 'woocommerce_get_price', array( $this, 'wceb_set_cart_item_price' ), 20, 2 );
        } else {
            add_filter( 'woocommerce_product_get_price', array( $this, 'wceb_set_cart_item_price' ), 20, 2 );
        }
        
    }

    /**
    *
    * Checks if two dates are set before adding to cart
    *
    * @param bool $passed
    * @param int $product_id
    * @param int $quantity
    * @param int | optional $variation_id
    * @return bool $passed
    *
    **/
    public function wceb_check_dates_before_add_to_cart( $passed = true, $product_id, $quantity, $variation_id = '', $cart_item_data = array() ) {
        
        $id = empty( $variation_id ) ? $product_id : $variation_id;

        if ( ! $passed ) {
            return $passed;
        }

        $product = wc_get_product( $id );

        if ( ! $product ) {
            return false;
        }
        
        // If product is bookable
        if ( wceb_is_bookable( $product ) ) {

            $booking_session = WC()->session->get( 'booking' );
            $dates_format = wceb_get_product_booking_dates( $product );

            if ( isset( $booking_session[$id] ) && ! empty( $booking_session[$id] ) ) {

                // If product is grouped or bundled, get the "parent" product data
                if ( isset( $booking_session[$id]['grouped_by'] ) ) {
                    $_product = $booking_session[$id]['grouped_by'];
                    $dates_format = wceb_get_product_booking_dates( $_product );
                }

                if ( $dates_format === 'one' ) {

                    if ( ! isset( $booking_session[$id]['start_date'] ) ) {
                        wc_add_notice( __( 'Please choose a date', 'easy_booking' ), 'error' );
                        $passed = false;
                    }

                    if ( isset( $booking_session[$id]['end_date'] ) ) {
                        wc_add_notice( __( 'You can only select one date', 'easy_booking' ), 'error' );
                        $passed = false;
                    }

                } else if ( $dates_format === 'two' ) {
            
                    if ( ! isset( $booking_session[$id]['start_date'] ) || ! isset( $booking_session[$id]['end_date'] ) ) {
                        wc_add_notice( __( 'Please choose two dates', 'easy_booking' ), 'error' );
                        $passed = false;
                    }

                }

            } else {

                if ( $dates_format === 'one' ) {
                    wc_add_notice( __( 'Please choose a date', 'easy_booking' ), 'error' );
                } else if ( $dates_format === 'two' ) {
                    wc_add_notice( __( 'Please choose two dates', 'easy_booking' ), 'error' );
                }
                
                $passed = false;

            }

        }

        return $passed;
    }

    /**
    *
    * Adds session data to cart item
    *
    * @param array $cart_item_meta
    * @param int $product_id
    * @return array $cart_item_meta
    *
    **/
    function wceb_add_cart_item_data( $cart_item_meta, $product_id, $variation_id ) {
        // Get session
        $booking_session = WC()->session->get( 'booking' );
        $id              = empty( $variation_id ) ? $product_id : $variation_id;

        if ( isset( $booking_session[$id] ) && ! empty( $booking_session[$id] ) ) {

            if ( isset( $booking_session[$id]['new_price'] ) ) {
                $cart_item_meta['_booking_price'] = wc_format_decimal( $booking_session[$id]['new_price'] );
            }

            if ( isset( $booking_session[$id]['duration'] ) ) {
                $cart_item_meta['_booking_duration'] = absint( $booking_session[$id]['duration'] );
            }

            if ( isset( $booking_session[$id]['start_date'] ) ) {
                $cart_item_meta['_start_date'] = sanitize_text_field( $booking_session[$id]['start_date'] ); // Formatted dates
            }

            if ( isset( $booking_session[$id]['end_date'] ) ) {
                $cart_item_meta['_end_date'] = sanitize_text_field( $booking_session[$id]['end_date'] ); // Formatted dates
            }

            if ( isset( $booking_session[$id]['start'] ) ) {
                $cart_item_meta['_ebs_start'] = sanitize_text_field( $booking_session[$id]['start'] );
            }

            if ( isset( $booking_session[$id]['end'] ) ) {
                $cart_item_meta['_ebs_end'] = sanitize_text_field( $booking_session[$id]['end'] );
            }

            // Reset session for this product ID
            unset( $booking_session[$id] );
            WC()->session->set( 'booking', $booking_session );

        }

        return $cart_item_meta;
    }

    /**
    *
    * Adds data to cart item
    *
    * @param array $cart_item
    * @param array $values - cart_item_meta
    * @return array $cart_item
    *
    **/
    function wceb_get_cart_item_from_session( $cart_item, $values ) {

        if ( isset( $values['_booking_price'] ) ) {
            $cart_item['_booking_price'] = $values['_booking_price'];
        }

        if ( isset( $values['_booking_duration'] ) ) {
            $cart_item['_booking_duration'] = $values['_booking_duration'];
        }

        if ( isset( $values['_start_date'] ) ) {
            $cart_item['_start_date'] = $values['_start_date'];
        }

        if ( isset( $values['_end_date'] ) ) {
            $cart_item['_end_date'] = $values['_end_date'];
        }

        if ( isset( $values['_ebs_start'] ) ) {
            $cart_item['_ebs_start'] = $values['_ebs_start'];
        }

        if ( isset( $values['_ebs_end'] ) ) {
            $cart_item['_ebs_end'] = $values['_ebs_end'];
        }

        $this->wceb_add_cart_item_booking_price( $cart_item );
        
        return $cart_item;
    }

    /**
    *
    * Override any filters on the price with the booking price once the item is in the cart
    *
    * @param str $price
    * @param WC_Product $_product
    * @return str $price
    *
    **/
    function wceb_set_cart_item_price( $price, $_product ) {
        
        if ( isset( $_product->new_booking_price ) && ! empty( $_product->new_booking_price ) ) {
            $price = $_product->new_booking_price;
        }

        return $price;

    }

    /**
    *
    * Sets custom price to the cart item
    *
    * @param array $cart_item
    * @return array $cart_item
    *
    **/
    function wceb_add_cart_item_booking_price( $cart_item ) {

        if ( isset( $cart_item['_booking_price'] ) && $cart_item['_booking_price'] >= 0 ) {

            $booking_price = apply_filters(
                'easy_booking_set_booking_price',
                $cart_item['_booking_price'],
                $cart_item
            );

            // If bundled
            if ( isset( $cart_item['bundled_by'] ) ) {
                
                // Get parent bundle product
                $bundle = WC()->cart->get_cart_item( $cart_item['bundled_by'] );

                // Get bundle item
                $bundle_item = $bundle['data']->get_bundled_item( $cart_item['bundled_item_id'] );

                // If is not priced individually, remove booking price
                if ( ! $bundle_item->is_priced_individually() ) {
                    $cart_item['data']->new_booking_price = '';
                    return $cart_item;
                }

            }

            $cart_item['data']->set_price( (float) $booking_price );
            $cart_item['data']->new_booking_price = (float) $booking_price;

        }
    
        return $cart_item;
    }
 
    /**
    *
    * Adds formatted dates to the cart item
    *
    * @param array $other_data
    * @param array $cart_item
    * @return array $other_data
    *
    **/
    function wceb_get_item_data( $other_data, $cart_item ) {

        // If is bundled, return
        if ( isset( $cart_item['bundled_by'] ) ) {
            return $other_data;
        }

        $product_id   = $cart_item['product_id'];
        $variation_id = $cart_item['variation_id'];

        $id = empty( $variation_id ) ? $product_id : $variation_id;

        $product = wc_get_product( $id );

        $start_text = esc_html( apply_filters( 'easy_booking_start_text', __( 'Start', 'easy_booking' ), $product ) );
        $end_text   = esc_html( apply_filters( 'easy_booking_end_text', __( 'End', 'easy_booking' ), $product ) );

        if ( isset( $cart_item['_start_date'] ) && ! empty ( $cart_item['_start_date'] ) ) {

            $other_data[] = array(
                'name'  => $start_text,
                'value' => $cart_item['_start_date']
            );

        }

        if ( isset( $cart_item['_end_date'] ) && ! empty ( $cart_item['_end_date'] ) ) {

            $other_data[] = array(
                'name'  => $end_text,
                'value' => $cart_item['_end_date']
            );

        }

        return $other_data;
    }

}

new WCEB_Cart();

endif;