<div class="order-view-section">
    <div id="yapay-order-container">
        <h2 class="section-title"><?php echo __( "Yapay Intermediador","wc-yapay_intermediador-pix" ); ?></h2>
        <p>
            <div class="section-row">
                <strong><?php echo __( "Método de pagamento: ", "wc-yapay_intermediador-pix" ) ;?></strong>
                <span><?php echo esc_html( "PIX - Yapay Intermediador" ); ?></span>
            </div>
            <div class="section-row">
                <strong><?php echo __( "Yapay transaction ID:  ", "wc-yapay_intermediador-pix" ); ?></strong>
                <span><?php echo esc_html( $transaction_id ); ?></span>
            </div>
            <div class="section-row">
                <strong><?php echo __( "QR Code:   ", "wc-yapay_intermediador-pix" ); ?></strong><br>
                <div id="yapay-pix-qr">
                    <object data="<?php echo esc_html( "{$qrcode_path}" )?>"></object>
                </div>
            </div>
            <div class="section-row">
                <strong><?php echo __( "Linha digitável:  ", "wc-yapay_intermediador-pix" ); ?></strong>
                <div>
                    <span><?php echo esc_html( $qrcode_original_path ); ?></span>
                </div>
            </div>
        </p>
    </div>
</div>