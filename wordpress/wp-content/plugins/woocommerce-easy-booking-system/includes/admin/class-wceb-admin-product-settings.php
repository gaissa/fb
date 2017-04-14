<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WCEB_Product_Settings' ) ) :

class WCEB_Product_Settings {

    private $wceb_settings;

	public function __construct() {

        // get plugin options values
        $this->wceb_settings = get_option( 'easy_booking_settings' );

		add_action( 'product_type_options', array( $this, 'easy_booking_add_product_option_pricing' ) );
        add_action( 'woocommerce_variation_options', array( $this, 'easy_booking_set_variation_booking_option' ), 10, 3 );
        add_action( 'woocommerce_product_after_variable_attributes', array( $this, 'easy_booking_add_variation_booking_options' ), 10, 3 );
        add_filter( 'woocommerce_product_data_tabs', array( $this, 'easy_booking_add_booking_tab' ), 10, 1 );
        add_action( 'woocommerce_product_data_panels', array( $this, 'easy_booking_add_booking_data_panel' ) );

        if ( WCEB()->allowed_types ) foreach ( WCEB()->allowed_types as $type ) {
            add_action( 'woocommerce_process_product_meta_' . $type, array( $this, 'easy_booking_save_booking_options' ) );
        }

        add_action( 'woocommerce_save_product_variation', array( $this, 'easy_booking_save_variation_booking_options' ), 10, 2 );
	}

    /**
    *
    * Adds a checkbox to the product admin page to set the product as bookable
    *
    * @param array $product_type_options
    * @return array $product_type_options
    *
    **/
    public function easy_booking_add_product_option_pricing( $product_type_options ) {
        global $post;

        $is_bookable = get_post_meta( $post->ID, '_booking_option', true );

        $show = array();
        if ( WCEB()->allowed_types ) foreach ( WCEB()->allowed_types as $type ) {
            $show[] = 'show_if_' . $type;
        }

        $product_type_options['booking_option'] = array(
            'id'            => '_booking_option',
            'wrapper_class' => implode( ' ', $show ),
            'label'         => __( 'Bookable', 'easy_booking' ),
            'description'   => __( 'Bookable products can be rent or booked on a daily/weekly/custom schedule', 'easy_booking' ),
            'default'       => $is_bookable === 'yes' ? 'yes' : 'no'
        );

        return $product_type_options;
    }

    /**
    *
    * Adds a checkbox to the product variation to set it as bookable
    *
    * @param int $loop
    * @param array $variation_data
    * @param obj $variation
    *
    **/
    public function easy_booking_set_variation_booking_option( $loop, $variation_data, $variation ) {

        $variation_id = is_callable( array( $variation, 'get_id' ) ) ? $variation->get_id() : $variation->ID;
        $is_bookable = get_post_meta( $variation_id, '_booking_option', true );

        ?>
        
            <label class="show_if_bookable">
                <input type="checkbox" class="checkbox variable_is_bookable" value="1" name="_var_booking_option[<?php echo $loop; ?>]" <?php checked( $is_bookable, 'yes' ) ?> />
                <?php _e( 'Bookable', 'woocommerce' ); ?>
            </label>
        
        <?php
    }

    /**
    *
    * Displays booking options for variations
    *
    * @param int $loop
    * @param array $variation_data
    * @param obj $variation
    *
    **/
    public function easy_booking_add_variation_booking_options( $loop, $variation_data, $variation ) {
        $variation_id = is_callable( array( $variation, 'get_id' ) ) ? $variation->get_id() : $variation->ID;
        include('views/wceb-html-variation-booking-options.php');
    }

    /**
    *
    * Adds a booking tab to the product admin page for booking options
    *
    * @param array $product_data_tabs
    * @return array $product_data_tabs
    *
    **/
    public function easy_booking_add_booking_tab( $product_data_tabs ) {
        $product_data_tabs['WCEB'] = array(
                'label'  => __( 'Bookings', 'easy_booking' ),
                'target' => 'booking_product_data',
                'class'  => array( 'show_if_bookable' ),
        );

        return $product_data_tabs;
    }

    /**
    *
    * Adds booking options in the booking tab
    *
    **/
    public function easy_booking_add_booking_data_panel() {
        global $post;

        $product = wc_get_product( $post->ID );
        include('views/wceb-html-product-booking-options.php');
    }

    /**
    *
    * Saves checkbox value and booking options for the product
    *
    * @param int $post_id
    *
    **/
    public function easy_booking_save_booking_options( $post_id ) {
        $booking_data = array(
            'bookable'                => isset( $_POST['_booking_option'] ) ? 'yes' : false,
            'dates'                   => isset( $_POST['_booking_dates'] ) ? $_POST['_booking_dates'] : '',
            'booking_min'             => isset( $_POST['_booking_min'] ) ? $_POST['_booking_min'] : '',
            'booking_max'             => isset( $_POST['_booking_max'] ) ? $_POST['_booking_max'] : '',
            'first_available_date'    => isset( $_POST['_first_available_date'] ) ? $_POST['_first_available_date'] : '',
            'booking_duration'        => isset( $_POST['_booking_duration'] ) ? $_POST['_booking_duration'] : '',
            'custom_booking_duration' => isset( $_POST['_custom_booking_duration'] ) ? $_POST['_custom_booking_duration'] : ''
        );

        $this->easy_booking_save_booking_data( $post_id, $booking_data );
    }

    /**
    *
    * Saves checkbox value and booking options for the variation
    *
    * @param int $variation_id
    * @param int $i - The loop
    *
    **/
    public function easy_booking_save_variation_booking_options( $variation_id , $i ) {
        
        $booking_data = array(
            'bookable'                => isset( $_POST['_var_booking_option'][$i] ) ? 'yes' : false,
            'dates'                   => isset( $_POST['_var_booking_dates'][$i] ) ? $_POST['_var_booking_dates'][$i] : '',
            'booking_min'             => isset( $_POST['_var_booking_min'][$i] ) ? $_POST['_var_booking_min'][$i] : '',
            'booking_max'             => isset( $_POST['_var_booking_max'][$i] ) ? $_POST['_var_booking_max'][$i] : '',
            'first_available_date'    => isset( $_POST['_var_first_available_date'][$i] ) ? $_POST['_var_first_available_date'][$i] : '',
            'booking_duration'        => isset( $_POST['_var_booking_duration'][$i] ) ? $_POST['_var_booking_duration'][$i] : '',
            'custom_booking_duration' => isset( $_POST['_var_custom_booking_duration'][$i] ) ? $_POST['_var_custom_booking_duration'][$i] : ''
        );

        $this->easy_booking_save_booking_data( $variation_id, $booking_data );

    }

    /**
    *
    * Sanitizes and saves booking data
    *
    * @param int $post_id
    * @param array $booking_data
    *
    **/
    public function easy_booking_save_booking_data( $id, array $booking_data ) {
        $all_bookable = $this->wceb_settings['easy_booking_all_bookable'];
        $is_bookable = isset( $booking_data['bookable'] ) ? esc_html( $booking_data['bookable'] ) : false;

        if ( ! empty( $all_bookable ) && $all_bookable === 'on' ) {
            $is_bookable = 'yes';
        }

        $data = array(
            'booking_min'          => $booking_data['booking_min'],
            'booking_max'          => $booking_data['booking_max'],
            'first_available_date' => $booking_data['first_available_date']
        );

        foreach ( $data as $name => $value ) {
            
            switch ( $value ) {
                case '' :
                    ${$name} = '';
                break;

                case 0 :
                    ${$name} = '0';
                break;

                default :
                    ${$name} = absint( $value );
                break;
            }
        }

        if ( $booking_min != 0 && $booking_max != 0 && $booking_min > $booking_max ) {
            WC_Admin_Meta_Boxes::add_error( __( 'Minimum booking duration must be inferior to maximum booking duration', 'easy_booking' ) );
        } else {
            update_post_meta( $id, '_booking_min', $booking_min );
            update_post_meta( $id, '_booking_max', $booking_max );
        }

        $booking_duration = sanitize_text_field( $booking_data['booking_duration'] );

        $duration = 1;
        switch ( $booking_duration ) {
            case 'global' :
                $duration = '';
            break;
            case 'parent' :
                $duration = '';
            break;
            case 'days' :
                $duration = 1;
            break;
            case 'weeks' :
                $duration = 7;
            break;
            case 'custom' :

                if ( ! empty( $booking_data['custom_booking_duration'] ) ) {

                    $duration = absint( $booking_data['custom_booking_duration'] );

                    if ( $duration <= 0 ) {
                        $duration = 1;
                    }

                } else {
                    $duration = '';
                }

            break;
            default :
                $duration = 1;
            break;
        }

        $dates = 'two';

        if ( ! empty( $booking_data['dates'] )
            && ( $booking_data['dates'] === 'one'
            || $booking_data['dates'] === 'two'
            || $booking_data['dates'] === 'parent'
            || $booking_data['dates'] === 'global' ) ) {

            $dates = sanitize_text_field( $booking_data['dates'] );

        }
        
        update_post_meta( $id, '_booking_dates', $dates );
        update_post_meta( $id, '_booking_duration', $booking_duration );
        update_post_meta( $id, '_custom_booking_duration', $duration );
        update_post_meta( $id, '_first_available_date', $first_available_date );
        update_post_meta( $id, '_booking_option', $is_bookable );
    }

}

return new WCEB_Product_Settings();

endif;