<?php

if ( ! class_exists( 'WC_Yapay_Intermediador_Requests' ) ) :

/**
 * WooCommerce Yapay_Intermediador main class.
 */

class WC_Yapay_Intermediador_Requests{
    
    public function getRequests() {
        global $wpdb;
        
        $requests = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}woocommerce_yapay_intermediador_request ORDER BY control_id DESC");
        
        return $requests;
        
    }
    
    public function convertRequestParams($oldParams) {
        $newParams = "";
        
        foreach ($oldParams as $key => $value) {
            $newParams .= $key."=".$value."\n";
        }
        return $newParams;
    }
    public function addRequest($param, $environment = "no") {
        global $wpdb;
        
        $param["request_params"] = $this->convertRequestParams($param["request_params"]);
        
        if($environment == "yes"){
            $retorno = $wpdb->insert($wpdb->prefix."woocommerce_yapay_intermediador_request",$param,array("%s","%s"));
        }
        
    }
}
endif;