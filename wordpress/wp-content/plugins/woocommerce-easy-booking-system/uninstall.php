<?php

// If uninstall not called from WordPress exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}

// Delete plugin settings
delete_option( 'easy_booking_settings' );
delete_option( 'easy_booking_global_settings' );

// Delete db entries
global $wpdb;

$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->postmeta WHERE meta_key = %s", "_booking_option" ) );
$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->postmeta WHERE meta_key = %s", "_booking_min" ) );
$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->postmeta WHERE meta_key = %s", "_booking_max" ) );
$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->postmeta WHERE meta_key = %s", "_first_available_date" ) );
$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->postmeta WHERE meta_key = %s", "_booking_duration" ) );
$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->postmeta WHERE meta_key = %s", "_custom_booking_duration" ) );
$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->postmeta WHERE meta_key = %s", "_manage_bookings" ) );