<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( class_exists( 'WC_Yapay_Intermediador_Bolepix_Gateway' ) ) return;

/**
 * WooCommerce Vindi Intermediador main class.
 */
class WC_Yapay_Intermediador_Bolepix_Gateway extends WC_Payment_Gateway {

    function __construct() {

        $this->id = "wc_yapay_intermediador_bolepix";
        $this->method_title = __( "Vindi Intermediador - Bolepix", 'wc-yapay_intermediador-bolepix' );
        $this->method_description = __( "Plugin Vindi Intermediador para WooCommerce", 'wc-yapay_intermediador-bolepix' );
        $this->title = __( "Vindi Intermediador", 'wc-yapay_intermediador-bolepix' );

        if ($this->get_option('show_icon') === 'yes') {
            $this->icon = plugins_url( 'woo-yapay/assets/images/', plugin_dir_path( __FILE__ ) ) . "bolepix-flag.svg";
        }

        $this->has_fields = true;
        $this->supports = array( 'default_credit_card_form' );

        $this->init_form_fields();

        $this->init_settings();

        foreach ( $this->settings as $setting_key => $value ) {
            $this->$setting_key = $value;
        }

        add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
        add_action( 'woocommerce_thankyou_' . $this->id, array( $this, 'thankyou_page' ) );
        add_action( 'woocommerce_order_details_after_order_table', [ $this, 'add_yapay_order_details' ], 10, 1 );

        if ( is_admin() ) {
            add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
        }
    }

    // Build the administration fields for this specific Gateway
    public function init_form_fields() {
        add_thickbox();

        $this->form_fields = array(
            'enabled' => array(
                'title'     => __( 'Ativar / Desativar', 'wc-yapay_intermediador-bolepix' ),
                'label'     => __( 'Ativar Vindi Intermediador', 'wc-yapay_intermediador-bolepix' ),
                'type'      => 'checkbox',
                'default'   => 'no',
                'description'     => __( 'Ativar / Desativar pagamento por Vindi Intermediador', 'wc-yapay_intermediador-bolepix' ),
            ),
            'title' => array(
                'title'     => __( 'Titulo', 'wc-yapay_intermediador-bolepix' ),
                'type'      => 'text',
                'desc_tip'  => __( 'Titulo do meio de pagamento que os compradores visualizarão durante o processo de finalização de compra.', 'wc-yapay_intermediador-bolepix' ),
                'default'   => __( 'Vindi Intermediador - Bolepix', 'wc-yapay_intermediador-bolepix' ),
            ),
            'description' => array(
                'title'     => __( 'Descrição', 'wc-yapay_intermediador-bolepix' ),
                'type'      => 'textarea',
                'desc_tip'  => __( 'Descrição do meio de pagamento que os compradores visualizarão durante o processo de finalização de compra.', 'wc-yapay_intermediador-bolepix' ),
                'default'   => __( 'A maneira mais fácil e segura e comprar pela internet.', 'wc-yapay_intermediador-bolepix' ),
                'css'       => 'max-width:350px;'
            ),
            'environment' => array(
                'title'     => __( 'Sandbox', 'wc-yapay_intermediador-bolepix' ),
                'label'     => __( 'Ativar Sandbox', 'wc-yapay_intermediador-bolepix' ),
                'type'      => 'checkbox',
                'description' => __( 'Ativar / Desativar o ambiente de teste (sandbox)', 'wc-yapay_intermediador-bolepix' ),
                'default'   => 'no',
            ),
            'not_require_cpf' => array(
                'title'     => __('Não exibir campo de CPF', 'wc-yapay_intermediador-cc'),
                'label'     => __('Não exibir campo de CPF no checkout para compras realizadas com o CNPJ', 'wc-yapay_intermediador-cc'),
                'type'      => 'checkbox',
                'description' => __('Atenção! Para marcar essa opção é necessário entrar em contato com o suporte da Vindi previamente', 'wc-yapay_intermediador-cc'),
                'default'   => 'no',
            ),
            'show_icon' => array(
                'title'     => __( 'Mostrar ícone no checkout', 'wc-yapay_intermediador-cc' ),
                'label'     => __( 'Ativar ícone', 'wc-yapay_intermediador-cc' ),
                'type'      => 'checkbox',
                'description' => __( 'Ativar / Desativar o ícone do método de pagamento no checkout', 'wc-yapay_intermediador-cc' ),
                'default'   => 'on',
            ),
            'token_account' => array(
                'title'     => __( 'Token da Conta', 'wc-yapay_intermediador-bolepix' ),
                'type'      => 'text',
                'desc_tip'  => __( 'Token de Integração utilizado para identificação da loja.', 'wc-yapay_intermediador-bolepix' ),
            ),
            'prefixo' => array(
                'title'     => __( 'Prefixo do Pedido', 'wc-yapay_intermediador-bolepix' ),
                'type'      => 'text',
                'desc_tip'  => __( 'Prefixo do pedido enviado para o Vindi Intermediador.', 'wc-yapay_intermediador-bolepix' ),
            ),
            'reseller_token' => array(
                'title'       => __('Reseller Token (Opcional)', 'wc-yapay_intermediador-cc'),
                'type'        => 'text',
                'description' => __('Configurar este campo, apenas quando direcionado pelo seu consultor comercial ou pela sua agência de desenvolvimento.', 'wc-yapay_intermediador-cc'),
                'desc_tip'    => __('Preencha este campo com o reseller token da sua conta.', 'wc-yapay_intermediador-cc'),
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
                'url_images'      => plugins_url( 'woo-yapay/assets/images/', plugin_dir_path( __FILE__ ) ),
                'not_require_cpf' => $this->get_option("not_require_cpf"),
                'enviroment'      => $this->get_option("environment") === 'yes' ? 'sandbox' : 'production'
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


        $reseller_token = $this->get_option("reseller_token");

        if ($reseller_token) {
            $params["reseller_token"] = $reseller_token;
        }
        
        $params["token_account"] = $this->get_option("token_account");
        $params["finger_print"] = $_POST["finger_print"];
		    $params['transaction[free]']= "WOOCOMMERCE_INTERMEDIADOR_v0.7.6";
        $params["customer[name]"] = substr($_POST["billing_first_name"] . " " . $_POST["billing_last_name"], 0 , 50);
        $params["customer[cpf]"] = $_POST["billing_cpf"];

        if ( !isset($_POST["billing_persontype"]) && !isset($_POST["billing_cpf"]) || $_POST["billing_persontype"] == 2 ) {
            $params["customer[trade_name]"] = substr($_POST["billing_first_name"] . " " . $_POST["billing_last_name"], 0 , 50);
            $params["customer[company_name]"] = substr($_POST["billing_company"], 0 , 50);
            $params["customer[cnpj]"] = $_POST["billing_cnpj"];

            if (isset( $_POST["yapay_cpfBP"]) && $_POST["yapay_cpfBP"] !== "" ) {
                $params["customer[cpf]"] = $_POST["yapay_cpfBP"];
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
            $params["transaction[shipping_price]"] = $order->get_shipping_total();
        }

        $discount = 0;
        $fee      = 0;

        if (count($order->get_items('fee')) > 0) {

            foreach( $order->get_items('fee') as $item_id => $item_fee ){
                $fee_total = floatval( $item_fee->get_total() );

                if ( $fee_total > 0 ) {
                    $fee += $fee_total;
                } else {
                    $discount += $fee_total * -1;
                }
            }
        }

        $discount += floatval( $order->get_total_discount() );

        if ( $discount > 0 ) {
            $params["transaction[price_discount]"] = $discount;
        }

        if ( $fee > 0 ) {
            $params["transaction[fee]"] = $fee;
        }


        $params["transaction[url_notification]"] = $this->get_wc_request_url($order_id);
        $params["transaction[available_payment_methods]"] = "28";

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

        $params["payment[payment_method_id]"] = $_POST["wc-yapay_intermediador-bolepix-payment-method"];
        $params["payment[split]"] = "1";

        $tcRequest = new WC_Yapay_Intermediador_Request();

        $tcResponse = $tcRequest->requestData("v2/transactions/pay_complete",$params,$this->get_option("environment"),false);
        if($tcResponse->message_response->message == "success"){

            $transactionParams["order_id"]             = (string)$tcResponse->data_response->transaction->order_number;
            $transactionParams["transaction_id"]       = (int)$tcResponse->data_response->transaction->transaction_id;
            $transactionParams["split_number"]         = (string)$tcResponse->data_response->transaction->order_number;
            $transactionParams["payment_method"]       = (int)$tcResponse->data_response->transaction->payment->payment_method_id;
            $transactionParams["token_transaction"]    = (string)$tcResponse->data_response->transaction->token_transaction;
            $transactionParams["url_payment"]          = (string)$tcResponse->data_response->transaction->payment->url_payment;
            $transactionParams["qrcode_path"]          = (string)$tcResponse->data_response->transaction->payment->qrcode_path;
            $transactionParams["qrcode_original_path"] = (string)$tcResponse->data_response->transaction->payment->qrcode_original_path;
            $transactionParams["url_payment"]          = (string)$tcResponse->data_response->transaction->payment->url_payment;
            $transactionParams["typeful_line"]         = (string)$tcResponse->data_response->transaction->payment->linha_digitavel;

            $order->update_meta_data('yapay_transaction_data', serialize($transactionParams));
            $order->save();

            $log = new WC_Logger();
            $log->add(
                "yapay-intermediador-transactions-save-",
                "VINDI NEW TRANSACTION SAVE : \n" .
                    print_r($transactionParams, true) . "\n\n"
            );

            $order->update_status( "on-hold", "Vindi Intermediador enviou automaticamente o status: \n | Bolepix copia e cola: ". $transactionParams["qrcode_original_path"] );

            if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.1', '>=' ) ) {
                WC()->cart->empty_cart();
            } else {
                $woocommerce->cart->empty_cart();
            }
            if(!isset($use_shipping)){
                $use_shipping = isset($use_shipping);
            }

            return array(
                'result'   => 'success',
                'redirect' => $this->get_return_url( $order )
            );

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

        return true;
    }

    public function thankyou_page( $order_id ) {
        $order     = new WC_Order( $order_id );
        $this->add_yapay_order_details($order);


    }


    public function add_yapay_order_details( $order )
    {
        if ( array_intersect( [ 'wc_yapay_intermediador_bolepix' ], [ $order->get_payment_method() ] ) ) {

            $order_id = $order->get_id();

            $dados = $this->get_meta_data( $order_id );
            $dados['url_image'] = (WP_PLUGIN_URL . '/'. str_replace( basename( __FILE__ ), "", plugin_basename(__FILE__) ));
            
            extract($dados);
            ob_start();

            require __DIR__ . '/templates/orders/wc_yapay_intermediador_bolepix_order.php';

            $html = ob_get_clean();

            echo $html;
        }
    }

    private function get_meta_data( $order_id )
    {
		$order = wc_get_order($order_id);
        $data = $order->get_meta('yapay_transaction_data', true);

        if ( is_serialized( $data ) ) {
            $data = unserialize( $data );
            if ( isset( $data['transaction_id'] ) && $data['transaction_id'] ) {
                return $data;
            }
        }
    }
}

