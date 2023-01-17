<?php

if ( class_exists( 'WC_Yapay_Intermediador_Cancellation' ) ) return;

/**
 * WooCommerce Yapay_Intermediador Cancellation class
 */

class WC_Yapay_Intermediador_Cancellation
{
    
    public function __construct() 
    {
        add_action( 'woocommerce_order_status_changed', [ $this, 'on_yapay_status_changed' ], 10, 3 ); 
    }

    /**
     * Watch yapay orders status 
     * @param int $id
     * @param string $from
     * @param string $to
     * @return void
     */
    public function on_yapay_status_changed( $id, $from, $to )
    {
        $order = wc_get_order( $id );

        $payment_method  = $order->get_payment_method();
        $payment_methods = [ 'wc_yapay_intermediador_bs', 'wc_yapay_intermediador_pix', 'wc_yapay_intermediador_cc', 'wc_yapay_intermediador_tef' ];

        if ( array_intersect( $payment_methods, [ $payment_method ] ) ) {
            if ( $to === 'cancelled' ) {
                $this->cancel_transaction( $payment_method, $id );
            }
        }

    }

    /**
     * Cancel yapay transactions
     * @param string $payment_method
     * @param int $order_id
     * @return void
     */
    private function cancel_transaction( $payment_method, $order_id )
    {
        include_once( 'class-wc-yapay_intermediador-request.php' );

        $transaction = $this->get_transaction( $order_id );
        $option      = $this->get_payment_option( $payment_method );

        if ( ! $transaction || ! isset( $transaction['transaction_id'] ) ) return;
        if ( ! $option['token_account'] ) return;

        $params = [
            "access_token"   => $option['token_account'],
            "transaction_id" => $transaction['transaction_id']
        ];

        $request  = new WC_Yapay_Intermediador_Request();
        $response = $request->requestData( "api/v3/transactions/cancel", $params, $option['environment'], false, "PATCH" );
    }

    /**
     * Get payment method options
     * @param string $payment_method
     * @return array|bool
     */
    private function get_payment_option( $payment_method )
    {
        $option = get_option( "woocommerce_{$payment_method}_settings" );

        if ( $option && ! empty( $option ) ) {
            if ( ! isset( $option['token_account'] ) || ! $option['token_account'] ) {
                return false;
            }

            if ( ! isset( $option['environment'] ) ) {
                return false;
            }
        }

        return $option;
    }

    /**
     * Get transaction data
     * @param int $order_id
     * @return array|bool
     */
    private function get_transaction( $order_id )
    {
        $data = get_post_meta( $order_id, "yapay_transaction_data", true );

        if ( is_serialized( $data ) ) {
            $transaction = unserialize( $data );

            if ( ! empty( $transaction ) && isset( $transaction['transaction_id'] ) ) {
                return $transaction;
            }
        }

        return;
    }
    
}