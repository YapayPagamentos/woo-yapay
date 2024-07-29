<?php
/**
 * Plugin Name: Vindi Pagamento WooCommerce
 * Plugin URI: https://vindi.com.br/formas-de-pagamentos/
 * Description: Intermediador de pagamento Vindi para a plataforma WooCommerce.
 * Author: Integração Vindi Intermediador
 * Author URI: https://vindi.com.br/
 * Version: 0.7.6
 * Text Domain: vindi-pagamento
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
include_once( 'includes/wc-yapay_intermediador-install.php' );
register_activation_hook( __FILE__, 'woocommerce_yapay_intermediador_install' );
add_action( 'plugins_loaded', 'wc_gateway_yapay_intermediador_init', 0 );
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'wc_gateway_yapay_intermediador_action_links' );


function wc_gateway_yapay_intermediador_init() {

    if ( ! class_exists( 'WC_Payment_Gateway' ) ) return;

    // If we made it this far, then include our Gateway Class
    include_once( 'class-wc-yapay_intermediador-creditcard-gateway.php' );
    include_once( 'class-wc-yapay_intermediador-bankslip-gateway.php' );
    include_once( 'class-wc-yapay_intermediador-tef-gateway.php' );
    include_once( 'class-wc-yapay_intermediador-pix-gateway.php' );
    include_once( 'class-wc-yapay_intermediador-bolepix-gateway.php' );

    // Now that we have successfully included our class,
    // Lets add it too WooCommerce
    add_filter( 'woocommerce_payment_gateways', 'wc_yapay_intermediador_gateway_add' );
    function wc_yapay_intermediador_gateway_add( $methods ) {
        $methods[] = 'WC_Yapay_Intermediador_Creditcard_Gateway';
        $methods[] = 'WC_Yapay_Intermediador_Tef_Gateway';
        $methods[] = 'WC_Yapay_Intermediador_Bankslip_Gateway';
        $methods[] = 'WC_Yapay_Intermediador_Pix_Gateway';
        $methods[] = 'WC_Yapay_Intermediador_Bolepix_Gateway';
        return $methods;
    }
}

function html_log_page() {
    include_once 'templates/wc_yapay_intermediador_html_log.php';
}

function wc_gateway_yapay_intermediador_action_links( $links ) {
    $plugin_links = array(
        '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout&section=wc_yapay_intermediador_creditcard_gateway' ) . '">' . __( 'Config. Cartão', 'wc-yapay_intermediador-cc' ) . '</a>',
        '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout&section=wc_yapay_intermediador_tef_gateway' ) . '">' . __( 'Config. TEF', 'wc-yapay_intermediador-tef' ) . '</a>',
        '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout&section=wc_yapay_intermediador_bankslip_gateway' ) . '">' . __( 'Config. Boleto', 'wc-yapay_intermediador-bs' ) . '</a>',
        '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout&section=wc_yapay_intermediador_pix_gateway' ) . '">' . __( 'Config. Pix', 'wc-yapay_intermediador-pix' ) . '</a>',
    );

    // Merge our new link with the default ones
    return array_merge( $plugin_links, $links );
}

function get_splits() {

    // Define a variável ajaxurl
    $script  = '<script>';
    $script .= 'var ajaxurl = "' . admin_url('admin-ajax.php') . '";';
    $script .= '</script>';
    echo $script;

}

add_action( 'wp_footer', 'get_splits' );

// Ação de callback do Ajax
function tc_get_splits() {

    include_once("includes/class-wc-yapay_intermediador-request.php");
    $tcConfig = new WC_Yapay_Intermediador_Creditcard_Gateway();

    $paymentId = $_POST['payment_method'];
    $price = $_POST['price'];
    $token_account = $tcConfig->get_option("token_account");
    $environment = $tcConfig->get_option("environment");
    $qs = intval($tcConfig->get_option("max_qtd_split"));
    $vs = floatval($tcConfig->get_option("min_value_split"));
    $fee = $tcConfig->get_option('show_installments_fees');

    $tcRequest = new WC_Yapay_Intermediador_Request();

    $params["token_account"] = $token_account;
    $params["price"] = $price;
    $params["type_response"] = "J";


    $tcResponse = $tcRequest->requestData("v1/transactions/simulate_splitting",$params,$environment);

    if($tcResponse['message_response']['message'] == "success"){
        $simulate_splitting = $tcResponse['data_response']['payment_methods'];

        $simulate_splitting = $tcResponse['data_response']['payment_methods'];
        
        foreach($simulate_splitting as $payment_method){
            if(intval($payment_method['payment_method_id']) == intval($paymentId)){
                for($i = 0 ; $i < $qs ; $i ++){
                    if(floatval($payment_method['splittings'][$i]['value_split']) >= $vs){
                        $results['splittings']['splitting'][$i] = $payment_method['splittings'][$i];


                    } else if ($price <= $vs) {
                        $results['splittings']['splitting'] = $payment_method['splittings'][0];
                        break;
                    }
                }

                $results['splittings']['fees'] = $fee;
                echo json_encode($results['splittings']);
            }
        }
    }

    die();
}

add_action( 'wp_ajax_tc_get_splits', 'tc_get_splits' );
add_action( 'wp_ajax_nopriv_tc_get_splits', 'tc_get_splits' );

add_action( 'template_redirect', 'wc_yapay_intermediador_template_redirect_intercept' );
function wc_yapay_intermediador_template_redirect_intercept() {
    global $wp_query;
    if( $wp_query->get('wc_yapay_intermediador') ) {
        include_once("includes/class-wc-yapay_intermediador-request.php");
        include_once("includes/class-wc-yapay_intermediador-auth.php");

        $code = $_GET['code'];
        $customerKey = $_GET['CK'];
        $customerSecret = $_GET['CS'];
        $typePayment = $_GET['type'];
        $environment = ($_GET["environment"] == "true") ? "yes":"no";

        $tcAuth = new WC_Yapay_Intermediador_Auth();

        $tcAuth->doAuthorization( $customerKey, $customerSecret, $code, $environment);

        $tcRequest = new WC_Yapay_Intermediador_Request();

        $params["access_token"] = $tcAuth->access_token;
        $params["url"] = get_site_url();

        $tcResponse = $tcRequest->requestData("v1/people/update",$params,$environment);

        if($tcResponse->message_response->message == "success"){

            $htmlId = "woocommerce_wc_yapay_intermediador_".$typePayment;

            $script = "<script>";
            $script .= "window.opener.document.getElementById('".$htmlId."_consumer_key').value = '$customerKey';";
            $script .= "window.opener.document.getElementById('".$htmlId."_consumer_secret').value = '$customerSecret';";
            $script .= "window.opener.document.getElementById('".$htmlId."_token_account').value = '".$tcResponse->data_response->token_account."';";
            $script .= "window.opener.document.getElementById('mainform').submit();";
            $script .= "window.close()";
            $script .= "";
            $script .= "";
            $script .= "</script>";

            echo $script;
        }
        exit;
    }

    if($wp_query->get('wc_yapay_intermediador_redirect_auth')){
        echo "Vamos redirecionar: ".get_site_url();
        die();
    }
}

add_action( 'template_redirect', 'wc_yapay_intermediador_notification' );
function wc_yapay_intermediador_notification() {
    global $wp_query;

    if( $wp_query->get( 'wc_yapay_intermediador_notification' ) && isset( $_GET['wc_yapay_intermediador_notification'] ) ) {

        $order_id = $_GET['order_id'];

        $log = new WC_Logger();
        $log->add(
            "yapay-intermediador-notification",
            "Vindi NEW NOTIFICATION : \n" .
            "ORDER ID: ". print_r( $order_id, true ) ."\n\n"
        );

        include_once( "includes/class-wc-yapay_intermediador-request.php" );

        $order = new WC_Order( $order_id );

        $tcTransaction = unserialize( $order->get_meta("yapay_transaction_data", true));

        if ( is_array( $tcTransaction ) && ! empty( $tcTransaction ) ) {

            $tcPayment    = "";
            $paymentOrder = method_exists($order, 'get_payment_method') ? $order->get_payment_method() : $order->payment_method;

            switch ($paymentOrder) {
                case "wc_yapay_intermediador_bs": $tcPayment  = new WC_Yapay_Intermediador_Bankslip_Gateway(); break;
                case "wc_yapay_intermediador_cc": $tcPayment  = new WC_Yapay_Intermediador_Creditcard_Gateway(); break;
                case "wc_yapay_intermediador_tef": $tcPayment = new WC_Yapay_Intermediador_Tef_Gateway(); break;
                case "wc_yapay_intermediador_pix": $tcPayment = new WC_Yapay_Intermediador_Pix_Gateway(); break;
                case "wc_yapay_intermediador_bolepix": $tcPayment = new WC_Yapay_Intermediador_Bolepix_Gateway(); break;
                default: $tcPayment                           = new WC_Yapay_Intermediador_Creditcard_Gateway();break;
            }

            $tcRequest = new WC_Yapay_Intermediador_Request();

            $params["token_transaction"] = $tcTransaction['token_transaction'];
            $params["token_account"]     = $tcPayment->get_option("token_account");

            $tcResponse = $tcRequest->requestData( "v2/transactions/get_by_token", $params,$tcPayment->get_option( "environment" ) );

            $log->add(
                "yapay-intermediador-notification",
                "Vindi NEW NOTIFICATION : \n" .
                "REQUEST: ". print_r( $tcResponse, true ) ."\n\n"
            );

            if ( $tcResponse->message_response->message == "success" ) {

                $codeStatus = intval( $tcResponse->data_response->transaction->status_id );
                $comment    = $codeStatus . ' - ' . $tcResponse->data_response->transaction->status_name;

                switch ( $codeStatus ) {
                    case 4:
                    case 5:
                    case 88:
                            if($order->get_status() != "on-hold"){
                                $order->update_status( 'on-hold', 'Vindi Intermediador enviou automaticamente o status: '.$comment .". | " );
                            }else{
                                $order->add_order_note( 'Vindi Intermediador enviou automaticamente o status: '.$comment  );
                            }
                        break;
                    case 6 :
                            $order->add_order_note( 'Vindi Intermediador - Aprovado. Pagamento confirmado automaticamente.' );
                            $order->payment_complete();
                        break;
                    case 24 :
                            if($order->get_status() != "on-hold"){
                                $order->update_status( 'on-hold', 'Vindi Intermediador enviou automaticamente o status: '.$comment .". | " );
                            }else{
                                $order->add_order_note( 'Vindi Intermediador enviou automaticamente o status: '.$comment  );
                            }
                        break;
                    case 7 :
                    case 89 :
                            if($order->get_status() != "cancelled"){
                                $order->update_status( 'cancelled', 'Vindi Intermediador - Cancelado. Pedido cancelado automaticamente (transação foi cancelada, pagamento foi negado, pagamento foi estornado ou ocorreu um chargeback). | ' );
                            }else{
                                $order->add_order_note( 'Vindi Intermediador - Cancelado. Pedido cancelado automaticamente (transação foi cancelada, pagamento foi negado, pagamento foi estornado ou ocorreu um chargeback).'  );
                            }
                        break;
                    case 87 :
                            if($order->get_status() != "on-hold"){
                                $order->update_status( 'on-hold', 'Vindi Intermediador enviou automaticamente o status: '.$comment .". | " );
                            }else{
                                $order->add_order_note( 'Vindi Intermediador enviou automaticamente o status: '.$comment  );
                            }
                        break;
                }

                $message = "Status do pedido " . $order->get_id() ." alterado: " . $comment . ". Transação: " .$tcResponse->data_response->transaction->transaction_id." ! ";
                echo $message;

                $log->add(
                    "yapay-intermediador-notification",
                    "Vindi NEW NOTIFICATION : \n" .
                    "STATUS: ".print_r( $message, true ) ."\n\n"
                );

            } else {
                echo "Ocorreu um erro para atualizar o status do pedido!";
                var_dump(str_replace($tcPayment->get_option("prefixo"),"",$tcTransaction['order_id']));
                var_dump($order->get_id());
                var_dump($tcResponse);
            }
        }
    }
}

add_action( 'template_redirect', 'wc_yapay_intermediador_log' );
function wc_yapay_intermediador_log() {
    if(isset($_GET["log_data"])){
        echo "<pre><code>".htmlentities(urldecode($_GET["log_data"]))."</code></pre>";
        die();
    }
}

function wc_yapay_intermediador_add_rewrite_rules() {
    add_rewrite_rule(
            'wc_yapay_intermediador/([^/]+)/?',
            'index.php?wc_yapay_intermediador=1&code=$matches[1]',
            'top' );

    add_rewrite_rule(
            'wc_yapay_intermediador_redirect_auth/([^/]+)/?',
            'index.php?wc_yapay_intermediador_redirect_auth=1',
            'top' );

    add_rewrite_rule(
            'wc_yapay_intermediador_notification/([^/]+)/?',
            'index.php?wc_yapay_intermediador_notification=1',
            'top' );

    add_rewrite_rule(
            'wc_yapay_intermediador_log/([^/]+)/?',
            'index.php?wc_yapay_intermediador_log=1',
            'top' );

}

add_action( 'init', 'wc_yapay_intermediador_rewrites_init' );

function wc_yapay_intermediador_rewrites_init() {
    add_rewrite_tag( '%wc_yapay_intermediador%', '([0-9]+)' );
	add_rewrite_tag( '%wc_yapay_intermediador_redirect_auth%', '([0-9]+)' );
	add_rewrite_tag( '%wc_yapay_intermediador_notification%', '([0-9]+)' );
	add_rewrite_tag( '%wc_yapay_intermediador_log%', '([0-9]+)' );


	wc_yapay_intermediador_add_rewrite_rules();
}


// Adicionar campo Correios na tela de pedido

// Adding Meta container admin shop_order pages
add_action( 'add_meta_boxes', 'mv_add_meta_boxes' );
if ( ! function_exists( 'mv_add_meta_boxes' ) )
{
    function mv_add_meta_boxes()
    {
        add_meta_box( 'mv_other_fields', __('Vindi - Código de Rastreio','woocommerce'), 'mv_add_other_fields_for_packaging', 'shop_order', 'side', 'core' );
    }
}

// Adiciona o container metafield na página de pedido
if ( ! function_exists( 'mv_add_other_fields_for_packaging' ) )
{
    function mv_add_other_fields_for_packaging()
    {
        global $post;
		$order = wc_get_order($post->ID);

        if ($post->post_status != 'wc-pending') {
            $meta_field_data = $order->get_meta('_my_field_slug', true) ? $order->get_meta('_my_field_slug', true) : '';
            $urlRastreio = $order->get_meta('_url_rastreio_yapay', true) ? $order->get_meta('_url_rastreio_yapay', true) : '';

            echo '
                <div id="RastreioYapay" class="listaRastreioYapay">
                    <ul id="list">
                        <li>' . $meta_field_data . '  </li>
                    </ul>

                    <input type="hidden" name="mv_other_meta_field_nonce" value="' . wp_create_nonce() . '">
                    <p style="border-bottom:solid 1px #eee;padding-bottom:15px;">
                    <p>Enviar código de rastreio:</p>
                        <span class="textRateio">Código<strong>*</strong>: </span><input type="text" id="inputYapayRastreio" maxlength="45" style="width:250px;" onkeyup="onkeyupYapay()" name="wp_woocommerce_rastreio">


                    </p>
                    <p>
                        <span class="textRateio">URL Transp.: </span><input type="text" id="inputYapayRastreioURL" onkeyup="onkeyupYapay()" maxlength="255" style="width:250px;" name="wp_woocommerce_rastreio" >

                    </p>
                    <div style="text-align: center;">
                    <button type="button" disabled id="btnYapayRastreio" class="button-secondary btnYapayRastreio" onClick="sendRastreio('. $post->ID .')" >Enviar Yapay</button>
                    </div>
                </div>

                <div class="obsYapay">
                    <p><strong>Observação:</strong> É importante enviar a URL da Transportadora.</p>
                </div>
                ';

        }
        else {
            echo 'Status não permite envio de código de rastreio';
        }
    }
}
// Fim container metafield

// Fim correios


function sendRastreioYapay() {
    $order_id = $_POST['order_id'];
    $code     = $_POST['code'];

    if ((strtolower($_POST["url"]) == "correios") OR (strtolower($_POST["url"]) == "correio") OR
        (strtolower($_POST["url"]) == "correios-sedex") OR (strtolower($_POST["url"]) == "correios-pac")) {
            $url = "http://www2.correios.com.br/sistemas/rastreamento/";
    } else {
        $url = $_POST["url"];
    }

    $order  = new WC_Order( $order_id );

    include_once("includes/class-wc-yapay_intermediador-request.php");

    $paymentOrder = method_exists($order, 'get_payment_method') ? $order->get_payment_method() : $order->payment_method;

    switch ($paymentOrder) {
        case "wc_yapay_intermediador_bs": $tcConfig  = new WC_Yapay_Intermediador_Bankslip_Gateway(); break;
        case "wc_yapay_intermediador_cc": $tcConfig  = new WC_Yapay_Intermediador_Creditcard_Gateway(); break;
        case "wc_yapay_intermediador_tef": $tcConfig = new WC_Yapay_Intermediador_Tef_Gateway(); break;
        case "wc_yapay_intermediador_pix": $tcConfig = new WC_Yapay_Intermediador_Pix_Gateway(); break;
        case "wc_yapay_intermediador_bolepix": $tcConfig = new WC_Yapay_Intermediador_Bolepix_Gateway(); break;
        default: $tcConfig                           = new WC_Yapay_Intermediador_Creditcard_Gateway();break;
    }

    $tcTransaction = unserialize($order->get_meta("yapay_transaction_data", true));

    if ($tcTransaction && is_array($tcTransaction)) {
        $token_transaction = $tcTransaction['token_transaction'];
        $idTransacao       = $tcTransaction['transaction_id'];

        $token_account = $tcConfig->get_option( "token_account" );
        $environment   = $tcConfig->get_option( "environment" );

        $params["token_account"]     = $token_account;
        $params["id"]                = $idTransacao;
        $params["transaction_token"] = $token_transaction;
        $params["code"]              = $code;
        $params["url"]               = $url;
        $params["posted_date"]       = time();

        $tcRequest = new WC_Yapay_Intermediador_Request();
        $tcResponse = $tcRequest->requestData( "v3/sales/trace", $params, $environment );

        $order->add_order_note( 'Enviado para Vindi o código de rastreio: ' . $code );

        $order->update_meta_data('_my_field_slug', $_POST['code']);
		$order->update_meta_data('_url_rastreio_yapay', $_POST['url'] );
        $order->save();
    }
}

add_action( 'wp_ajax_sendRastreioYapay', 'sendRastreioYapay' );
add_action( 'wp_ajax_nopriv_sendRastreioYapay', 'sendRastreioYapay' );

function yapay_enqueue_scripts() {
    wp_enqueue_style('yapay_intermediador', plugins_url('woo-yapay/assets/css/styles.css', plugin_dir_path(__FILE__)), array(),time());
    wp_enqueue_script('yapay_intermediador', plugins_url('woo-yapay/assets/js/index.js', plugin_dir_path(__FILE__)), array('jquery'), time());

    if (!is_admin()) {
        wp_enqueue_script('yapay_intermediador-imask', 'https://cdnjs.cloudflare.com/ajax/libs/imask/7.1.3/imask.min.js', array());

        wp_enqueue_script('yapay_intermediador-checkout-credit', plugins_url('woo-yapay/assets/js/credit.js', plugin_dir_path(__FILE__)), array('yapay_intermediador-imask', 'jquery'));

        wp_enqueue_script(
            'yapay_intermediador-fingerprint',
            'https://static.traycheckout.com.br/js/finger_print.js',
            ['jquery', 'yapay_intermediador-checkout'],
        );
    }
}
add_action('init', 'yapay_enqueue_scripts');
