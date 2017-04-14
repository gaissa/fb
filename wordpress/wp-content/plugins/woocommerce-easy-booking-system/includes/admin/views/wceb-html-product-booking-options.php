<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// WooCommerce 3.0 compatibility
if ( is_callable( array( $product, 'get_type' ) ) ) {
    $product_type = $product->get_type();
} else {
    $product_type = $product->product_type;
}

?>

<div id="booking_product_data" class="panel woocommerce_options_panel">

    <div class="options_group show_if_bookable">

        <?php woocommerce_wp_select( array(
            'id'          => 'booking_dates',
            'class'       => 'select short booking_dates',
            'name'        => '_booking_dates',
            'label'       => __( 'Number of dates to select', 'easy_booking' ),
            'desc_tip'    => true,
            'description' => __( 'Choose whether to have one or two date(s) to select for this product.', 'easy_booking' ),
            'value'       => isset( $post->_booking_dates ) ? $post->_booking_dates : 'global',
            'options'     => array(
                'global' => __( 'Same as global settings', 'easy_booking' ),
                'one'    => __( 'One', 'easy_booking' ),
                'two'    => __( 'Two', 'easy_booking' )
            )
        ) ); ?>

        <div class="show_if_two_dates">

            <?php woocommerce_wp_select( array(
                'id'          => 'booking_duration',
                'class'       => 'select short booking_duration',
                'name'        => '_booking_duration',
                'label'       => __( 'Booking duration', 'easy_booking' ),
                'desc_tip'    => true,
                'description' => __( 'The booking duration of your products. Daily, weekly or a custom period (e.g. 28 days for a monthly booking). The price will be applied to the whole period.', 'easy_booking' ),
                'value'       => isset( $post->_booking_duration ) ? $post->_booking_duration : 'global',
                'options'     => array(
                    'global' => __( 'Same as global settings', 'easy_booking' ),
                    'days'   => __( 'Daily', 'easy_booking' ),
                    'weeks'  => __( 'Weekly', 'easy_booking' ),
                    'custom' => __( 'Custom', 'easy_booking' )
                )
            ) );

            woocommerce_wp_text_input( array(
                'id'                => 'custom_booking_duration',
                'class'             => 'custom_booking_duration',
                'name'              => '_custom_booking_duration',
                'label'             => __( 'Custom booking duration (days)', 'easy_booking' ),
                'value'             => isset( $post->_custom_booking_duration ) ? $post->_custom_booking_duration : '',
                'placeholder'       =>  __( 'Same as global settings', 'easy_booking' ),
                'type'              => 'number',
                'custom_attributes' => array(
                    'step' => '1',
                    'min'  => '1',
                    'max'  => '366'
                ) 
            ) );

            woocommerce_wp_text_input( array(
                'id'                => 'booking_min',
                'class'             => 'booking_min',
                'name'              => '_booking_min',
                'label'             => __( 'Minimum booking duration', 'easy_booking' ) . ' (<span class="wceb_unit">' . __('days', 'easy_booking') . '</span>)',
                'desc_tip'          => 'true',
                'description'       => __( 'The minimum number of days / weeks / custom period to book. Leave zero to set no duration limit. Leave empty to use the global settings.', 'easy_booking' ),
                'value'             => isset( $post->_booking_min ) ? $post->_booking_min : '',
                'placeholder'       => __( 'Same as global settings', 'easy_booking' ),
                'type'              => 'number',
                'custom_attributes' => array(
                    'step' => '1',
                    'min'  => '0'
                ) 
            ) );

            woocommerce_wp_text_input( array(
                'id'                => 'booking_max',
                'class'             => 'booking_max',
                'name'              => '_booking_max',
                'label'             => __( 'Maximum booking duration', 'easy_booking' ) . ' (<span class="wceb_unit">' . __('days', 'easy_booking') . '</span>)',
                'desc_tip'          => 'true',
                'description'       => __( 'The maximum number of days / weeks / custom period to book. Leave zero to set no duration limit. Leave empty to use the global settings.', 'easy_booking' ),
                'value'             => isset( $post->_booking_max ) ? $post->_booking_max : '',
                'placeholder'       => __( 'Same as global settings', 'easy_booking' ),
                'type'              => 'number',
                'custom_attributes' => array(
                    'step' => '1',
                    'min'  => '0'
                )
            ) ); ?>

        </div>

        <?php woocommerce_wp_text_input( array(
            'id'                => 'first_available_date',
            'class'             => 'first_available_date',
            'name'              => '_first_available_date',
            'label'             => __( 'First available date (day)', 'easy_booking' ),
            'desc_tip'          => 'true',
            'description'       => __( 'First available date, relative to the current day. I.e. : today + 5 days. Leave zero for the current day. Leave empty to use the global settings.', 'easy_booking' ),
            'value'             => isset( $post->_first_available_date ) ? $post->_first_available_date : '',
            'placeholder'       => __( 'Same as global settings', 'easy_booking' ),
            'type'              => 'number',
            'custom_attributes' => array(
                'step' => '1',
                'min'  => '0'
            )
        ) ); ?>

    </div>

    <?php do_action( 'easy_booking_after_booking_options', $product ); ?>
    <?php do_action( 'easy_booking_after_' . $product_type . '_booking_options', $product ); ?>

</div>