<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_Yapay_Intermediador_Tef_Gateway' ) ) :

/**
 * WooCommerce Yapay Intermediador main class.
 */
class WC_Yapay_Intermediador_Tef_Gateway extends WC_Payment_Gateway {
    
    function __construct() {

        $version = "0.1.0";
        // The global ID for this Payment method
        $this->id = "wc_yapay_intermediador_tef";

        // The Title shown on the top of the Payment Gateways Page next to all the other Payment Gateways
        $this->method_title = __( "Yapay Intermediador - Transferência Online", 'wc-yapay_intermediador-tef' );

        // The description for this Payment Gateway, shown on the actual Payment options page on the backend
        $this->method_description = __( "Plugin Yapay Intermediador para WooCommerce", 'wc-yapay_intermediador-tef' );

        // The title to be used for the vertical tabs that can be ordered top to bottom
        $this->title = __( "Yapay Intermediador", 'wc-yapay_intermediador-tef' );

        // If you want to show an image next to the gateway's name on the frontend, enter a URL to an image.
        $this->icon = null;

        // Bool. Can be set to true if you want payment fields to show on the checkout 
        // if doing a direct integration, which we are doing in this case
        $this->has_fields = true;

        // Supports the default credit card form
        $this->supports = array( 'default_credit_card_form' );

        // This basically defines your settings which are then loaded with init_settings()
        $this->init_form_fields();

        // After init_settings() is called, you can get the settings and load them into variables, e.g:
        // $this->title = $this->get_option( 'title' );
        $this->init_settings();

        // Turn these settings into variables we can use
        foreach ( $this->settings as $setting_key => $value ) {
            $this->$setting_key = $value;
        }

        add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
        add_action( 'woocommerce_thankyou_' . $this->id, array( $this, 'thankyou_page' ) );

        if ( is_admin() ) {
            add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
        }       
    } 
    
    // Build the administration fields for this specific Gateway
    public function init_form_fields() {
        add_thickbox();
        $payment_methods = array();
        
        $payment_methods["7"] = "Itaú Shopline (Transferência)";
        $payment_methods["14"] = "Peela";
        $payment_methods["21"] = "Transf. Online HSBC";
        $payment_methods["22"] = "Transf. Online Bradesco";
        $payment_methods["23"] = "Transf. Online Banco do Brasil";
        
        $this->form_fields = array(
            'enabled' => array(
                'title'     => __( 'Ativar / Desativar', 'wc-yapay_intermediador-tef' ),
                'label'     => __( 'Ativar Yapay Intermediador', 'wc-yapay_intermediador-tef' ),
                'type'      => 'checkbox',
                'default'   => 'no',
                'description'     => __( 'Ativar / Desativar pagamento por Yapay Intermediador', 'wc-yapay_intermediador-tef' ),
            ),
            'title' => array(
                'title'     => __( 'Titulo', 'wc-yapay_intermediador-tef' ),
                'type'      => 'text',
                'desc_tip'  => __( 'Titulo do meio de pagamento que os compradores visualizarão durante o processo de finalização de compra.', 'wc-yapay_intermediador-tef' ),
                'default'   => __( 'Yapay Intermediador - Transferência Online', 'wc-yapay_intermediador-tef' ),
            ),
            'description' => array(
                'title'     => __( 'Descrição', 'wc-yapay_intermediador-tef' ),
                'type'      => 'textarea',
                'desc_tip'  => __( 'Descrição do meio de pagamento que os compradores visualizarão durante o processo de finalização de compra.', 'wc-yapay_intermediador-tef' ),
                'default'   => __( 'A maneira mais fácil e segura e comprar pela internet.', 'wc-yapay_intermediador-tef' ),
                'css'       => 'max-width:350px;'
            ),
            'environment' => array(
                'title'     => __( 'Sandbox', 'wc-yapay_intermediador-tef' ),
                'label'     => __( 'Ativar Sandbox', 'wc-yapay_intermediador-tef' ),
                'type'      => 'checkbox',
                'description' => __( 'Ativar / Desativar o ambiente de teste (sandbox)', 'wc-yapay_intermediador-tef' ),
                'default'   => 'no',
            ),
            // 'bt_config' => array(
            //     'title'             => __( 'Configuração Yapay Intermediador', 'wc-yapay_intermediador-tef' ),
            //     'type'              => 'text',
            //     'default'           => 'Configurar',
            //     'desc_tip'          => __( 'Clique no botão para configurar o Yapay Intermediador.', 'wc-yapay_intermediador-tef' ),
            //     'custom_attributes' => array('onclick'=>'window.open("http://developers.tray.com.br/authLoginWc.php?environment="+document.getElementById("woocommerce_wc_yapay_intermediador_tef_environment").checked+"&path='.  urlencode(get_site_url()) .'&type=cc", "", "width=650,height=550")'),
            //     'class' => 'button-primary',
            //     'css' => 'text-align:center'
            // ),
            'token_account' => array(
                'title'     => __( 'Token da Conta', 'wc-yapay_intermediador-tef' ),
                'type'      => 'text',
                'desc_tip'  => __( 'Token de Integração utilizado para identificação da loja.', 'wc-yapay_intermediador-tef' ),
            ),
            'payment_methods' => array(
                    'title'             => __( 'Meios de Pagamento Disponíveis', 'wc-yapay_intermediador-tef' ),
                    'type'              => 'multiselect',
                    'class'             => 'wc-enhanced-select',
                    'css'               => 'width: 450px;',
                    'default'           => array("7","14","21","22","23"),
                    'description'       => __( 'Selecione todos os meios de pagamento disponíveis na loja.', 'wc-yapay_intermediador-tef' ),
                    'options'           => $payment_methods,
                    'desc_tip'          => true,
                    'custom_attributes' => array(
                            'data-placeholder' => __( 'Selecione os meios de pagamento', 'wc-yapay_intermediador-tef' )
                    )
            ),
            'prefixo' => array(
                'title'     => __( 'Prefixo do Pedido', 'wc-yapay_intermediador-tef' ),
                'type'      => 'text',
                'desc_tip'  => __( 'Prefixo do pedido enviado para o Yapay Intermediador.', 'wc-yapay_intermediador-tef' ),
            ),
            'consumer_key' => array(
                'type'      => 'hidden'
            ),
            'consumer_secret' => array(
                'type'      => 'hidden'
            )
        );      
    }
    
    public function payment_fields() {
        global $woocommerce;
        
        if ( $description = $this->get_description() ) {
                echo wpautop( wptexturize( $description ) );
        }
        
        wc_get_template( $this->id.'_form.php', array(
                'url_images'           => plugins_url( 'woo-yapay/assets/images/', plugin_dir_path( __FILE__ ) ),
                'payment_methods'      => $this->get_option("payment_methods")
        ), 'woocommerce/'.$this->id.'/', plugin_dir_path( __FILE__ ) . 'templates/' );
    }
    
    public function add_error( $messages ) {
        global $woocommerce;

        // Remove duplicate messages.
        $messages = array_unique( $messages );

        if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.1', '>=' ) ) {
            foreach ( $messages as $message ) {
                wc_add_notice( $message, 'error' );
            }
        } else {
            foreach ( $messages as $message ) {
                $woocommerce->add_error( $message );
            }
        }
    }
    
    /**
    * Get WooCommerce return URL.
    *
    * @return string
    */
    public function get_wc_request_url($order_id) {
        return get_site_url()."/?wc_yapay_intermediador_notification=1&order_id=$order_id";
    }
        
    public function process_payment( $order_id ) {
        global $woocommerce;

        include_once("includes/class-wc-yapay_intermediador-request.php");
        
        $order = new WC_Order( $order_id );
        
       
        $params["token_account"] = $this->get_option("token_account");
		$params['transaction[free]']= "WOOCOMMERCE_INTERMEDIADOR_v0.6.1";
        $params["customer[name]"] = $_POST["billing_first_name"] . " " . $_POST["billing_last_name"];
        $params["customer[cpf]"] = $_POST["billing_cpf"];

        if ($_POST["billing_persontype"] == 2) {
            $params["customer[trade_name]"] = $_POST["billing_first_name"] . " " . $_POST["billing_last_name"];
            $params["customer[company_name]"] = $_POST["billing_company"];
            $params["customer[cnpj]"] = $_POST["billing_cnpj"];

            if ($_POST["yapay_cpfT"] == "") {
                $params["customer[cpf]"] = $_POST["billing_cpf"];
            }
            else {
                $params["customer[cpf]"] = $_POST["yapay_cpfT"];
            }
        } else {
            if (($_POST["billing_persontype"] == NULL) AND ($_POST["billing_cpf"] == NULL) ) {
                $params["customer[cpf]"] = $_POST["yapay_cpfT"];
                $params["customer[trade_name]"] = $_POST["billing_first_name"] . " " . $_POST["billing_last_name"];
                $params["customer[company_name]"] = $_POST["billing_company"];
                $params["customer[cnpj]"] = $_POST["billing_cnpj"];
            } 
        }



        $params["customer[inscricao_municipal]"] = "";
        $params["customer[email]"] = $_POST["billing_email"];
        $params["customer[contacts][][type_contact]"] = "H";
        $params["customer[contacts][][number_contact]"] = $_POST["billing_phone"];
        
        $params["customer[addresses][0][type_address]"] = "B";
        $params["customer[addresses][0][postal_code]"] = $_POST["billing_postcode"];
        $params["customer[addresses][0][street]"] = $_POST["billing_address_1"];
        $params["customer[addresses][0][number]"] = $_POST["billing_number"];
        $params["customer[addresses][0][neighborhood]"] = $_POST["billing_neighborhood"];
        $params["customer[addresses][0][completion]"] = $_POST["billing_address_2"];
        $params["customer[addresses][0][city]"] = $_POST["billing_city"];
        $params["customer[addresses][0][state]"] = $_POST["billing_state"];
        
        if (isset($_POST["ship_to_different_address"])){
            if ($_POST["ship_to_different_address"]){
                $params["customer[addresses][1][type_address]"] = "D";
                $params["customer[addresses][1][postal_code]"] = $_POST["shipping_postcode"];
                $params["customer[addresses][1][street]"] = $_POST["shipping_address_1"];
                $params["customer[addresses][1][number]"] = $_POST["shipping_number"];
                $params["customer[addresses][1][neighborhood]"] = $_POST["shipping_neighborhood"];
                $params["customer[addresses][1][completion]"] = $_POST["shipping_address_2"];
                $params["customer[addresses][1][city]"] = $_POST["shipping_city"];
                $params["customer[addresses][1][state]"] = $_POST["shipping_state"];
            }else{
                $params["customer[addresses][1][type_address]"] = "D";
                $params["customer[addresses][1][postal_code]"] = $_POST["billing_postcode"];
                $params["customer[addresses][1][street]"] = $_POST["billing_address_1"];
                $params["customer[addresses][1][number]"] = $_POST["billing_number"];
                $params["customer[addresses][1][neighborhood]"] = $_POST["billing_neighborhood"];
                $params["customer[addresses][1][completion]"] = $_POST["billing_address_2"];
                $params["customer[addresses][1][city]"] = $_POST["billing_city"];
                $params["customer[addresses][1][state]"] = $_POST["billing_state"];
            }
        }else{
            $params["customer[addresses][1][type_address]"] = "D";
            $params["customer[addresses][1][postal_code]"] = $_POST["billing_postcode"];
            $params["customer[addresses][1][street]"] = $_POST["billing_address_1"];
            $params["customer[addresses][1][number]"] = $_POST["billing_number"];
            $params["customer[addresses][1][neighborhood]"] = $_POST["billing_neighborhood"];
            $params["customer[addresses][1][completion]"] = $_POST["billing_address_2"];
            $params["customer[addresses][1][city]"] = $_POST["billing_city"];
            $params["customer[addresses][1][state]"] = $_POST["billing_state"];
        }
        
        if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
          $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
        }

        $params["transaction[customer_ip]"] = $_SERVER['REMOTE_ADDR'];        

        $params["transaction[order_number]"] = $this->get_option("prefixo").$order_id;
        $shippingData = $order->get_shipping_methods();
        $shipping_type = "";
        foreach ($shippingData as $shipping){            
            $shipping_type .= $shipping["name"];
            if(count($shippingData) > 1){
                $shipping_type .= " / ";
            }
        }
        
        if($shipping_type != ""){
            $params["transaction[shipping_type]"] = $shipping_type;
            $params["transaction[shipping_price]"] = $order->order_shipping;
        }
        
        if (count($order->get_items('fee')) > 0) {
            add_filter ( 'additional_fees', 'yp_additional_fees', 10, 2  );
    
            function yp_additional_fees( $discount, $order ) {
                foreach( $order->get_items('fee') as $item_id => $item_fee ){
                    $fee_total = $item_fee->get_total();
                }
            
                if( $discount > 0 ) {
                    $total_fee = $discount + abs($fee_total);
                    return $total_fee;
                }
                return abs($fee_total);
            }


            $params["transaction[price_discount]"] = apply_filters( 'additional_fees', $order->discount_total, $order );


        } else if (intval($order->discount_total) > 0) {
            $params["transaction[price_discount]"] = $order->discount_total;
            
        } 

        // $params["transaction[price_discount]"] = $order->discount_total;
        $params["transaction[url_notification]"] = $this->get_wc_request_url($order_id);
        $params["transaction[available_payment_methods]"] = implode(",",$this->get_option("payment_methods"));
        
        if ( 0 < sizeof( $order->get_items() ) ) {
            $i = 0;
            foreach ($order->get_items() as $product) {
                $params["transaction_product[$i][code]"] = $product["product_id"];
                $params["transaction_product[$i][description]"] = $product['name'];
                $params["transaction_product[$i][price_unit]"] = $order->get_item_subtotal( $product, false ) ;
                $params["transaction_product[$i][quantity]"] = $product['qty'];
                $i++;
            }
        }
        
        $params["payment[payment_method_id]"] = $_POST["wc-yapay_intermediador-tef-payment-method"];
        $params["payment[split]"] = "1";
        
        $tcRequest = new WC_Yapay_Intermediador_Request();
        
        $tcResponse = $tcRequest->requestData("v2/transactions/pay_complete",$params,$this->get_option("environment"),false);

        if($tcResponse->message_response->message == "success"){
            // Remove cart.  
            include_once("includes/class-wc-yapay_intermediador-transactions.php");
            
            $transactionData = new WC_Yapay_Intermediador_Transactions();
            
            $transactionParams["order_id"] = (string)$tcResponse->data_response->transaction->order_number;
            $transactionParams["transaction_id"] = (int)$tcResponse->data_response->transaction->transaction_id;
            $transactionParams["split_number"] = (int)$tcResponse->data_response->transaction->order_number;
            $transactionParams["payment_method"] = (int)$tcResponse->data_response->transaction->payment->payment_method_id;
            $transactionParams["token_transaction"] = (string)$tcResponse->data_response->transaction->token_transaction;
            $transactionParams["url_payment"] = (string)$tcResponse->data_response->transaction->payment->url_payment;
            //$transactionParams["typeful_line"] = (string)$tcResponse->data_response->transaction->payment->linha_digitavel;
            
            $transactionData->addTransaction($transactionParams);
            
            if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.1', '>=' ) ) {
                WC()->cart->empty_cart();
            } else {
                $woocommerce->cart->empty_cart();
            }
            if(!isset($use_shipping)){
                $use_shipping = isset($use_shipping);
            }
            if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.1', '>=' ) ) {
                return array(
                    'result'   => 'success',
                    'redirect' => $this->get_return_url( $order )
                    // 'redirect' => add_query_arg( array( 'use_shipping' => $use_shipping ), $order->get_checkout_payment_url( true ) )
                );
            } else {
                return array(
                    'result'   => 'success',
                    'redirect' => $this->get_return_url( $order )
                    // 'redirect' => add_query_arg( array( 'order' => $order->id, 'key' => $order->order_key, 'use_shipping' => $use_shipping ), get_permalink( woocommerce_get_page_id( 'pay' ) ) )
                );
            }

        }else{
            $errors = array();
            if(isset($tcResponse->error_response->general_errors)){
                foreach ($tcResponse->error_response->general_errors->general_error as $error){
                    $errors[] = "<strong>Código:</strong> ".$error->code ." | <strong>Mensagem:</strong> ".$error->message;
                }
            }else if(isset($tcResponse->error_response->validation_errors)){
                foreach ($tcResponse->error_response->validation_errors->validation_error as $error){
                    $errors[] = "<strong>Mensagem:</strong> ".$error->message_complete;
                }
            }else{
                $errors[] = "<strong>Código:</strong> 9999 | <strong>Mensagem:</strong> Não foi possível finalizar o pedido. Tente novamente mais tarde!";
            }
            $this->add_error($errors);
        }
    }
    
    public function validate_fields() { 
        $errors = array();
        if($_POST["wc-yapay_intermediador-tef-payment-method"] == ""){
            $errors[] = "<strong>Tipo de Transferência</strong> não selecionada";
        }
        if (count($errors)){
            $this->add_error($errors);
        }
        return true; 
    }
    
    public function thankyou_page( $order_id ) {
        global $woocommerce;

        $order        = new WC_Order( $order_id );
        $request_data = $_POST;
        
        include_once("includes/class-wc-yapay_intermediador-transactions.php");
             
        $transactionData = new WC_Yapay_Intermediador_Transactions();
        
        $tcTransaction = $transactionData->getTransactionByOrderId($this->get_option("prefixo").$order_id);
        $html = "";
        $html .= "<ul class='order_details'>";
        $html .= "<li>";
        $html .= "Número da Transação:<strong>{$tcTransaction->transaction_id}</strong>";
        $html .= "</li>";
        $html .= "<li>";
        $strPaymentMethod = "";
        switch (intval($tcTransaction->payment_method)){
            case 7: $strPaymentMethod = "Itaú";break;
            case 14: $strPaymentMethod = "Peela";break;
            case 21: $strPaymentMethod = "HSBC";break;
            case 22: $strPaymentMethod = "Bradesco";break;
            case 23: $strPaymentMethod = "Banco do Brasil";break;
        }
        $html .= "Transferência Online:<strong>$strPaymentMethod</strong>";
        $html .= "</li>";
        $html .= "<li>";
        $html .= "<br><br>";
        $html .= "<a href='{$tcTransaction->url_payment}' target='_blank' class='button'>Efetuar Transferência Online</a>";
        $html .= "</li>";
        $html .= "</ul>";
 
        
        echo $html;
        
        $order->add_order_note( 'Pedido registrado no Yapay Intermediador. Transação: '.$tcTransaction->transaction_id );
        
        // if ($order->get_status() != 'processing' ) {
        //     $order->update_status( 'on-hold', 'Pedido registrado no Yapay Intermediador. Transação: '.$tcTransaction->transaction_id );
        // }
    }
}
endif;