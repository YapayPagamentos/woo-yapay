<div class='woocommerce-order-overview woocommerce-thankyou-order-details order_details' style='padding:20px; margin-bottom:30px;'>
    <h3><strong style='color: #6d6d6d'>Yapay Intermediador</strong></h3>
    <div>
        <div>
            <h4>Pix</h4>
        </div>
        <div style='margin: 20px 0'>
            <span><strong>Pix Copia e Cola</strong></span>
            <div style='display: flex; align-items: center; gap:10px;'>
                <input style='width: 100%;' type='text' value="<?php echo $qrcode_original_path ?>" />
                <a style='cursor: pointer; border: 1px solid #b8b8b8;padding: 12px; border-radius:3px; color:#111;' class='copiaCola' id='copiaCola'>
                    <img style='max-width: 20px' name='imgCopy' src='<?php echo esc_url("$url_image/assets/images/copy.svg"); ?>' />
                </a>
            </div>
        </div>
        <div style='margin: 20px 0'>
            <span><strong>Escaneie o QR Code:</strong></span>
            <div>
                <object class='qrCodeYapay' data="<?php echo $qrcode_path ?>"></object>
            </div>
        </div>
        <hr />
    </div>
    <div>
        <div>
            <h4>Boleto Bancário</h4>
        </div>
        <div style='margin: 20px 0'>
            <a href=<?php echo $url_payment ?> target='_blank' class='button'>Imprimir Boleto</a>
        </div>
        <div style='margin: 20px 0'>
            <span><strong>Digitável do Boleto:</strong></span>
            <div style='display: flex; align-items: center; gap:10px;'>
                <input style='width: 100%;' type='text' id='linhaDigitavel' value="<?php echo $typeful_line ?>" />
                <a style='cursor: pointer; border: 1px solid #b8b8b8;padding: 12px;border-radius: 3px; color:#111;' class='copiaCola' id='copiaCola'>
                    <img style='max-width: 20px' name='imgCopy' src='<?php echo esc_url("$url_image/assets/images/copy.svg"); ?>' />
                </a>
            </div>
        </div>
    </div>
</div>