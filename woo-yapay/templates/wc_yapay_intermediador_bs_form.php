<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<fieldset id="wc-yapay_intermediador-bs-payment-form">

    <input type="hidden" id="tcbPaymentMethod" name="wc-yapay_intermediador-bs-payment-method" class="required-entry" value="" autocomplete="off">
    <label for="tcbPaymentMethod">Selecione a forma de pagamento <span class="required">*</span></label>
    <ul>
        
        <li class="tcbPaymentMethod">
            <img src="<?php echo $url_images; ?>boleto-flag.svg" class="tcPaymentFlag tcPaymentFlagSelected" id="tcbPaymentMethod">
        </li>


    </ul>
    <div class="clear"></div>

</fieldset>
