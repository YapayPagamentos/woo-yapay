<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<fieldset id="wc-yapay_intermediador-bs-payment-form" class="wc_yapay_intermediador_gateway">
    <input type="hidden" id="tcbPaymentMethod" name="wc-yapay_intermediador-bs-payment-method" class="required-entry" value="" autocomplete="off">
    <?php if ($not_require_cpf == 'no') : ?>
    <div id="cpf_yapayB" class="cpf_yapay" style="display: none">  	
	    <label>CPF<strong style="color: red;">*</strong> (somente números)</label>
        <input type="text" class="input-text yapay_cpf" onkeyup="somenteNumeros(this)" id="yapay_cpfB" type="text" name="yapay_cpfB" maxlength="11" required>
    </div>
    <?php endif; ?>


    <div class="clear"></div>

</fieldset>

