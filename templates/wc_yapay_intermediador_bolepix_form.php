<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<fieldset id="wc-yapay_intermediador-bolepix-payment-form" class="wc_yapay_intermediador_gateway">

    <input type="hidden" id="tcbPaymentMethod" name="wc-yapay_intermediador-bolepix-payment-method" class="required-entry" value="28" autocomplete="off">
    <?php if ($not_require_cpf == 'no') : ?>
        <div id="cpf_yapayBP" class="cpf_yapay" style="display: none">
            <label>CPF<strong style="color: red;">*</strong> (somente números)</label>
            <input type="text" class="input-text yapay_cpf" id="yapay_cpfBP" type="text" name="yapay_cpfBP"  required>
        </div>
    <?php endif; ?>

    <div class="clear"></div>

</fieldset>

