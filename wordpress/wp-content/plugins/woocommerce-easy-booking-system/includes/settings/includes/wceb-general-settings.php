<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

register_setting(
	'easy_booking_general_settings',
	'easy_booking_settings', 
	array( $this, 'sanitize_values' )
);

add_settings_section(
	'easy_booking_main_settings',
	__( 'General settings', 'easy_booking' ),
	array( $this, 'easy_booking_section_general' ),
	'easy_booking_general_settings'
);

add_settings_field(
	'easy_booking_calc_mode',
	__( 'Calculation mode', 'easy_booking' ),
	array( $this, 'easy_booking_calc_mode' ),
	'easy_booking_general_settings',
	'easy_booking_main_settings'
);

add_settings_field(
	'easy_booking_all_bookable',
	__( 'Make all products bookable?', 'easy_booking' ),
	array( $this, 'easy_booking_all_bookable' ),
	'easy_booking_general_settings',
	'easy_booking_main_settings'
);

add_settings_field(
	'easy_booking_dates',
	__( 'Number of dates to select', 'easy_booking' ),
	array( $this, 'easy_booking_dates' ),
	'easy_booking_general_settings',
	'easy_booking_main_settings'
);

add_settings_field(
	'easy_booking_duration',
	__( 'Booking duration', 'easy_booking' ),
	array( $this, 'easy_booking_duration' ),
	'easy_booking_general_settings',
	'easy_booking_main_settings'
);

add_settings_field(
	'easy_booking_custom_duration',
	__( 'Custom booking duration', 'easy_booking' ),
	array( $this, 'easy_booking_custom_duration' ),
	'easy_booking_general_settings',
	'easy_booking_main_settings'
);

add_settings_field(
	'easy_booking_booking_min',
	__( 'Minimum booking duration', 'easy_booking' ),
	array( $this, 'easy_booking_booking_min' ),
	'easy_booking_general_settings',
	'easy_booking_main_settings'
);

add_settings_field(
	'easy_booking_booking_max',
	__( 'Maximum booking duration', 'easy_booking' ),
	array( $this, 'easy_booking_booking_max' ),
	'easy_booking_general_settings',
	'easy_booking_main_settings'
);

add_settings_field(
	'easy_booking_first_available_date',
	__( 'First available date', 'easy_booking' ),
	array( $this, 'easy_booking_first_available_date' ),
	'easy_booking_general_settings',
	'easy_booking_main_settings'
);

add_settings_field(
	'easy_booking_max_year',
	__( 'Booking limit', 'easy_booking' ),
	array( $this, 'easy_booking_max_year' ),
	'easy_booking_general_settings',
	'easy_booking_main_settings'
);

add_settings_field(
	'easy_booking_first_day',
	__( 'First weekday', 'easy_booking' ),
	array( $this, 'easy_booking_first_day' ),
	'easy_booking_general_settings',
	'easy_booking_main_settings'
);