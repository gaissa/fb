<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WCEB_Settings {

	public function __construct() {

		// get plugin options values
		$this->options = get_option('easy_booking_settings');

		// initialize options the first time
		if ( ! $this->options ) {
		
		    $this->options = array(
		    	'easy_booking_calc_mode'            => 'nights',
		    	'easy_booking_all_bookable'         => 'no',
		    	'easy_booking_dates'                => 'two',
		    	'easy_booking_duration'             => 'days',
		    	'easy_booking_custom_duration'      => '1',
		    	'easy_booking_booking_min'          => '',
		    	'easy_booking_booking_max'          => '',
		    	'easy_booking_first_available_date' => '',
		    	'easy_booking_year_max'             => absint( date('Y') + 5 ),
		    	'easy_booking_first_day'            => 1,
		        'easy_booking_calendar_theme'       => 'default',
		        'easy_booking_background_color'     => '#FFFFFF',
		        'easy_booking_main_color'           => '#0089EC',
		        'easy_booking_text_color'           => '#000000'
		    );

		    add_option( 'easy_booking_settings', $this->options );

		}

		// Backward compatibility
		if ( ! isset( $this->options['easy_booking_first_day'] ) ) {
			$this->options['easy_booking_first_day'] = 1;
			update_option('easy_booking_settings', $this->options);
		}

		// Backward compatibility
		if ( ! isset( $this->options['easy_booking_duration'] ) ) {
			$this->options['easy_booking_duration'] = 'days';
			update_option('easy_booking_settings', $this->options);
		}

		// Backward compatibility
		if ( ! isset( $this->options['easy_booking_custom_duration'] ) ) {
			$this->options['easy_booking_custom_duration'] = '1';
			update_option('easy_booking_settings', $this->options);
		}

		// Backward compatibility
		if ( ! isset( $this->options['easy_booking_dates'] ) ) {
			$this->options['easy_booking_dates'] = 'two';
			update_option('easy_booking_settings', $this->options);
		}

		if ( is_multisite() ) {
			
			$this->global_settings = get_option('easy_booking_global_settings');

			if ( ! $this->global_settings ) {
				$this->global_settings = array();
				add_option('easy_booking_global_settings', $this->global_settings);
			}

		}

		if ( is_admin() ) {

			add_action( 'admin_menu', array( $this, 'wceb_add_option_pages' ), 10 );

			if ( is_multisite() ) {
				add_action( 'network_admin_menu', array( $this,'wceb_add_network_option_pages' ) );
			}

			add_action( 'admin_init', array( $this, 'wceb_settings_init' ) );
			add_action( 'easy_booking_settings_general_tab', array( $this, 'easy_booking_general_settings' ), 10);
			add_action( 'easy_booking_settings_texts_tab', array( $this, 'easy_booking_text_settings' ), 10);
			add_action( 'easy_booking_settings_appearance_tab', array( $this, 'easy_booking_appearance_settings' ), 10);
			add_action( 'easy_booking_save_settings', array( $this, 'easy_booking_apply_global_product_settings' ) );

		}

	}

	/**
	 *
	 * On multisite, display a special settings page for license keys
	 *
	 */
	public function wceb_add_network_option_pages() {
		$hook = add_menu_page(
			'Easy Booking',
			'Easy Booking',
			apply_filters( 'easy_booking_settings_capability', 'manage_options' ),
			'easy-booking',
			'',
			'dashicons-calendar-alt',
			58
		);

		$option_page = add_submenu_page(
			'easy-booking',
			__( 'Global Settings', 'easy_booking' ),
			__( 'Global Settings', 'easy_booking' ),
			apply_filters( 'easy_booking_settings_capability', 'manage_options' ),
			'easy-booking',
			array( $this, 'easy_booking_global_option_page' )
		);

		$addons_page = add_submenu_page(
			'easy-booking',
			__( 'Add-ons', 'easy_booking' ),
			__( 'Add-ons', 'easy_booking' ),
			apply_filters( 'easy_booking_settings_capability', 'manage_options' ),
			'easy-booking-addons',
			array( $this, 'easy_booking_addons_page' )
		);
	}

	/**
	 *
	 * Plugin settings page
	 *
	 */
	public function wceb_add_option_pages() {
		$hook = add_menu_page(
			'Easy Booking',
			'Easy Booking',
			apply_filters( 'easy_booking_settings_capability', 'manage_options' ),
			'easy-booking',
			'',
			'dashicons-calendar-alt',
			58
		);

		$option_page = add_submenu_page(
			'easy-booking',
			__('Settings', 'easy_booking'),
			__('Settings', 'easy_booking'),
			apply_filters( 'easy_booking_settings_capability', 'manage_options' ),
			'easy-booking',
			array( $this, 'easy_booking_option_page' )
		);

		$new_addon = ' <span class="wceb-new-addon">New !</span>';

		$addons_page = add_submenu_page(
			'easy-booking',
			__('Add-ons', 'easy_booking'),
			__('Add-ons', 'easy_booking') . $new_addon,
			apply_filters( 'easy_booking_settings_capability', 'manage_options' ),
			'easy-booking-addons',
			array( $this, 'easy_booking_addons_page' )
		);
		
		add_action( 'load-'. $hook, array( $this, 'wceb_save_settings' ) );
		add_action( 'admin_print_scripts-'. $option_page, array( $this, 'wceb_load_settings_scripts' ) );
	}

	/**
	 *
	 * Generate CSS files when saving settings and add an action hook
	 *
	 */
	public function wceb_save_settings() {

		// If settings are updated
	  	if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] ) {

	  		$data = get_option('easy_booking_settings');

	  		// Regenerate CSS files after saving settings (in case the colors have changed)
	  		$this->wceb_generate_css( $data );

			do_action( 'easy_booking_save_settings', $data );
			
	   	}

	}

	/**
	 *
	 * Load colorpicker script and style
	 *
	 */
	public function wceb_load_settings_scripts() {
	  	wp_enqueue_style('wp-color-picker');
	  	wp_enqueue_script('color-picker', plugins_url('assets/js/admin/script.js', WCEB_PLUGIN_FILE), array('wp-color-picker'), false, true );
	}

	// Generate static css file
	public function wceb_generate_css( $data ) {
		$plugin_dir = plugin_dir_path( WCEB_PLUGIN_FILE ); // Shorten code, save 1 call

        $php_files = array(
        	'default' => realpath( $plugin_dir . 'assets/css/dev/default.css.php' ),
        	'classic' => realpath( $plugin_dir . 'assets/css/dev/classic.css.php' )
        );

        $blog_id = '';

        if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			$blog_id = '.' . get_current_blog_id();
        }

		$css_files = array(
        	'default' => realpath( $plugin_dir . 'assets/css/default' . $blog_id . '.min.css' ),
        	'classic' => realpath( $plugin_dir . 'assets/css/classic' . $blog_id . '.min.css' )
        );

        if ( $php_files ) foreach ( $php_files as $theme => $php_file ) {
        	ob_start(); // Capture all output (output buffering)

	        require( $php_file ); // Generate CSS
	        
			$css          = ob_get_clean(); // Get generated CSS (output buffering)
			$minified_css = wceb_minify_css( $css ); // Minify CSS

	        if ( file_exists( $css_files[$theme] ) ) {

	        	if ( is_writable( $css_files[$theme] ) ) {
	        		file_put_contents( $css_files[$theme], $minified_css ); // Save it
	        	}

	        } else {

	        	$file = fopen( $plugin_dir . 'assets/css/' . $theme . $blog_id . '.min.css', 'a+' );
		        fwrite( $file, $minified_css );
		        fclose( $file );

	        }

        }
        
    }

	public function wceb_settings_init() {

		$this->wceb_general_settings();
		$this->wceb_appearance_settings();

		// Multisite settings
		if ( is_multisite() ) {
			$this->wceb_multisite_settings();
		}

	}

	public function wceb_general_settings() {
		include_once( 'includes/wceb-general-settings.php' );
	}

	public function wceb_appearance_settings() {
		include_once( 'includes/wceb-appearance-settings.php' );
	}

	public function wceb_multisite_settings() {
		include_once( 'includes/wceb-network-settings.php' );
	}

	public function easy_booking_option_page() {
		include_once( 'views/html-wceb-settings.php' );
	}

	public function easy_booking_general_settings() {
		settings_fields( 'easy_booking_general_settings' );
		do_settings_sections( 'easy_booking_general_settings' );
	}

	public function easy_booking_text_settings() {
		settings_fields( 'easy_booking_text_settings' );
		do_settings_sections( 'easy_booking_text_settings' );
	}

	public function easy_booking_appearance_settings() {
		settings_fields( 'easy_booking_appearance_settings' );
		do_settings_sections( 'easy_booking_appearance_settings' );
	}

	public function easy_booking_global_option_page() {
		include_once( 'views/html-wceb-network-settings.php' );
	}

	public function easy_booking_section_general() {
		echo '';
	}

	public function easy_booking_calc_mode() {
		wceb_settings_select( array(
			'id'          => 'calc_mode',
			'name'        => 'easy_booking_settings[easy_booking_calc_mode]',
			'value'       => isset( $this->options['easy_booking_calc_mode'] ) ? $this->options['easy_booking_calc_mode'] : 'nights',
			'description' => __('Choose whether to calculate the final price depending on number of days or number of nights (i.e. 5 days = 4 nights).' , 'easy_booking'),
			'options'     => array(
				'days'   => __('Days', 'easy_booking'),
				'nights' => __('Nights', 'easy_booking')
			)
		));
	}

	public function easy_booking_all_bookable() {
		wceb_settings_checkbox( array(
			'id'          => 'easy_booking_all_bookable',
			'name'        => 'easy_booking_settings[easy_booking_all_bookable]',
			'description' => __( 'Check to make all your products bookable. Any new or modified product will be automatically bookable.', 'easy_booking' ),
			'value'       => isset( $this->options['easy_booking_all_bookable'] ) ? $this->options['easy_booking_all_bookable'] : '',
			'cbvalue'     => 'on'
		));

		echo '<input type="hidden" name="easy_booking_settings[post_all_bookable]" value="1">';
	}

	public function easy_booking_dates() {
		wceb_settings_select( array(
			'id'          => 'easy_booking_dates',
			'name'        => 'easy_booking_settings[easy_booking_dates]',
			'value'       => isset( $this->options['easy_booking_dates'] ) ? $this->options['easy_booking_dates'] : 'days',
			'options'     => array(
				'one'   => __( 'One', 'easy_booking' ),
                'two'  => __( 'Two', 'easy_booking' )
			)
		));
	}

	public function easy_booking_duration() {
		wceb_settings_select( array(
			'id'          => 'easy_booking_duration',
			'name'        => 'easy_booking_settings[easy_booking_duration]',
			'value'       => isset( $this->options['easy_booking_duration'] ) ? $this->options['easy_booking_duration'] : 'days',
			'options'     => array(
				'days'   => __( 'Daily', 'easy_booking' ),
                'weeks'  => __( 'Weekly', 'easy_booking' ),
                'custom' => __( 'Custom', 'easy_booking' )
			)
		));
	}

	public function easy_booking_custom_duration() {
		wceb_settings_input( array(
			'type'              => 'number',
			'id'                => 'easy_booking_custom_duration',
			'name'              => 'easy_booking_settings[easy_booking_custom_duration]',
			'description'       => __( 'Used only for products with "Custom" as a booking duration.', 'easy_booking' ),
			'value'             => isset( $this->options['easy_booking_custom_duration'] ) ? absint( $this->options['easy_booking_custom_duration'] ) : '1',
			'custom_attributes' => array(
				'step' => '1',
                'min'  => '1',
                'max'  => '366'
			)
		));
	}

	public function easy_booking_booking_min() {
		wceb_settings_input( array(
			'type'              => 'number',
			'id'                => 'easy_booking_booking_min',
			'name'              => 'easy_booking_settings[easy_booking_booking_min]',
			'value'             => isset( $this->options['easy_booking_booking_min'] ) ? absint( $this->options['easy_booking_booking_min'] ) : '',
			'description'       => __('Set a minimum booking duration for all your products. You can individually change it on your product settings. Leave 0 or empty to set no duration limit.', 'easy_booking'),
			'custom_attributes' => array(
				'step' => '1',
                'min'  => '0'
			)
		));
	}

	public function easy_booking_booking_max() {
		wceb_settings_input( array(
			'type'              => 'number',
			'id'                => 'easy_booking_booking_max',
			'name'              => 'easy_booking_settings[easy_booking_booking_max]',
			'value'             => isset( $this->options['easy_booking_booking_max'] ) ? absint( $this->options['easy_booking_booking_max'] ) : '',
			'description'       => __('Set a maximum booking duration for all your products. You can individually change it on your product settings. Leave 0 or empty to set no duration limit.', 'easy_booking'),
			'custom_attributes' => array(
				'min' => 0
			)
		));
	}

	public function easy_booking_first_available_date() {
		wceb_settings_input( array(
			'type'              => 'number',
			'id'                => 'easy_booking_first_available_date',
			'name'              => 'easy_booking_settings[easy_booking_first_available_date]',
			'value'             => isset( $this->options['easy_booking_first_available_date'] ) ? absint( $this->options['easy_booking_first_available_date'] ) : '',
			'description'       => __('Set the first available date for all your products, relative to the current day. You can individually change it on your product settings. Leave 0 or empty to keep the current day.', 'easy_booking'),
			'custom_attributes' => array(
				'min' => 0
			)
		));
	}

	public function easy_booking_max_year() {
		$current_year = absint( date('Y') );
		wceb_settings_input( array(
			'type'              => 'number',
			'id'                => 'easy_booking_max_year',
			'name'              => 'easy_booking_settings[easy_booking_max_year]',
			'value'             => isset ( $this->options['easy_booking_max_year'] ) ? $this->options['easy_booking_max_year'] : absint( $current_year + 5 ),
			'description'       => __('Set the limit to allow bookings (December 31 of year x). Min: current year. Max: current year + 10 years.', 'easy_booking'),
			'custom_attributes' => array(
				'min' => $current_year,
				'max' => absint( $current_year + 10 )
			)
		));
	}

	public function easy_booking_first_day() {
		wceb_settings_select( array(
			'id'          => 'easy_booking_first_day',
			'name'        => 'easy_booking_settings[easy_booking_first_day]',
			'value'       => isset( $this->options['easy_booking_first_day'] ) ? $this->options['easy_booking_first_day'] : '1',
			'options'     => array(
				'1' => __( 'Monday', 'easy_booking' ),
				'0' => __( 'Sunday', 'easy_booking' )
			)
		));
	}

	public function easy_booking_section_color() {
		echo '<p>' . __('Customize the calendar so it looks great with your theme !', 'easy_booking') . '</br>' . __('Prefer a light background and a dark text color, for better rendering.', 'easy_booking') . '</p>';
	}

	public function easy_booking_theme() {
		wceb_settings_select( array(
			'id'          => 'calendar_theme',
			'name'        => 'easy_booking_settings[easy_booking_calendar_theme]',
			'value'       => isset( $this->options['easy_booking_calendar_theme'] ) ? $this->options['easy_booking_calendar_theme'] : 'default',
			'options'     => array(
				'default' => __( 'Default', 'easy_booking' ),
				'classic' => __( 'Classic', 'easy_booking' )
			)
		));
	}	

	public function easy_booking_background() {
		wceb_settings_input( array(
			'type'              => 'text',
			'id'                => 'easy_booking_background_color',
			'name'              => 'easy_booking_settings[easy_booking_background_color]',
			'value'             => $this->options['easy_booking_background_color'],
			'class'             => 'color-field'
		));
	}

	public function easy_booking_color() {
		wceb_settings_input( array(
			'type'              => 'text',
			'id'                => 'easy_booking_main_color',
			'name'              => 'easy_booking_settings[easy_booking_main_color]',
			'value'             => $this->options['easy_booking_main_color'],
			'class'             => 'color-field'
		));
	}

	public function easy_booking_text() {
		wceb_settings_input( array(
			'type'              => 'text',
			'id'                => 'easy_booking_text_color',
			'name'              => 'easy_booking_settings[easy_booking_text_color]',
			'value'             => $this->options['easy_booking_text_color'],
			'class'             => 'color-field'
		));;
	}

	public function easy_booking_addons_page() {
		include_once('views/html-wceb-addons.php');
	}

	/**
	 * Apply global settings to bookable products
	 *
	 */
	public function easy_booking_apply_global_product_settings() {
		$all_bookable = isset( $this->options['easy_booking_all_bookable'] ) ? $this->options['easy_booking_all_bookable'] : '';

		$args = array(
            'post_type'      => array( 'product', 'product_variation' ),
            'posts_per_page' => -1,
            'post_status'    => 'publish'
        );

        $query = new WP_Query( $args );

        if ( $query ) while ( $query->have_posts() ) : $query->the_post();
        	global $post;

        	if ( ! empty( $all_bookable ) && $all_bookable === 'on' ) {
        		update_post_meta( $post->ID, '_booking_option', 'yes' );
        	}

        endwhile;
	}

	public function sanitize_values( $settings ) {
		
		$saved_settings = $this->options;

		foreach ( $settings as $key => $value ) {

			if ( $key === 'easy_booking_max_year' ) {

				$current_year                      = absint( date('Y') );
				$max_year                          = absint( $current_year + 10 );
				$settings['easy_booking_max_year'] = absint( substr( $value, 0, 4 ) );

				if ( $settings['easy_booking_max_year'] < $current_year ) {
					$settings['easy_booking_max_year'] = $current_year;
				}

				if ( $settings['easy_booking_max_year'] > $max_year ) {
					$settings['easy_booking_max_year'] = $max_year;
				}

			} else if ( $key === 'easy_booking_all_bookable' && empty( $settings['easy_booking_all_bookable'] ) ) {
				$settings['easy_booking_all_bookable'] = 'no';
			} else if ( $key === 'easy_booking_custom_duration' ) {
				
				 $settings['easy_booking_custom_duration'] = absint( $value );

				 if ( $settings['easy_booking_custom_duration'] <= 0 ) {
				 	$settings['easy_booking_custom_duration'] = 1;
				 }

			} else {
				$settings[$key] = esc_html( $value );
			}
			
		}

		if ( isset( $settings['post_all_bookable'] ) && ! isset( $settings['easy_booking_all_bookable'] ) ) {
			$settings['easy_booking_all_bookable'] = 'no';
		}

		foreach ( $saved_settings as $setting => $value ) {
			if ( ! isset( $settings[$setting] ) ) {
				$settings[$setting] = $value;
			}
		}

		return $settings;
	}
}

return new WCEB_Settings();