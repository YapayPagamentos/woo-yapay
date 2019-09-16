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


    <div id="cpf_yapayB" class="cpf_yapay" style="display: none">  	
	    <label>CPF<strong style="color: red;">*</strong> (somente números)</label>
        <input type="text" class="input-text yapay_cpf" onkeyup="somenteNumeros(this)" id="yapay_cpfB" type="text" name="yapay_cpfB" maxlength="11" required>
    </div>


    <div class="clear"></div>

</fieldset>

