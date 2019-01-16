<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $woocommerce_yapay_intermediador_db_version;
$woocommerce_yapay_intermediador_db_version = '1.1';

function woocommerce_yapay_intermediador_install () {
    global $wpdb, $woocommerce_yapay_intermediador_db_version;
    $table_transaction = $wpdb->prefix . "woocommerce_yapay_intermediador_transactions"; 
    $table_request = $wpdb->prefix . "woocommerce_yapay_intermediador_request"; 
    $table_response = $wpdb->prefix . "woocommerce_yapay_intermediador_response"; 
    
    $charset_collate = '';

    if ( $wpdb->has_cap( 'collation' ) ) {
        if ( ! empty( $wpdb->charset ) ) {
            $charset_collate .= "DEFAULT CHARACTER SET $wpdb->charset";
        }
        if ( ! empty( $wpdb->charset_collate ) ) {
            $charset_collate .= " COLLATE $wpdb->collate";
        }
    }
    
    //$charset_collate = $wpdb->get_charset_collate();

    $sql_table_transaction = "CREATE TABLE IF NOT EXISTS $table_transaction (
    control_id bigint(20) NOT NULL auto_increment,
    order_id varchar(50) NOT NULL,
    transaction_id bigint(20) NOT NULL,
    split_number int(11) NOT NULL,
    payment_method int(11) NOT NULL,
    token_transaction varchar(100) NOT NULL,
    url_payment varchar(200),
    typeful_line varchar(60),
    PRIMARY KEY (control_id)
) $charset_collate;";

   $sql_table_request = "CREATE TABLE IF NOT EXISTS $table_request (
    control_id bigint(20) NOT NULL auto_increment,
    request_params longtext NOT NULL,
    request_url varchar(100) NOT NULL DEFAULT '',
    date_request TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY  (control_id)
) $charset_collate;";

   $sql_table_response = "CREATE TABLE IF NOT EXISTS $table_response (
    control_id bigint(20) NOT NULL auto_increment,
    response_body longtext NOT NULL,
    response_url varchar(100) NOT NULL DEFAULT '',
    date_response TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY  (control_id)
) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql_table_transaction );
    dbDelta( $sql_table_request );
    dbDelta( $sql_table_response );

    add_option( 'woocommerce_yapay_intermediador_db_version', $woocommerce_yapay_intermediador_db_version );
}
