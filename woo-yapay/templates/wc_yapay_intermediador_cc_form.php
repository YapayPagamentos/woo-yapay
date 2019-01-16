<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<fieldset id="wc-yapay_intermediador-cc-payment-form">
    
    <input type="hidden" id="wc-yapay_intermediador-cc-cart-total" value="<?php echo number_format( $cart_total, 2, '.', '' ); ?>" />
    <input type="hidden" id="tcPaymentMethod" name="wc-yapay_intermediador-cc-payment-method" class="required-entry" value="" autocomplete="off">
    <label for="tcPaymentMethod">Selecione a forma de pagamento <span class="required">*</span></label>
    <ul>
        <?php
            $imgSrc = "";
            foreach ($payment_methods as $idTcPayment){
                switch (intval($idTcPayment)){
                    case 2: $imgSrc = "diners-club-international";break;
                    case 3: $imgSrc = "visa";break;
                    case 4: $imgSrc = "mastercard";break;
                    case 5: $imgSrc = "american-express";break;
                    case 15: $imgSrc = "discover";break;
                    case 16: $imgSrc = "elo";break;
                    case 18: $imgSrc = "aura";break;
                    case 19: $imgSrc = "jcb";break;
                    case 20: $imgSrc = "hipercard";break;
                    case 25: $imgSrc = "hiper";break;
                }
        ?>
        <li class="tcPaymentMethod">
            <img src="<?php echo $url_images.$imgSrc; ?>-flag.svg" class="tcPaymentFlag" id="tcPaymentFlag<?php echo $idTcPayment;?>" onclick="selectCreditCardTc('<?php echo $idTcPayment;?>')">
        </li>

        <?php
            }
        ?>

     </ul>
    <div class="clear"></div>

    <div id="wc-yapay_intermediador-cc-credit-card-form" class="wc-yapay_intermediador-cc-method-form">
        <p class="form-row form-row-first">
            <label for="wc-yapay_intermediador-cc-card-holder-name"><?php _e( 'Nome do Titular do Cartão', 'woocommerce-wc-yapay_intermediador-cc' ); ?> <span class="required">*</span></label>
            <input id="wc-yapay_intermediador-cc-card-holder-name" name="wc-yapay_intermediador-cc_card_holder_name" class="input-text" type="text" autocomplete="off" style="font-size: 1.5em; padding: 8px;" />
        </p>
        <p class="form-row form-row-last">
            <label for="wc-yapay_intermediador-cc-card-number"><?php _e( 'Número do Cartão', 'woocommerce-wc-yapay_intermediador-cc' ); ?> <span class="required">*</span></label>
            <input id="wc-yapay_intermediador-cc-card-number" name="wc-yapay_intermediador-cc_card_number" class="input-text " type="text" maxlength="20" autocomplete="off" placeholder="&bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull;" style="font-size: 1.5em; padding: 8px;" onkeyup="identifyCreditCardTc(this.value)" onblur="getSplits(document.getElementById('tcPaymentMethod').value)" />
        </p>
        <div class="clear"></div>
        <p class="form-row form-row-first">
            <label for="wc-yapay_intermediador-cc-card-expiry"><?php _e( 'Data de Expiração (MM/YYYY)', 'woocommerce-wc-yapay_intermediador-cc' ); ?> <span class="required">*</span></label>
            <input id="wc-yapay_intermediador-cc-card-expiry" name="wc-yapay_intermediador-cc_card_expiry" class="input-text wc-credit-card-form-card-expiry" type="text" autocomplete="off" placeholder="<?php _e( 'MM / YYYY', 'woocommerce-wc-yapay_intermediador-cc' ); ?>" style="font-size: 1.5em; padding: 8px;" />
        </p>
        <p class="form-row form-row-last">
            <label for="wc-yapay_intermediador-cc-card-cvc"><?php _e( 'Código de Segurança', 'woocommerce-wc-yapay_intermediador-cc' ); ?> <span class="required">*</span></label>
            <input id="wc-yapay_intermediador-cc-card-cvc" name="wc-yapay_intermediador-cc_card_cvc" class="input-text wc-credit-card-form-card-cvc" type="text" autocomplete="off" placeholder="<?php _e( 'CVV', 'woocommerce-wc-yapay_intermediador-cc' ); ?>" style="font-size: 1.5em; padding: 8px;" />
        </p>
        <div class="clear"></div>
        <p class="form-row form-row-first">
            <label for="wc-yapay_intermediador-cc-card-installments"><?php _e( 'Parcelamento', 'woocommerce-wc-yapay_intermediador-cc' ); ?> <small>(<?php _e( 'valor mínimo de parcela - R$ 10,00', 'woocommerce-wc-yapay_intermediador-cc' ); ?>)</small> <span class="required">*</span></label>
            <select id="wc-yapay_intermediador-cc-card-installments" name="wc-yapay_intermediador-cc_card_installments" style="font-size: 1.5em; padding: 4px; width: 100%;" disabled="disabled">
                <option value="0">--</option>
            </select>
        </p>

        <div class="clear"></div>
    </div>
</fieldset>
