<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!class_exists('WC_Yapay_Intermediador_Creditcard_Gateway')) :

    /**
     * WooCommerce Vindi Intermediador main class.
     */
    class WC_Yapay_Intermediador_Creditcard_Gateway extends WC_Payment_Gateway
    {

        function __construct($new = "N")
        {

            $version = "0.1.0";
            // The global ID for this Payment method
            $this->id = "wc_yapay_intermediador_cc";

            // The Title shown on the top of the Payment Gateways Page next to all the other Payment Gateways
            $this->method_title = __("Vindi Intermediador - Cartões de Crédito", 'wc-yapay_intermediador-cc');

            // The description for this Payment Gateway, shown on the actual Payment options page on the backend
            $this->method_description = __("Plugin Vindi Intermediador para WooCommerce", 'wc-yapay_intermediador-cc');

            // The title to be used for the vertical tabs that can be ordered top to bottom
            $this->title = __("Vindi Intermediador", 'wc-yapay_intermediador-cc');

            // If you want to show an image next to the gateway's name on the frontend, enter a URL to an image.
            if ($this->get_option('show_icon') === 'yes') {
                $this->icon = plugins_url('woo-yapay/assets/images/', plugin_dir_path(__FILE__)) . "cc-flag.svg";
            }

            // Bool. Can be set to true if you want payment fields to show on the checkout
            // if doing a direct integration, which we are doing in this case
            $this->has_fields = true;

            // Supports the default credit card form
            $this->supports = array('default_credit_card_form');

            // This basically defines your settings which are then loaded with init_settings()
            $this->init_form_fields();

            // After init_settings() is called, you can get the settings and load them into variables, e.g:
            // $this->title = $this->get_option( 'title' );
            $this->init_settings();

            // Turn these settings into variables we can use
            foreach ($this->settings as $setting_key => $value) {
                $this->$setting_key = $value;
            }

            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));

            if ($new == "N") {
                add_action('woocommerce_thankyou_' . $this->id, array($this, 'thankyou_page'));
            }

            if (is_admin()) {
                add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
            }

        } // End __construct()

        // Build the administration fields for this specific Gateway
        public function init_form_fields()
        {
            add_thickbox();
            $payment_methods = array();

            $payment_methods["3"] = "Visa";
            $payment_methods["4"] = "Mastercard";
            $payment_methods["5"] = "American Express";
            $payment_methods["16"] = "Elo";
            $payment_methods["19"] = "JCB";
            $payment_methods["20"] = "Hipercard";
            $payment_methods["25"] = "Hiper";

            $qtd_split = array("-", "1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12");

            $this->form_fields = array(
                'enabled' => array(
                    'title'     => __('Ativar / Desativar', 'wc-yapay_intermediador-cc'),
                    'label'     => __('Ativar Vindi Intermediador', 'wc-yapay_intermediador-cc'),
                    'type'      => 'checkbox',
                    'default'   => 'no',
                    'description'     => __('Ativar / Desativar pagamento por Vindi Intermediador', 'wc-yapay_intermediador-cc'),
                ),
                'title' => array(
                    'title'     => __('Titulo', 'wc-yapay_intermediador-cc'),
                    'type'      => 'text',
                    'desc_tip'  => __('Titulo do meio de pagamento que os compradores visualizarão durante o processo de finalização de compra.', 'wc-yapay_intermediador-cc'),
                    'default'   => __('Vindi - Cartões de Crédito', 'wc-yapay_intermediador-cc'),
                ),
                'description' => array(
                    'title'     => __('Descrição', 'wc-yapay_intermediador-cc'),
                    'type'      => 'textarea',
                    'desc_tip'  => __('Descrição do meio de pagamento que os compradores visualizarão durante o processo de finalização de compra.', 'wc-yapay_intermediador-cc'),
                    'default'   => __('A maneira mais fácil e segura de comprar pela internet.', 'wc-yapay_intermediador-cc'),
                    'css'       => 'max-width:350px;'
                ),
                'environment' => array(
                    'title'     => __('Sandbox', 'wc-yapay_intermediador-cc'),
                    'label'     => __('Ativar Sandbox', 'wc-yapay_intermediador-cc'),
                    'type'      => 'checkbox',
                    'description' => __('Ativar / Desativar o ambiente de teste (sandbox)', 'wc-yapay_intermediador-cc'),
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
                    'title'     => __('Mostrar ícone no checkout', 'wc-yapay_intermediador-cc'),
                    'label'     => __('Ativar ícone', 'wc-yapay_intermediador-cc'),
                    'type'      => 'checkbox',
                    'description' => __('Ativar / Desativar o ícone do método de pagamento no checkout', 'wc-yapay_intermediador-cc'),
                    'default'   => 'on',
                ),
                'token_account' => array(
                    'title'     => __('Token da Conta', 'wc-yapay_intermediador-cc'),
                    'type'      => 'text',
                    'desc_tip'  => __('Token de Integração utilizado para identificação da loja.', 'wc-yapay_intermediador-cc'),
                ),
                'payment_methods' => array(
                    'title'             => __('Meios de Pagamento Disponíveis', 'wc-yapay_intermediador-cc'),
                    'type'              => 'multiselect',
                    'class'             => 'wc-enhanced-select',
                    'css'               => 'width: 450px;',
                    'default'           => array_keys($payment_methods),
                    'description'       => __('Selecione todos os meios de pagamento disponíveis na loja.', 'wc-yapay_intermediador-cc'),
                    'options'           => $payment_methods,
                    'desc_tip'          => true,
                    'custom_attributes' => array(
                        'data-placeholder' => __('Selecione os meios de pagamento', 'wc-yapay_intermediador-cc')
                    )
                ),
                'max_qtd_split' => array(
                    'title'     => __('Quantidade Máxima de Parcelas', 'wc-yapay_intermediador-cc'),
                    'type'      => 'select',
                    'options'   => $qtd_split,
                    'default'   => '12',
                    'desc_tip'  => __('Quantidade máxima de parcelas que será disponibilizado ao comprador.', 'wc-yapay_intermediador-cc'),
                ),
                'min_value_split' => array(
                    'title'     => __('Valor Mínimo de Parcelas', 'wc-yapay_intermediador-cc'),
                    'type'      => 'text',
                    'desc_tip'  => __('Valor mínimo de parcelas que será disponibilizado ao comprador. Valor mínimo R$10,00 (Inserir valor separado por "." Ex: 12.54)).', 'wc-yapay_intermediador-cc'),
                ),
                'show_installments_fees' => array(
                    'title'     => __('Exibição de texto nas parcelas', 'wc-yapay_intermediador-cc'),
                    'type'      => 'select',
                    'options'   => [
                        'show_fee_text_price' => 'Exibir texto e valor total da parcela',
                        'show_fee_text'  => 'Exibir somente o texto',
                        'show_fee_price' => 'Exibir valor total da parcela',
                        'not_show'       => 'Não exibir'
                    ],
                    'default'   => 'show_fee_price',
                    'desc_tip'  => __('Forma de exibição das parcelas no checkout', 'wc-yapay_intermediador-cc'),
                ),
                'prefixo' => array(
                    'title'     => __('Prefixo do Pedido', 'wc-yapay_intermediador-cc'),
                    'type'      => 'text',
                    'desc_tip'  => __('Prefixo do pedido enviado para o Vindi Intermediador.', 'wc-yapay_intermediador-cc'),
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

        public function payment_fields()
        {
            global $woocommerce;

            $cart_total = 0;
            if (defined('WC_VERSION') && version_compare(WC_VERSION, '2.1', '>=')) {
                $order_id = absint(get_query_var('order-pay'));
            } else {
                $order_id = isset($_GET['order_id']) ? absint($_GET['order_id']) : 0;
            }

            // Gets order total from "pay for order" page.
            if (0 < $order_id) {
                $order      = new WC_Order($order_id);
                $cart_total = (float) $order->get_total();

                // Gets order total from cart/checkout.
            } elseif (0 < $woocommerce->cart->total) {
                $cart_total = (float) $woocommerce->cart->total;
            }

            if ($description = $this->get_description()) {
                echo wpautop(wptexturize($description));
            }


            wc_get_template($this->id . '_form.php', array(
                'cart_total'           => $cart_total,
                'url_images'           => plugins_url('woo-yapay/assets/images/', plugin_dir_path(__FILE__)),
                'payment_methods'      => $this->get_option("payment_methods"),
                'ta'                   => $this->get_option("token_account"),
                'enviroment'           => $this->get_option("environment") === 'yes' ? 'sandbox' : 'production',
                'not_require_cpf'      => $this->get_option("not_require_cpf")
            ), 'woocommerce/' . $this->id . '/', plugin_dir_path(__FILE__) . 'templates/');
        }

        public function add_error($messages)
        {
            global $woocommerce;

            // Remove duplicate messages.
            $messages = array_unique($messages);

            if (defined('WC_VERSION') && version_compare(WC_VERSION, '2.1', '>=')) {
                foreach ($messages as $message) {
                    wc_add_notice($message, 'error');
                }
            } else {
                foreach ($messages as $message) {
                    $woocommerce->add_error($message);
                }
            }
        }

        /**
         * Get WooCommerce return URL.
         *
         * @return string
         */
        public function get_wc_request_url($order_id)
        {
            return get_site_url() . "/?wc_yapay_intermediador_notification=1&order_id=$order_id";
        }

        public function process_payment($order_id)
        {
            global $woocommerce;

            include_once("includes/class-wc-yapay_intermediador-request.php");

            $order = new WC_Order($order_id);

            $reseller_token = $this->get_option("reseller_token");

            if ($reseller_token) {
                $params["reseller_token"] = $reseller_token;
            }
            
            $params["token_account"] = $this->get_option("token_account");
            $params["finger_print"] = $_POST["finger_print"];
            $params['transaction[free]'] = "WOOCOMMERCE_INTERMEDIADOR_v0.7.6";
            $params["customer[name]"] = $_POST["billing_first_name"] . " " . $_POST["billing_last_name"];
			$params["customer[cpf]"] = $_POST["billing_cpf"];

            if (!isset($_POST["billing_persontype"]) && !isset($_POST["billing_cpf"]) || $_POST["billing_persontype"] == 2) {
                $params["customer[trade_name]"] = $_POST["billing_first_name"] . " " . $_POST["billing_last_name"];
                $params["customer[company_name]"] = $_POST["billing_company"];
                $params["customer[cnpj]"] = $_POST["billing_cnpj"];

                if ($_POST["yapay_cpfC"] !== "") {
                    $params["customer[cpf]"] = $_POST["yapay_cpfC"];
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

            if (isset($_POST["ship_to_different_address"])) {
                if ($_POST["ship_to_different_address"]) {
                    $params["customer[addresses][1][type_address]"] = "D";
                    $params["customer[addresses][1][postal_code]"] = $_POST["shipping_postcode"];
                    $params["customer[addresses][1][street]"] = $_POST["shipping_address_1"];
                    $params["customer[addresses][1][number]"] = $_POST["shipping_number"];
                    $params["customer[addresses][1][neighborhood]"] = $_POST["shipping_neighborhood"];
                    $params["customer[addresses][1][completion]"] = $_POST["shipping_address_2"];
                    $params["customer[addresses][1][city]"] = $_POST["shipping_city"];
                    $params["customer[addresses][1][state]"] = $_POST["shipping_state"];
                } else {
                    $params["customer[addresses][1][type_address]"] = "D";
                    $params["customer[addresses][1][postal_code]"] = $_POST["billing_postcode"];
                    $params["customer[addresses][1][street]"] = $_POST["billing_address_1"];
                    $params["customer[addresses][1][number]"] = $_POST["billing_number"];
                    $params["customer[addresses][1][neighborhood]"] = $_POST["billing_neighborhood"];
                    $params["customer[addresses][1][completion]"] = $_POST["billing_address_2"];
                    $params["customer[addresses][1][city]"] = $_POST["billing_city"];
                    $params["customer[addresses][1][state]"] = $_POST["billing_state"];
                }
            } else {
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

            $params["transaction[order_number]"] = $this->get_option("prefixo") . $order_id;

            $shippingData = $order->get_shipping_methods();
            $shipping_type = "";
            foreach ($shippingData as $shipping) {
                $shipping_type .= $shipping["name"];
                if (count($shippingData) > 1) {
                    $shipping_type .= " / ";
                }
            }

            if ($shipping_type != "") {
                $params["transaction[shipping_type]"] = $shipping_type;
                $params["transaction[shipping_price]"] = $order->get_shipping_total();
            }

            $discount = 0;
            $fee      = 0;

            if (count($order->get_items('fee')) > 0) {

                foreach ($order->get_items('fee') as $item_id => $item_fee) {
                    $fee_total = floatval($item_fee->get_total());

                    if ($fee_total > 0) {
                        $fee += $fee_total;
                    } else {
                        $discount += $fee_total * -1;
                    }
                }
            }

            $discount += floatval($order->get_total_discount());

            if ($discount > 0) {
                $params["transaction[price_discount]"] = $discount;
            }

            if ($fee > 0) {
                $params["transaction[fee]"] = $fee;
            }

            $params["transaction[url_notification]"] = $this->get_wc_request_url($order_id);
            $params["transaction[available_payment_methods]"] = implode(",", $this->get_option("payment_methods"));
            $params["transaction[max_split_transaction]"] = $_POST["wc-yapay_intermediador-cc_card_installments"];


            if (0 < sizeof($order->get_items())) {
                $i = 0;
                foreach ($order->get_items() as $product) {
                    $params["transaction_product[$i][code]"] = $product["product_id"];
                    $params["transaction_product[$i][description]"] = $product['name'];
                    $params["transaction_product[$i][price_unit]"] = $order->get_item_subtotal($product, false);
                    $params["transaction_product[$i][quantity]"] = $product['qty'];
                    $i++;
                }
            }



            $card_expiry = explode("/", preg_replace('/\s/', "", $_POST["wc-yapay_intermediador-cc_card_expiry"]));

            $anoCartao = "20" . $card_expiry[1];


            $params["payment[payment_method_id]"] = $_POST["wc-yapay_intermediador-cc-payment-method"];
            $params["payment[card_name]"] = $_POST["wc-yapay_intermediador-cc_card_holder_name"];
            $params["payment[card_number]"] = preg_replace('/\s/', "", $_POST["wc-yapay_intermediador-cc_card_number"]);
            $params["payment[card_expdate_month]"] = $card_expiry[0];
            $params["payment[card_expdate_year]"] = $anoCartao; //$card_expiry[1];
            $params["payment[card_cvv]"] = $_POST["wc-yapay_intermediador-cc_card_cvc"];

            $params["payment[split]"] = $_POST["wc-yapay_intermediador-cc_card_installments"];

            $tcRequest = new WC_Yapay_Intermediador_Request();

            $tcResponse = $tcRequest->requestData("v2/transactions/pay_complete", $params, $this->get_option("environment"), false);

            if ($tcResponse->message_response->message == "success") {

                $transactionParams["order_id"]          = (string)$tcResponse->data_response->transaction->order_number;
                $transactionParams["transaction_id"]    = (int)$tcResponse->data_response->transaction->transaction_id;
                $transactionParams["split_number"]      = (int)$tcResponse->data_response->transaction->payment->split;
                $transactionParams["payment_method"]    = (int)$tcResponse->data_response->transaction->payment->payment_method_id;
                $transactionParams["token_transaction"] = (string)$tcResponse->data_response->transaction->token_transaction;


                $order->update_meta_data('yapay_transaction_data', serialize($transactionParams));
                $order->save();

                $log = new WC_Logger();
                $log->add(
                    "yapay-intermediador-transactions-save-",
                    "Vindi NEW TRANSACTION SAVE : \n" .
                        print_r($transactionParams, true) . "\n\n"
                );

                if (defined('WC_VERSION') && version_compare(WC_VERSION, '2.1', '>=')) {
                    WC()->cart->empty_cart();
                } else {
                    $woocommerce->cart->empty_cart();
                }
                if (!isset($use_shipping)) {
                    $use_shipping = isset($use_shipping);
                }
                if (defined('WC_VERSION') && version_compare(WC_VERSION, '2.1', '>=')) {
                    return array(
                        'result'   => 'success',
                        'redirect' => $this->get_return_url($order)
                        // 'redirect' => add_query_arg( array( 'use_shipping' => $use_shipping ), $order->get_checkout_payment_url( true ) )
                    );
                } else {
                    return array(
                        'result'   => 'success',
                        'redirect' => $this->get_return_url($order)
                        // 'redirect' => add_query_arg( array( 'order' => $order->id, 'key' => $order->order_key, 'use_shipping' => $use_shipping ), get_permalink( woocommerce_get_page_id( 'pay' ) ) )
                    );
                }
            } else {
                $errors = array();
                if (isset($tcResponse->error_response->general_errors)) {
                    foreach ($tcResponse->error_response->general_errors->general_error as $error) {
                        $errors[] = "<strong>Código:</strong> " . $error->code . " | <strong>Mensagem:</strong> " . $error->message;
                    }
                } else if (isset($tcResponse->error_response->validation_errors)) {
                    foreach ($tcResponse->error_response->validation_errors->validation_error as $error) {
                        $errors[] = "<strong>Mensagem:</strong> " . $error->message_complete;
                    }
                } else {
                    $errors[] = "<strong>Código:</strong> 9999 | <strong>Mensagem:</strong> Não foi possível finalizar o pedido. Tente novamente mais tarde!";
                }
                $this->add_error($errors);
            }
        }

        public function validate_fields()
        {
            global $wp;
            $order_id = $wp->query_vars['order-pay'];
            $order = new WC_Order($order_id);

            $errors = array();
            if ($_POST["wc-yapay_intermediador-cc-payment-method"] == "") {
                $errors[] = "<strong>Bandeira de Cartão</strong> não selecionada";
            }

            if ($_POST["wc-yapay_intermediador-cc_card_holder_name"] == "") {
                $errors[] = "<strong>Nome do Titular</strong> não informado";
            }

            if (!$this->luhn(preg_replace('/\s/', "", $_POST["wc-yapay_intermediador-cc_card_number"]))) {
                $errors[] = "<strong>Cartão de Crédito</strong> não informado ou inválido";
            }

            $card_expiry = explode("/", preg_replace('/\s/', "", $_POST["wc-yapay_intermediador-cc_card_expiry"]));

            echo $card_expiry;

            $anoCartao = "20" . $card_expiry[1];

            if ($anoCartao < date('Y')) {
                $errors[] = "<strong>Data de Vencimento do Cartão</strong> não informado ou inválido";
            } else if (($anoCartao == date('Y')) && ($card_expiry[0] < date('m'))) {
                $errors[] = "<strong>Data de Vencimento do Cartão</strong> não informado ou inválido";
            }

            if ($_POST["wc-yapay_intermediador-cc_card_installments"] == "0") {
                $errors[] = "<strong>Quantidade de Parcelas</strong> não informado";
            }

            if ($_POST["wc-yapay_intermediador-cc_card_installments"] == "0") {
                $errors[] = "<strong>Quantidade de Parcelas</strong> não informado";
            }

            if ($_POST["billing_phone"] == "") {
                $errors[] = "<strong>Telefone</strong> não informado";
            }


            if (count($errors)) {
                $this->add_error($errors);
                return false;
            }

            return true;
        }

        function luhn($ccNumberTc)
        {
            $sum = 0;
            foreach (array_reverse(str_split($ccNumberTc)) as $i => $num) {
                $num = ($i % 2 != 0) ? intval($num) * 2 : $num;
                if ($num > 9) {
                    $num = str_split(strval($num));
                    $num = intval($num[0]) + intval($num[1]);
                }
                $sum += $num;
            }
            return ($sum % 10 == 0) ? true : false;
        }

        public function thankyou_page($order_id)
        {

            $order = new WC_Order($order_id);

            include_once("includes/class-wc-yapay_intermediador-request.php");
            $tcRequest = new WC_Yapay_Intermediador_Request();

            $data = $order->get_meta('yapay_transaction_data', true);

            if (is_serialized($data)) {
                $data = unserialize($data);

                $params["token_account"]     = $this->get_option("token_account");
                $params['price']             = $order->get_total();
                $params['payment_method_id'] = $data['payment_method'];
                $tcResponse                  = $tcRequest->requestData("v1/seller_splits/simulate_split", $params, $this->get_option("environment"), false);

                $tcTransactionSplit = $tcResponse->data_response->splittings->splitting[$data['split_number'] - 1];
                $strPaymentMethod   = "";

                switch (intval($data['payment_method'])) {
                    case 3:
                        $strPaymentMethod  = "Visa";
                        break;
                    case 4:
                        $strPaymentMethod  = "Mastercard";
                        break;
                    case 5:
                        $strPaymentMethod  = "American Express";
                        break;
                    case 15:
                        $strPaymentMethod = "Discover";
                        break;
                    case 16:
                        $strPaymentMethod = "Elo";
                        break;
                    case 18:
                        $strPaymentMethod = "Aura";
                        break;
                    case 19:
                        $strPaymentMethod = "JCB";
                        break;
                    case 20:
                        $strPaymentMethod = "Hipercard";
                        break;
                    case 25:
                        $strPaymentMethod = "Hiper (Itaú)";
                        break;
                }

                $html = "
            <div class='woocommerce-order-overview woocommerce-thankyou-order-details order_details' style='padding:20px; margin-bottom:30px;'>
                <h3><strong style='color: #6d6d6d'>Vindi Intermediador</strong></h3>
                <hr/>
                <div style='margin: 20px 0'>
                    <span>Número da Transação: <strong>" . $data['transaction_id'] . "</strong></span>
                </div>
                <div style='margin: 20px 0'>
                    <span>Bandeira de Cartão: <strong>$strPaymentMethod</strong></span>
                </div>
                <div style='margin: 20px 0'>
                    <span>Pagamento em: <strong>" . $tcTransactionSplit->split . " x R$" . number_format(floatval($tcTransactionSplit->value_split), 2, ',', '') . "</strong></span>
                </div>
            </div>
            ";

                $order->add_order_note('Pedido registrado no Vindi Intermediador. Transação: ' . $data['transaction_id']);
            } else {
                $html = "
            <div class='woocommerce-order-overview woocommerce-thankyou-order-details order_details' style='padding:20px; margin-bottom:30px;'>
                <h3><strong style='color: #6d6d6d'>Vindi Intermediador</strong></h3>
                <div style='margin: 20px 0'>
                    <strong style='color: red'>Ocorreu um erro na geração da cobrança de crédito. Entre em contato com o administrador da Loja</strong>
                </div>
            </div>
            ";
            }


            echo $html;
        }
    }
endif;
