<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

register_setting(
	'easy_booking_appearance_settings',
	'easy_booking_settings', 
	array( $this, 'sanitize_values' )
);

add_settings_section(
	'easy_booking_main_color',
	__( 'Appearance', 'easy_booking' ),
	array( $this, 'easy_booking_section_color' ),
	'easy_booking_appearance_settings'
);

add_settings_field(
	'easy_booking_calendar_theme',
	__( 'Calendar theme', 'easy_booking' ),
	array( $this, 'easy_booking_theme' ),
	'easy_booking_appearance_settings',
	'easy_booking_main_color'
);

add_settings_field(
	'easy_booking_background_color',
	__( 'Background color', 'easy_booking' ),
	array( $this, 'easy_booking_background' ),
	'easy_booking_appearance_settings',
	'easy_booking_main_color'
);

add_settings_field(
	'easy_booking_main_color',
	__( 'Main color', 'easy_booking' ),
	array( $this, 'easy_booking_color' ),
	'easy_booking_appearance_settings',
	'easy_booking_main_color'
);

add_settings_field(
	'easy_booking_text_color',
	__( 'Text color', 'easy_booking' ),
	array( $this, 'easy_booking_text' ),
	'easy_booking_appearance_settings',
	'easy_booking_main_color'
);