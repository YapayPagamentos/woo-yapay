<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<script src="https://static.traycheckout.com.br/js/finger_print.js" type="text/javascript"></script>


<fieldset id="wc-yapay_intermediador-pix-payment-form" class="wc_yapay_intermediador_gateway" data-yapay="payment-form">

    <input type="hidden" id="tcbPaymentMethod" name="wc-yapay_intermediador-pix-payment-method" class="required-entry" value="27" autocomplete="off">
    <?php if ($not_require_cpf == 'no') : ?>
        <div id="cpf_yapayP" class="cpf_yapay" style="display: none">  	
            <label>CPF<strong style="color: red;">*</strong> (somente números)</label>
            <input type="text" class="input-text yapay_cpf" id="yapay_cpfP" type="text" name="yapay_cpfP"  required>
        </div>
    <?php endif; ?>

    <div class="clear"></div>

</fieldset>

<script>
    var fp = window.yapay.FingerPrint({
        env: 'production'
    });
    document.getElementById('finger_print').value = fp.getFingerPrint();
</script>