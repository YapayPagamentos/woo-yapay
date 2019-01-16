<?php

if ( ! class_exists( 'WC_Yapay_Intermediador_Transactions' ) ) :

class WC_Yapay_Intermediador_Transactions{
    public function getTransactions() {
        global $wpdb;
        
        $responses = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}woocommerce_yapay_intermediador_transactions");
        
        return $responses;
        
    }
    
    public function getTransactionByOrderId($order_id) {
        global $wpdb;
        
        $responses = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}woocommerce_yapay_intermediador_transactions WHERE order_id = '$order_id'");
        
        return $responses;
        
    }
    
    public function getTransactionById($transaction_id) {
        global $wpdb;
        
        $responses = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}woocommerce_yapay_intermediador_transactions WHERE transaction_id = $transaction_id");
        
        return $responses;
        
    }
    
    public function getTransactionByToken($token_transaction) {
        global $wpdb;
        
        $responses = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}woocommerce_yapay_intermediador_transactions WHERE token_transaction = '$token_transaction'");
        
        return $responses;
        
    }
    
    public function addTransaction($param) {
        global $wpdb;
        
        $retorno = $wpdb->insert($wpdb->prefix."woocommerce_yapay_intermediador_transactions",$param);
        
        return $retorno;
    }
    
}
endif;