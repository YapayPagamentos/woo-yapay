<?php


if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

// Tables
$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . "woocommerce_yapay_intermediador_transactions" );
$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . "woocommerce_yapay_intermediador_request" );
$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . "woocommerce_yapay_intermediador_response" );
