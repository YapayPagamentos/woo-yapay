<?php

if ( ! class_exists( 'WC_Yapay_Intermediador_Request' ) ) :
    include_once("class-wc-yapay_intermediador-request.php");
endif;

if ( ! class_exists( 'WC_Yapay_Intermediador_Auth' ) ) :

/**
 * WooCommerce Yapay Intermediador main class.
 */
class WC_Yapay_Intermediador_Auth{
    
    public $urlAccessToken = "v1/authorizations/access_token";
    public $urlRefreshToken = "v1/authorizations/refresh";
    
    public $access_token = "";
    public $refresh_token = "";
    
    public function doAuthorization($consumer_key = "", $consumer_secret = "", $code = "",$environment = "yes")
    {
        
        $params["consumer_key"] = $consumer_key;
        $params["consumer_secret"] = $consumer_secret;
        $params["code"] = $code;
        
        $tcRequest = new WC_Yapay_Intermediador_Request();
        
        $tcResponse = $tcRequest->requestData($this->urlAccessToken,$params,$environment);
        
        if($tcResponse->message_response->message == "success"){
            $this->access_token = $tcResponse->data_response->authorization->access_token;
            $this->refresh_token = $tcResponse->data_response->authorization->refresh_token;
        }
    }
}
endif;