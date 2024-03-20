<div class="order-view-section">
    <div id="yapay-order-container">
        <h2 class="section-title"><?php echo __( "Vindi Pagamento","wc-yapay_intermediador-bs" ); ?></h2>
        <p>
            <div class="section-row">
                <strong><?php echo __( "Método de pagamento: ", "wc-yapay_intermediador-bs" ) ;?></strong>
                <span><?php echo esc_html( "Boleto Bancário - Vindi Pagamento" ); ?></span>
            </div>
            <div class="section-row">
                <strong><?php echo __( "Vindi transaction ID:  ", "wc-yapay_intermediador-bs" ); ?></strong>
                <span><?php echo esc_html( $transaction_id ); ?></span>
            </div>
            <div class="section-row">
                <strong><?php echo __( "Download do boleto:   ", "wc-yapay_intermediador-bs" ); ?></strong><br>
                <div id="yapay-billet-link">
                    <a target="_blank" href="<?php echo esc_url( $url_payment )?>"><?php echo esc_html( "Imprimir Boleto" ); ?></a>
                </div>
            </div>
            <div class="section-row">
                <strong><?php echo __( "Linha digitável:  ", "wc-yapay_intermediador-bs" ); ?></strong>
                <span><?php echo esc_html( $typeful_line ); ?></span>
            </div>
        </p>
    </div>
</div>