<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<fieldset id="wc-yapay_intermediador-pix-payment-form">

    <input type="hidden" id="tcbPaymentMethod" name="wc-yapay_intermediador-pix-payment-method" class="required-entry" value="27" autocomplete="off">

    <div id="cpf_yapayP" class="cpf_yapay" style="display: none">  	
	    <label>CPF<strong style="color: red;">*</strong> (somente números)</label>
        <input type="text" class="input-text yapay_cpf" onkeyup="somenteNumeros(this)" id="yapay_cpfP" type="text" name="yapay_cpfP" maxlength="11" required>
    </div>


    <div class="clear"></div>

</fieldset>

