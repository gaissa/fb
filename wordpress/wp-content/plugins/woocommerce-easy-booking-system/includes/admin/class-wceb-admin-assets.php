<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WCEB_Admin_Assets' ) ) :

class WCEB_Admin_Assets {

	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'easy_booking_enqueue_admin_scripts' ), 20 );
	}

	public function easy_booking_enqueue_admin_scripts() {
        // Current screen ID
        $screen    = get_current_screen();
        $screen_id = $screen->id;

        // Plugin settings
        $this->options = get_option('easy_booking_settings');

        // Calendar theme
        $theme = $this->options['easy_booking_calendar_theme'];

        if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {

            wp_register_script(
                'picker',
                plugins_url( 'assets/js/dev/picker.js', WCEB_PLUGIN_FILE  ),
                array( 'jquery' ),
                '1.0',
                true
            );

            wp_register_script(
                'legacy',
                plugins_url( 'assets/js/dev/legacy.js', WCEB_PLUGIN_FILE  ),
                array( 'jquery' ),
                '1.0',
                true
            );

            wp_register_script(
                'pickadate',
                plugins_url( 'assets/js/dev/picker.date.js', WCEB_PLUGIN_FILE  ),
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

        // JS for admin product settings
        wp_register_script(
            'wceb-admin-product',
            wceb_get_file_path( 'admin', 'wceb-admin-product', 'js' ),
            array( 'jquery' ),
            '1.0',
            true
        );

        $global_duration = $this->options['easy_booking_duration'];
        $global_text     = __( 'days', 'easy_booking' );

        switch ( $global_duration ) {
            case 'weeks' :
                $global_text = __( 'weeks', 'easy_booking' );
            break;
            case 'custom':
                $global_text = __( 'custom period', 'easy_booking' );
            break;
            default:
                $global_text = __( 'days', 'easy_booking' );
            break;
        }

        wp_localize_script(
            'wceb-admin-product',
            'localization',
            array(
                'date_format'     => esc_html( $this->options['easy_booking_dates'] ),
                'global'          => esc_html( $global_text ),
                'global_duration' => esc_html( $this->options['easy_booking_custom_duration'] ),
                'days'            => __( 'days', 'easy_booking' ),
                'weeks'           => __( 'weeks', 'easy_booking' ),
                'custom'          => __( 'custom period', 'easy_booking' )
            )
        );

        // JS for pickadate.js in the admin panel
        wp_register_script(
            'pickadate-custom-admin',
            wceb_get_file_path( 'admin', 'pickadate-custom-admin', 'js' ),
            '1.0',
            true
        );

        // JS for admin notices
        wp_register_script(
            'easy_booking_functions',
            wceb_get_file_path( 'admin', 'wceb-admin-functions', 'js' ),
            array( 'jquery' ),
            '1.0',
            true
        );

        wp_localize_script(
            'easy_booking_functions',
            'ajax_object',
            array(
                'ajax_url' => esc_url( admin_url( 'admin-ajax.php' ) ),
                'hide_notice_nonce' => wp_create_nonce( 'hide-notice' )
            )
        );

        // Pickadate translation
        wp_register_script(
            'datepicker.language',
            plugins_url( 'assets/js/translations/' . WCEB_LANG . '.js', WCEB_PLUGIN_FILE ),
            array( 'jquery', 'pickadate' ),
            '1.0',
            true
        );

        wp_localize_script(
            'datepicker.language',
            'params',
            array(
                'first_day' => absint( $this->options['easy_booking_first_day'] )
            )
        );

        // Picker CSS
        if ( function_exists( 'is_multisite' ) && is_multisite() ) {
            // If multisite, register the CSS file corresponding to the blog ID
            $blog_id = get_current_blog_id();

            wp_register_style(
                'picker',
                plugins_url( 'assets/css/' . $theme . '.' . $blog_id . '.min.css', WCEB_PLUGIN_FILE ),
                true
            );

            wp_register_style(
                'picker-default',
                plugins_url( 'assets/css/default.' . $blog_id . '.min.css', WCEB_PLUGIN_FILE ),
                true
            );

        } else {

            wp_register_style(
                'picker',
                plugins_url( 'assets/css/' . $theme . '.min.css', WCEB_PLUGIN_FILE ),
                true
            );

            wp_register_style(
                'picker-default',
                plugins_url( 'assets/css/default.min.css', WCEB_PLUGIN_FILE ),
                true
            );

        }

        // Static picker CSS
        wp_register_style(
            'static-picker',
            wceb_get_file_path( 'admin', 'static-picker', 'css' ),
            true
        );

        // Picker right-to-left CSS
        wp_register_style(
            'rtl-style',
            wceb_get_file_path( '', 'rtl', 'css' ),
            true
        );

        // CSS for admin notices
        wp_register_style(
            'easy_booking_notices',
            wceb_get_file_path( 'admin', 'wceb-notices', 'css' ),
            WCEB_PLUGIN_FILE
        );

        $this->wceb_load_admin_common_scripts();

        if ( in_array( $screen_id, array( 'product' ) ) ) {
            $this->wceb_load_admin_product_scripts();
        }

        if ( in_array( $screen_id, array( 'shop_order' ) ) ) {
            $this->wceb_load_admin_order_scripts();
        }
        
        if ( in_array( $screen_id, array( 'product' ) ) || in_array( $screen_id, array( 'shop_order' ) ) ) {
            $this->wceb_load_admin_product_and_order_scripts(); 
        }

    }

    /**
    *
    * Load scripts common to the whole admin panel
    *
    **/
    private function wceb_load_admin_common_scripts() {
        wp_enqueue_script( 'easy_booking_functions' );
        wp_enqueue_style( 'easy_booking_notices' );
        
    }

    /**
    *
    * Load scripts on the admin product page
    *
    **/
    private function wceb_load_admin_product_scripts() {
        wp_enqueue_script( 'wceb-admin-product' );
        wp_enqueue_style( 'picker-default' );
        wp_enqueue_style( 'static-picker' );
    }

    /**
    *
    * Load scripts on the admin order page
    *
    **/
    private function wceb_load_admin_order_scripts() {
        global $post;

        wp_enqueue_script( 'pickadate-custom-admin' );

        // Calculation mode (Days or Nights)
        $calc_mode = $this->options['easy_booking_calc_mode'];

        wp_localize_script( 'pickadate-custom-admin', 'order_ajax_info',
            array( 
                'ajax_url'  => esc_url( admin_url( 'admin-ajax.php' ) ),
                'order_id'  => $post->ID,
                'calc_mode' => esc_html( $calc_mode )
            )
        );

        wp_enqueue_style( 'picker' );

    }

    /**
    *
    * Load scripts on the admin product and order pages
    *
    **/
    private function wceb_load_admin_product_and_order_scripts() {
        wp_enqueue_script( 'pickadate' );

        if ( is_rtl() ) {
            wp_enqueue_style( 'rtl-style' );  
        }
        
        wp_enqueue_script( 'datepicker.language' );
    }
}

return new WCEB_Admin_Assets();

endif;