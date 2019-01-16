<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<fieldset id="wc-yapay_intermediador-tef-payment-form">
    
    <input type="hidden" id="tctPaymentMethod" name="wc-yapay_intermediador-tef-payment-method" class="required-entry" value="" autocomplete="off">
    <label for="tctPaymentMethod">Selecione a forma de pagamento <span class="required">*</span></label>
    <ul>
        <?php
            $imgSrc = "";
            foreach ($payment_methods as $idTcPayment){
                switch (intval($idTcPayment)){
                    case 7: $imgSrc = "itau";break;
                    case 14: $imgSrc = "peela";break;
                    case 21: $imgSrc = "hsbc";break;
                    case 22: $imgSrc = "bradesco";break;
                    case 23: $imgSrc = "bb";break;
                }
        ?>
        <li class="tcPaymentMethod">
            <img src="<?php echo $url_images.$imgSrc; ?>-flag.svg" class="tcPaymentFlag" id="tctPaymentFlag<?php echo $idTcPayment;?>" onclick="selectTefTc('<?php echo $idTcPayment;?>')">
        </li>

        <?php
            }
        ?>

     </ul>
    <div class="clear"></div>

</fieldset>
