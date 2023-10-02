<?php

if ( ! class_exists( 'WC_Yapay_Intermediador_Responses' ) ) :

class WC_Yapay_Intermediador_Responses{
    public function getResponses() {
        global $wpdb;
        
        $responses = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}woocommerce_yapay_intermediador_response ORDER BY control_id DESC");
        
        return $responses;
        
    }
    
    public function convertResponseParams($oldParams) {
        $newParams = "";
        
        foreach ($oldParams as $key => $value) {
            $newParams .= $key."=".$value."\n";
        }
        return $newParams;
    }
    
    public function addResponse($param,$environment = "no") {
        global $wpdb;
        
        if($environment == "yes"){
            $retorno = $wpdb->insert($wpdb->prefix."woocommerce_yapay_intermediador_response",$param,array("%s","%s"));
        }
    }
    
}

endif;