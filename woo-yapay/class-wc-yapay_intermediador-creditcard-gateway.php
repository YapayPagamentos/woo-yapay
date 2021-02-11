<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_Yapay_Intermediador_Creditcard_Gateway' ) ) :

/**
 * WooCommerce Yapay Intermediador main class.
 */
class WC_Yapay_Intermediador_Creditcard_Gateway extends WC_Payment_Gateway {
    
    function __construct($new = "N") {
        
        $version = "0.1.0";
        // The global ID for this Payment method
        $this->id = "wc_yapay_intermediador_cc";

        // The Title shown on the top of the Payment Gateways Page next to all the other Payment Gateways
        $this->method_title = __( "Yapay Intermediador - Cartões de Crédito", 'wc-yapay_intermediador-cc' );

        // The description for this Payment Gateway, shown on the actual Payment options page on the backend
        $this->method_description = __( "Plugin Yapay Intermediador para WooCommerce", 'wc-yapay_intermediador-cc' );

        // The title to be used for the vertical tabs that can be ordered top to bottom
        $this->title = __( "Yapay Intermediador", 'wc-yapay_intermediador-cc' );

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
        
        if ( $new == "N") {
            add_action( 'woocommerce_thankyou_' . $this->id, array( $this, 'thankyou_page' ) );
        }
        
        if ( is_admin() ) {
            add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
        } 

        wp_enqueue_style( 'yapay_intermediador-checkout', plugins_url( 'woo-yapay/assets/css/styles.css', plugin_dir_path( __FILE__ ) ), array(), $version );
        wp_enqueue_script( 'yapay_intermediador-checkout', plugins_url( 'woo-yapay/assets/js/yapay_intermediador.js', plugin_dir_path( __FILE__ ) ), array( ), $version, true );

    } // End __construct()
    
    // Build the administration fields for this specific Gateway
    public function init_form_fields() {
        add_thickbox();
        $payment_methods = array();
        
        // $payment_methods["2"] = "Diners Club International";
        $payment_methods["3"] = "Visa";
        $payment_methods["4"] = "Mastercard";
        $payment_methods["5"] = "American Express";
        $payment_methods["15"] = "Discovery";
        $payment_methods["16"] = "Elo";
        $payment_methods["18"] = "Aura";
        $payment_methods["19"] = "JCB";
        $payment_methods["20"] = "Hipercard";
        $payment_methods["25"] = "Hiper";
        
        $qtd_split = array("-","1","2","3","4","5","6","7","8","9","10","11","12");
        
        $this->form_fields = array(
            'enabled' => array(
                'title'     => __( 'Ativar / Desativar', 'wc-yapay_intermediador-cc' ),
                'label'     => __( 'Ativar Yapay Intermediador', 'wc-yapay_intermediador-cc' ),
                'type'      => 'checkbox',
                'default'   => 'no',
                'description'     => __( 'Ativar / Desativar pagamento por Yapay Intermediador', 'wc-yapay_intermediador-cc' ),
            ),
            'title' => array(
                'title'     => __( 'Titulo', 'wc-yapay_intermediador-cc' ),
                'type'      => 'text',
                'desc_tip'  => __( 'Titulo do meio de pagamento que os compradores visualizarão durante o processo de finalização de compra.', 'wc-yapay_intermediador-cc' ),
                'default'   => __( 'Yapay Intermediador - Cartões de Crédito', 'wc-yapay_intermediador-cc' ),
            ),
            'description' => array(
                'title'     => __( 'Descrição', 'wc-yapay_intermediador-cc' ),
                'type'      => 'textarea',
                'desc_tip'  => __( 'Descrição do meio de pagamento que os compradores visualizarão durante o processo de finalização de compra.', 'wc-yapay_intermediador-cc' ),
                'default'   => __( 'A maneira mais fácil e segura e comprar pela internet.', 'wc-yapay_intermediador-cc' ),
                'css'       => 'max-width:350px;'
            ),
            'environment' => array(
                'title'     => __( 'Sandbox', 'wc-yapay_intermediador-cc' ),
                'label'     => __( 'Ativar Sandbox', 'wc-yapay_intermediador-cc' ),
                'type'      => 'checkbox',
                'description' => __( 'Ativar / Desativar o ambiente de teste (sandbox)', 'wc-yapay_intermediador-cc' ),
                'default'   => 'no',
            ),

            // 'bt_config' => array(
            //     'title'             => __( 'Configuração Yapay Intermediador', 'wc-yapay_intermediador-cc' ),
            //     'type'              => 'text',
            //     'default'           => 'Configurar',
            //     'desc_tip'          => __( 'Clique no botão para configurar o Yapay Intermediador.', 'wc-yapay_intermediador-cc' ),
            //     'custom_attributes' => array('onclick'=>'window.open("http://developers.tray.com.br/authLoginWc.php?environment="+document.getElementById("woocommerce_wc_yapay_intermediador_cc_environment").checked+"&path='.  urlencode(get_site_url()) .'&type=cc", "", "width=650,height=550")'),
            //     'class' => 'button-primary',
            //     'css' => 'text-align:center'
            // ),

            // 'bt_config' => array(
            //     'title'             => __( 'Configuração Yapay Intermediador', 'wc-yapay_intermediador-cc' ),
            //     'type'              => 'text',
            //     'default'           => 'Configurar',
            //     'desc_tip'          => __( 'Clique no botão para configurar o Yapay Intermediador.', 'wc-yapay_intermediador-cc' ),
            //     'custom_attributes' => array('onclick'=>'window.open("http://dev.yapay.com.br/authLoginWc.php?environment="+document.getElementById("woocommerce_wc_yapay_intermediador_cc_environment").checked+"&path='.  urlencode(get_site_url()) .'&type=cc", "", "width=650,height=550")'),
            //     'class' => 'button-primary',
            //     'css' => 'text-align:center'
            // ),
            'token_account' => array(
                'title'     => __( 'Token da Conta', 'wc-yapay_intermediador-cc' ),
                'type'      => 'text',
                'desc_tip'  => __( 'Token de Integração utilizado para identificação da loja.', 'wc-yapay_intermediador-cc' ),
            ),
            'payment_methods' => array(
                'title'             => __( 'Meios de Pagamento Disponíveis', 'wc-yapay_intermediador-cc' ),
                'type'              => 'multiselect',
                'class'             => 'wc-enhanced-select',
                'css'               => 'width: 450px;',
                'default'           => array("2","3","4","5","15","16","18","19","20","25"),
                'description'       => __( 'Selecione todos os meios de pagamento disponíveis na loja.', 'wc-yapay_intermediador-cc' ),
                'options'           => $payment_methods,
                'desc_tip'          => true,
                'custom_attributes' => array(
                        'data-placeholder' => __( 'Selecione os meios de pagamento', 'wc-yapay_intermediador-cc' )
                )
            ),
            'max_qtd_split' => array(
                'title'     => __( 'Quantidade Máxima de Parcelas', 'wc-yapay_intermediador-cc' ),
                'type'      => 'select',
                'options'   => $qtd_split,
                'default'   => '12',
                'desc_tip'  => __( 'Quantidade máxima de parcelas que será disponibilizado ao comprador.', 'wc-yapay_intermediador-cc' ),
            ),
            'min_value_split' => array(
                'title'     => __( 'Valor Mínimo de Parcelas', 'wc-yapay_intermediador-cc' ),
                'type'      => 'text',
                'desc_tip'  => __( 'Valor mínimo de parcelas que será disponibilizado ao comprador. Valor mínimo R$10,00 (Inserir valor separado por "." Ex: 12.54)).', 'wc-yapay_intermediador-cc' ),
            ),
            'prefixo' => array(
                'title'     => __( 'Prefixo do Pedido', 'wc-yapay_intermediador-cc' ),
                'type'      => 'text',
                'desc_tip'  => __( 'Prefixo do pedido enviado para o Yapay Intermediador.', 'wc-yapay_intermediador-cc' ),
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
        
        $cart_total = 0;
        if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.1', '>=' ) ) {
                $order_id = absint( get_query_var( 'order-pay' ) );
        } else {
                $order_id = isset( $_GET['order_id'] ) ? absint( $_GET['order_id'] ) : 0;
        }

        // Gets order total from "pay for order" page.
        if ( 0 < $order_id ) {
                $order      = new WC_Order( $order_id );
                $cart_total = (float) $order->get_total();

        // Gets order total from cart/checkout.
        } elseif ( 0 < $woocommerce->cart->total ) {
                $cart_total = (float) $woocommerce->cart->total;
        }

        if ( $description = $this->get_description() ) {
                echo wpautop( wptexturize( $description ) );
        }
        
        
        wc_get_template( $this->id.'_form.php', array(
                'cart_total'           => $cart_total,
                'url_images'           => plugins_url( 'woo-yapay/assets/images/', plugin_dir_path( __FILE__ ) ),
                'payment_methods'      => $this->get_option("payment_methods"),
                'ta'                   => $this->get_option("token_account"),
                'ev'                   => $this->get_option("environment")
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
        
        // Finger
        $params["finger_print"] = $_POST["finger_print"];

        $params["token_account"] = $this->get_option("token_account");
		$params['transaction[free]']= "WOOCOMMERCE_INTERMEDIADOR_v0.6.1";
        $params["customer[name]"] = $_POST["billing_first_name"] . " " . $_POST["billing_last_name"];
        $params["customer[cpf]"] = $_POST["billing_cpf"];

        if ($_POST["billing_persontype"] == 2) {
            $params["customer[trade_name]"] = $_POST["billing_first_name"] . " " . $_POST["billing_last_name"];
            $params["customer[company_name]"] = $_POST["billing_company"];
            $params["customer[cnpj]"] = $_POST["billing_cnpj"];

            if ($_POST["yapay_cpfC"] == "")  {
                $params["customer[cpf]"] = $_POST["billing_cpf"];
            }
            else {
                $params["customer[cpf]"] = $_POST["yapay_cpfC"];
            }
        }  else {
            if (($_POST["billing_persontype"] == NULL) AND ($_POST["billing_cpf"] == NULL) ) {
                $params["customer[cpf]"] = $_POST["yapay_cpfC"];
                $params["customer[trade_name]"] = $_POST["billing_first_name"] . " " . $_POST["billing_last_name"];
                $params["customer[company_name]"] = $_POST["billing_company"];
                $params["customer[cnpj]"] = $_POST["billing_cnpj"];
            } 
        }  


        $params["customer[inscricao_municipal]"] = "";
        $params["customer[email]"] = $_POST["billing_email"];
        $params["customer[contacts][0][type_contact]"] = "H";
        $params["customer[contacts][0][number_contact]"] = $_POST["billing_phone"];

        if ($_POST["billing_cellphone"] != "") {
            $params["customer[contacts][1][type_contact]"] = "M";
            $params["customer[contacts][1][number_contact]"] = $_POST["billing_cellphone"];

        }
        
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
        
        // $params["transaction[customer_ip]"] = $order->customer_ip_address;
        // $params["transaction[customer_ip]"] = $_SERVER['HTTP_CF_CONNECTING_IP'];

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
        $params["transaction[max_split_transaction]"] = $_POST["wc-yapay_intermediador-cc_card_installments"];

        
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
        
        
   
        $card_expiry = explode("/",preg_replace('/\s/', "", $_POST["wc-yapay_intermediador-cc_card_expiry"]));

        $anoCartao = "20".$card_expiry[1];
        

        $params["payment[payment_method_id]"] = $_POST["wc-yapay_intermediador-cc-payment-method"];
        $params["payment[card_name]"] = $_POST["wc-yapay_intermediador-cc_card_holder_name"];
        $params["payment[card_number]"] = preg_replace('/\s/', "",$_POST["wc-yapay_intermediador-cc_card_number"]);
        $params["payment[card_expdate_month]"] = $card_expiry[0];
        $params["payment[card_expdate_year]"] = $anoCartao;//$card_expiry[1];
        $params["payment[card_cvv]"] = $_POST["wc-yapay_intermediador-cc_card_cvc"];
        
        $params["payment[split]"] = $_POST["wc-yapay_intermediador-cc_card_installments"];
        
        $tcRequest = new WC_Yapay_Intermediador_Request();

        $tcResponse = $tcRequest->requestData("v2/transactions/pay_complete",$params,$this->get_option("environment"),false);
                    
                
        if($tcResponse->message_response->message == "success"){
            // Remove cart.  
            include_once("includes/class-wc-yapay_intermediador-transactions.php");
            
            $transactionData = new WC_Yapay_Intermediador_Transactions();
            
            $transactionParams["order_id"] = (string)$tcResponse->data_response->transaction->order_number;
            $transactionParams["transaction_id"] = (int)$tcResponse->data_response->transaction->transaction_id;
            $transactionParams["split_number"] = (int)$tcResponse->data_response->transaction->payment->split;
            $transactionParams["payment_method"] = (int)$tcResponse->data_response->transaction->payment->payment_method_id;
            $transactionParams["token_transaction"] = (string)$tcResponse->data_response->transaction->token_transaction;

            
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
        global $wp;
        $order_id = $wp->query_vars['order-pay'];
        $order = new WC_Order( $order_id );

        $errors = array();
        if($_POST["wc-yapay_intermediador-cc-payment-method"] == ""){
            $errors[] = "<strong>Bandeira de Cartão</strong> não selecionada";
        }
        
        if($_POST["wc-yapay_intermediador-cc_card_holder_name"] == ""){
            $errors[] = "<strong>Nome do Titular</strong> não informado";
        }
            
        if(!$this->luhn(preg_replace('/\s/', "", $_POST["wc-yapay_intermediador-cc_card_number"]))){
            $errors[] = "<strong>Cartão de Crédito</strong> não informado ou inválido";
        }
        
        $card_expiry = explode("/",preg_replace('/\s/', "", $_POST["wc-yapay_intermediador-cc_card_expiry"]));
    
        echo $card_expiry;

        $anoCartao = "20".$card_expiry[1];

        if($anoCartao < date('Y') ){
            $errors[] = "<strong>Data de Vencimento do Cartão</strong> não informado ou inválido";
        }else if (($anoCartao == date('Y') ) && ($card_expiry[0] < date('m') ) ){
            $errors[] = "<strong>Data de Vencimento do Cartão</strong> não informado ou inválido";
        }
        
        if($_POST["wc-yapay_intermediador-cc_card_installments"] == "0"){
            $errors[] = "<strong>Quantidade de Parcelas</strong> não informado";
        }
    
        if($_POST["wc-yapay_intermediador-cc_card_installments"] == "0"){
            $errors[] = "<strong>Quantidade de Parcelas</strong> não informado";
        }        
        
        if($_POST["billing_phone"] == ""){
            $errors[] = "<strong>Telefone</strong> não informado";
        }


        if (count($errors)){
            $this->add_error($errors);         
            return false; 
        }
        
        return true; 
    }
    
    function luhn($ccNumberTc) {
        $sum = 0;
	foreach(array_reverse(str_split($ccNumberTc)) as $i => $num){
            $num = ($i % 2 != 0) ? intval($num) * 2 : $num;
            if($num > 9){
                $num = str_split(strval($num));
                $num = intval($num[0]) + intval($num[1]);
            }
            $sum += $num;
        }
	return ($sum % 10 == 0) ? true : false;
    }
    
    public function thankyou_page( $order_id ) {
        // global $woocommerce;

        $order        = new WC_Order( $order_id );
        // $request_data = $_POST;

        include_once("includes/class-wc-yapay_intermediador-transactions.php");
        include_once("includes/class-wc-yapay_intermediador-request.php");
          
        $transactionData = new WC_Yapay_Intermediador_Transactions();
       
        $tcTransaction = $transactionData->getTransactionByOrderId($this->get_option("prefixo").$order_id);

        $tcRequest = new WC_Yapay_Intermediador_Request();
      
        $params["token_account"] = $this->get_option("token_account");
        $params['price']= $order->get_total();
        $params['payment_method_id'] = $tcTransaction->payment_method;
        $tcResponse = $tcRequest->requestData("v1/seller_splits/simulate_split",$params,$this->get_option("environment"),false);

        $tcTransactionSplit = $tcResponse->data_response->splittings->splitting[$tcTransaction->split_number - 1];
      
        $html = "";
        $html .= "<ul class='order_details'>";
        $html .= "<li>";
        $html .= "Número da Transação: <strong>{$tcTransaction->transaction_id}</strong>";
        $html .= "</li>";
        $html .= "<li>";
        $strPaymentMethod = "";
        switch (intval($tcTransaction->payment_method)){
            // case 2: $strPaymentMethod = "Diners Club International";break;
            case 3: $strPaymentMethod = "Visa";break;
            case 4: $strPaymentMethod = "Mastercard";break;
            case 5: $strPaymentMethod = "American Express";break;
            case 15: $strPaymentMethod = "Discover";break;
            case 16: $strPaymentMethod = "Elo";break;
            case 18: $strPaymentMethod = "Aura";break;
            case 19: $strPaymentMethod = "JCB";break;
            case 20: $strPaymentMethod = "Hipercard";break;
            case 25: $strPaymentMethod = "Hiper (Itaú)";break;
        }
        $html .= "Bandeira de Cartão: <strong>$strPaymentMethod</strong>";
        $html .= "</li>";
        $html .= "<li>";
        $html .= "Pagamento em: <strong>".$tcTransactionSplit->split." x R$".number_format(floatval($tcTransactionSplit->value_split), 2, ',', '')."</strong>";
        $html .= "</li>";
        $html .= "</ul>";
        
        echo $html;

        $order->add_order_note( 'Pedido registrado no Yapay Intermediador. Transação: '.$tcTransaction->transaction_id );

        // if ($order->get_status() != 'processing') {
        //     $order->update_status( 'on-hold', 'Pedido registrado no Yapay Intermediador. Transação: '.$tcTransaction->transaction_id );
        // }
    }
    
}
endif;