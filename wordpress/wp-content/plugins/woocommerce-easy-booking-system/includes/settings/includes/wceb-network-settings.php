<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

register_setting(
	'easy_booking_global_settings',
	'easy_booking_global_settings', 
	array( $this, 'sanitize_values' )
);