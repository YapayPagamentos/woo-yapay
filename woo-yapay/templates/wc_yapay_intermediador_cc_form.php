<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
?>


<script src="https://static.traycheckout.com.br/js/finger_print.js" type="text/javascript"></script>


<fieldset id="wc-yapay_intermediador-cc-payment-form" data-yapay="payment-form" onclick=inputCPFYapay()>
    
    <input type="hidden" id="wc-yapay_intermediador-cc-cart-total" value="<?php echo number_format( $cart_total, 2, '.', '' ); ?>" />
    <input type="hidden" id="tcPaymentMethod" name="wc-yapay_intermediador-cc-payment-method" class="required-entry" value="" autocomplete="off">
    <label for="tcPaymentMethod">Formas de pagamento</label>
        <ul style="margin: 30px 0;">
            <?php
                $imgSrc = "";
                foreach ($payment_methods as $idTcPayment){
                    switch (intval($idTcPayment)){
                        // case 2: $imgSrc = "diners-club-international";break;
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
                <img src="<?php echo $url_images.$imgSrc; ?>-flag.svg" class="tcPaymentFlag" id="tcPaymentFlag<?php echo $idTcPayment;?>" >
            </li>

            <?php
                }
            ?>

        </ul>

    <div class="clear"></div>

    <div id="wc-yapay_intermediador-cc-credit-card-form" class="wc-yapay_intermediador-cc-method-form">
        <div class="form-row form-row-wide linha1">
            
        
            <p class="form-row form-row-first">
                <label for="wc-yapay_intermediador-cc-card-holder-name"><?php _e( 'Nome do portador', 'woocommerce-wc-yapay_intermediador-cc' ); ?> <span class="required">*</span></label>
                <input id="wc-yapay_intermediador-cc-card-holder-name"  name="wc-yapay_intermediador-cc_card_holder_name" class="input-text" type="text" autocomplete="off" style="font-size: 1.5em; padding: 8px;" onkeypress="return apenasLetrasCartao(event,this);" />
            </p>
            <p class="form-row form-row-last">
                <label for="wc-yapay_intermediador-cc-card-number"><?php _e( 'Número do Cartão', 'woocommerce-wc-yapay_intermediador-cc' ); ?> <span class="required">*</span></label>
                <input id="wc-yapay_intermediador-cc-card-number" name="wc-yapay_intermediador-cc_card_number" class="input-text " type="text" maxlength="20" autocomplete="off" placeholder="&bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull;" style="font-size: 1.5em; padding: 8px;" onkeyup="identifyCreditCardTc(this.value)" onblur="getSplits(document.getElementById('tcPaymentMethod').value)" onkeypress="somenteNumerosCartao(event,this)" />
            </p>
        </div>
        <div class="clear"></div>
        <p class="form-row form-row-first">
            <label for="wc-yapay_intermediador-cc-card-expiry"><?php _e( 'Data de Expiração (MM/YY)', 'woocommerce-wc-yapay_intermediador-cc' ); ?> <span class="required">*</span></label>
            <input id="wc-yapay_intermediador-cc-card-expiry" name="wc-yapay_intermediador-cc_card_expiry" class="input-text wc-credit-card-form-card-expiry" type="text"  autocomplete="off" placeholder="<?php _e( 'MM / YY', 'woocommerce-wc-yapay_intermediador-cc' ); ?>" onkeyup="
                    var v = this.value;
                    if (v.match(/^\d{2}$/) !== null) {
                        this.value = v + ' / ';
                    } else if (v.match(/^\d{2}\/\d{2}$/) !== null) {
                        this.value = v;
                    }"
                    maxlength="7"  
                    style="font-size: 1.5em; padding: 8px;" />
        </p>
        <p class="form-row form-row-last">
            <label for="wc-yapay_intermediador-cc-card-cvc"><?php _e( 'Código de Segurança', 'woocommerce-wc-yapay_intermediador-cc' ); ?> <span class="required">*</span></label>
            <input id="wc-yapay_intermediador-cc-card-cvc" name="wc-yapay_intermediador-cc_card_cvc" class="input-text wc-credit-card-form-card-cvc" type="text" autocomplete="off" maxlength="4" placeholder="<?php _e( 'CVV', 'woocommerce-wc-yapay_intermediador-cc' ); ?>" style="font-size: 1.5em; padding: 8px;" />
        </p>
        <input type="hidden" name="finger_print" id="finger_print">
        <div class="clear"></div>

        <p class="form-row form-row-wide">
            <label for="wc-yapay_intermediador-cc-card-installments"><?php _e( 'Parcelamento', 'woocommerce-wc-yapay_intermediador-cc' ); ?>  <span class="required">*</span></label>
            <select id="wc-yapay_intermediador-cc-card-installments" name="wc-yapay_intermediador-cc_card_installments" style="font-size: 1.5em; padding: 4px; width: 100%;" disabled="disabled">
                <option value="0" disabled selected>--</option>
            </select>
        </p>

        <div id="cpf_yapayC" class="input-text cpf_yapay" style="display: none; ">      
            <label>CPF titular cartão<strong style="color: red;">*</strong> (somente números)</label>
            <input type="text" class="input-text yapay_cpf" onkeyup="somenteNumeros(this)" type="text" id="yapay_cpfC" name="yapay_cpfC" maxlength="11" required>
        </div>

        <div class="clear"></div>
    </div>
</fieldset>

<script>
    var fp = window.yapay.FingerPrint({ env: 'production' });
    document.getElementById('finger_print').value = fp.getFingerPrint();
</script>