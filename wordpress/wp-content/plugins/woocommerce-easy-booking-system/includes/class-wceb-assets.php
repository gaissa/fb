<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WCEB_Assets' ) ) :

class WCEB_Assets {

	public function __construct() {
        // Get plugin options values
        $this->options = get_option( 'easy_booking_settings' );
        
		if ( ! is_admin() ) {
            add_action( 'wp_enqueue_scripts', array( $this, 'wceb_enqueue_scripts' ), 15 );
        }
	}

	public function wceb_enqueue_scripts() {
        global $post;

        // If not on a single product page, return
        if ( ! is_product() ) {
            return;
        }

        $product = wc_get_product( $post->ID );

        if ( ! $product ) {
            return;
        }
        
        // If product is out-of-stock, return
        if ( ! $product->is_in_stock() ) {
            return;
        }

        // Load scripts only on product page if "booking" option is checked
        if ( wceb_is_bookable( $product ) ) {
        
            // WooCommerce 2.7 compatibility
            if ( is_callable( array( $product, 'get_type' ) ) ) {
                $product_type = $product->get_type();
            } else {
                $product_type = $product->product_type;
            }

            $booking_settings = array();
            $children         = array();
            $tax_display_mode = get_option( 'woocommerce_tax_display_shop' );

            $prices = array();
            
            switch ( $product_type ) {
                case 'variable' :

                    // Get parent product booking data
                    $parent_booking_settings = wceb_get_product_booking_data( $product );
                    $variation_ids = $product->get_children();

                    // Price html for each variation (" / day", " / night" or " / week")
                    $booking_settings['prices_html'] = array();

                    if ( $variation_ids ) {

                        foreach ( $variation_ids as $variation_id ) {

                            $variation = wc_get_product( $variation_id );

                            if ( ! wceb_is_bookable( $variation ) ) {
                                continue;
                            }


                            $variation_settings = wceb_get_variation_booking_data( $variation, $parent_booking_settings );
                            $booking_settings['prices_html'][$variation_id] = wceb_get_price_html( $variation );

                            foreach ( $variation_settings as $setting => $value ) {
                                $booking_settings[$setting][$variation_id] = $value;
                            }

                        }

                    } else {

                        foreach ( $parent_booking_settings as $setting => $value ) {
                            $booking_settings[$setting] = $value;
                        }
                    }

                break;
                case 'grouped' :

                    $booking_settings = wceb_get_product_booking_data( $product );
                    $booking_settings['prices_html'] = wceb_get_price_html( $product );

                    // Get grouped product children prices
                    $children = $product->get_children();

                    if ( $children ) foreach ( $children as $child_id ) {
                        $child = wc_get_product( $child_id );
                        $child_prices[$child_id] = wceb_get_product_price( $product, $child, false, 'array' );
                        $prices['price'][$child_id] = $child_prices[$child_id]['price'];
                        $prices['regular_price'][$child_id] = isset( $child_prices[$child_id]['regular_price'] ) ? $child_prices[$child_id]['regular_price'] : '';
                    }

                break;
                case 'bundle' :

                    $booking_settings = wceb_get_product_booking_data( $product );
                    $booking_settings['prices_html'] = wceb_get_price_html( $product );

                    $prices = wceb_get_product_price( $product, false, false, 'array' );

                break;
                default:

                    $booking_settings = wceb_get_product_booking_data( $product );
                    $booking_settings['prices_html'] = wceb_get_price_html( $product );
                    
                break;
            }
            
            // Register scripts
            $this->wceb_register_frontend_scripts( $product, $children, $prices, $booking_settings );

            // Load scripts
            $this->wceb_load_scripts();

        }
        
    }

    private function wceb_register_frontend_scripts( $product, $children = array(), $prices, $booking_settings ) {

        // WooCommerce 2.7 compatibility
        if ( is_callable( array( $product, 'get_type' ) ) ) {
            $product_type = $product->get_type();
        } else {
            $product_type = $product->product_type;
        }
        
        $calc_mode       = $this->options['easy_booking_calc_mode']; // Calculation mode (Days or Nights)
        $start_date_text = apply_filters( 'easy_booking_start_text', __( 'Start', 'easy_booking' ), $product );
        $end_date_text   = apply_filters( 'easy_booking_end_text', __( 'End', 'easy_booking' ), $product );
        $theme           = $this->options['easy_booking_calendar_theme']; // Calendar theme

        $last_year = isset( $this->options['easy_booking_max_year'] ) ? absint( $this->options['easy_booking_max_year'] ) : absint( date('Y') + 5 );

        // Load accounting.js script
        // Backward compatibility
        if ( WC()->version >= '2.5.0' ) {

            wp_register_script(
                'accounting',
                WC()->plugin_url() . '/assets/js/accounting/accounting' . WCEB_SUFFIX . '.js',
                array( 'jquery' ),
                '0.4.2'
            );

        } else {

            wp_register_script(
                'accounting',
                WC()->plugin_url() . '/assets/js/admin/accounting' . WCEB_SUFFIX . '.js',
                array( 'jquery' ),
                '0.4.2'
            );

        }

        if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {

            wp_register_script(
                'picker',
                plugins_url( 'assets/js/dev/picker.js', WCEB_PLUGIN_FILE ),
                array( 'jquery' ),
                '1.0',
                true
            );

            wp_register_script(
                'legacy',
                plugins_url( 'assets/js/dev/legacy.js', WCEB_PLUGIN_FILE ),
                array( 'jquery' ),
                '1.0',
                true
            );

            wp_register_script(
                'pickadate',
                plugins_url( 'assets/js/dev/picker.date.js', WCEB_PLUGIN_FILE ),
                array( 'jquery', 'picker', 'legacy' ),
                '1.0',
                true
            );

        } else {

            // Concatenated and minified script including picker.js, picker.date.js and legacy.js
            wp_register_script(
                'pickadate',
                plugins_url( 'assets/js/pickadate.min.js', WCEB_PLUGIN_FILE ),
                array( 'jquery' ),
                '1.0',
                true
            );

        }

        wp_register_script(
            'wceb-main',
            wceb_get_file_path( '', 'wceb', 'js' ),
            array( 'jquery', 'pickadate', 'accounting' ),
            '1.0',
            true
        );

        wp_register_script(
            'pickadate-custom',
            wceb_get_file_path( '', 'wceb-' . $product_type, 'js' ),
            array( 'jquery', 'pickadate', 'wceb-main' ),
            '1.0',
            true
        );

        // Datepickers parameters
        $pickadate_params = apply_filters(
            'easy_booking_frontend_parameters',
            array(
                'ajax_url'                     => esc_url( admin_url( 'admin-ajax.php' ) ),
                'product_type'                 => esc_html( $product_type ),
                'children'                     => $children,
                'calc_mode'                    => esc_html( $calc_mode ),
                'start_text'                   => esc_html( $start_date_text ),
                'end_text'                     => esc_html( $end_date_text ),
                'booking_dates'                => $this->wceb_sanitize_parameters( $booking_settings['booking_dates'], 'esc_html' ),
                'booking_duration'             => $this->wceb_sanitize_parameters( $booking_settings['booking_duration'], 'esc_html' ),
                'booking_custom_duration'      => $this->wceb_sanitize_parameters( $booking_settings['custom_booking_duration'], 'absint' ),
                'min'                          => $this->wceb_sanitize_parameters( $booking_settings['booking_min'], 'absint' ),
                'max'                          => $this->wceb_sanitize_parameters( $booking_settings['booking_max'], 'esc_html' ),
                'first_date'                   => $this->wceb_sanitize_parameters( $booking_settings['first_available_date'], 'absint' ),
                'max_year'                     => absint( substr( $last_year, 0, 4 ) ),
                'prices_html'                  => $this->wceb_sanitize_parameters( $booking_settings['prices_html'], 'esc_html' ),
                'currency_format_num_decimals' => absint( get_option( 'woocommerce_price_num_decimals' ) ),
                'currency_format_symbol'       => get_woocommerce_currency_symbol(),
                'currency_format_decimal_sep'  => esc_attr( stripslashes( get_option( 'woocommerce_price_decimal_sep' ) ) ),
                'currency_format_thousand_sep' => esc_attr( stripslashes( get_option( 'woocommerce_price_thousand_sep' ) ) ),
                'currency_format'              => esc_attr( str_replace( array( '%1$s', '%2$s' ), array( '%s', '%v' ), get_woocommerce_price_format() ) ), // For accounting JS
            )
        );

        if ( array_key_exists( 'price', $prices ) ) {
            $pickadate_params['product_price'] = $this->wceb_sanitize_parameters( $prices['price'], 'wc_format_decimal');
        }

        if ( array_key_exists( 'regular_price', $prices ) ) {
            $pickadate_params['product_regular_price'] = $this->wceb_sanitize_parameters( $prices['regular_price'], 'wc_format_decimal');
        }

        wp_localize_script(
            'wceb-main',
            'wceb_object',
            $pickadate_params
        );

        // Load datepickers stylesheet
        // If multisite, load the stylesheet corresponding to the site ID
        if ( function_exists( 'is_multisite' ) && is_multisite() ) {
            $blog_id = get_current_blog_id();

            wp_register_style(
                'picker',
                plugins_url( 'assets/css/' . $theme . '.' . $blog_id . '.min.css', WCEB_PLUGIN_FILE ),
                true
            );

        } else {

            wp_register_style(
                'picker',
                plugins_url( 'assets/css/' . $theme . '.min.css', WCEB_PLUGIN_FILE ),
                true
            );

        }

        // Load Right to left CSS file if necessary
        if ( is_rtl() ) {

            wp_register_style(
                'rtl-style',
                wceb_get_file_path( '', 'rtl', 'css' ),
                array( 'picker' ),
                true
            );

        }

        // Load translations
        wp_register_script(
            'datepicker.language',
            plugins_url( 'assets/js/translations/' . WCEB_LANG . '.js', WCEB_PLUGIN_FILE ),
            array( 'jquery', 'pickadate', 'wceb-main' ),
            '1.0',
            true
        );

        // Parameters for translations
        wp_localize_script(
            'datepicker.language',
            'params',
            array(
                'first_day' => absint( $this->options['easy_booking_first_day'] )
            )
        );

    }

    private function wceb_load_scripts() {
        
        wp_enqueue_script( 'accounting' );
        wp_enqueue_script( 'pickadate' );

        // Hook to load additional scipts or stylesheets
        do_action( 'easy_booking_enqueue_additional_scripts' );

        wp_enqueue_script( 'wceb-main' );
        wp_enqueue_script( 'pickadate-custom' );
        wp_enqueue_style( 'picker' );

        // Load Right to left CSS file if necessary
        if ( is_rtl() ) {
            wp_enqueue_style( 'rtl-style' );
        }

        wp_enqueue_script( 'datepicker.language' );

    }

    private function wceb_sanitize_parameters( $param, $func ) {
        return is_array( $param ) ? array_map( $func, $param ) : $func( $param );
    }
}

return new WCEB_Assets();

endif;