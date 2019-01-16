<?php

if ( ! class_exists( 'WC_Yapay_Intermediador_Request' ) ) :

/**
 * WooCommerce Yapay Intermediador main class.
 */
include_once("class-wc-yapay_intermediador-requests.php");    
include_once("class-wc-yapay_intermediador-responses.php");    
class WC_Yapay_Intermediador_Request{
    
    public function getUrlEnvironment($environment){
        return ($environment == 'yes') ? "https://api.sandbox.traycheckout.com.br/" : "https://api.traycheckout.com.br/";
    }
    
    public function requestData($pathPost, $dataRequest, $environment = "yes", $strResponse = false)
    {
        
        $urlPost = self::getUrlEnvironment($environment).$pathPost;
        
        $requestData = new WC_Yapay_Intermediador_Requests();
        
        $paramRequests["request_params"] = $dataRequest;
        $paramRequests["request_url"] = $urlPost;
        
        $requestData->addRequest($paramRequests,$environment);
        
        $ch = curl_init ( $urlPost );
        
        curl_setopt ( $ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1 );
        curl_setopt ( $ch, CURLOPT_POST, 1 );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $dataRequest);
        curl_setopt ( $ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2 );

        
        if (!($response = curl_exec($ch))) {
            //Mage::log('Error: Erro na execucao! ', null, 'traycheckout.log');
            if(curl_errno($ch)){
                //Mage::log('Error '.curl_errno($ch).': '. curl_error($ch), null, 'traycheckout.log');
            }else{
                //Mage::log('Error : '. curl_error($ch), null, 'traycheckout.log');
            }
            curl_close ( $ch );
            exit();    
        }
        
        $httpCode = curl_getinfo ( $ch, CURLINFO_HTTP_CODE );
        
        curl_close($ch); 
        
        $responseData = new WC_Yapay_Intermediador_Responses();
        
        $paramResponse["response_body"] = $response;
        $paramResponse["response_url"] = $urlPost;
        
        $responseData->addResponse($paramResponse,$environment);
        
        if(!$strResponse){
            $response = simplexml_load_string($response);
        }
                
        return $response;
    }
}
endif;