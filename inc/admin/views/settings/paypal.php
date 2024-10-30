<form method="post">

    <input type="hidden" value="<?php echo wp_create_nonce('money'); ?>" name="nonce">

    <p>
        <strong><?php _e('Environment','ddabout'); ?></strong>
        <br>
        <input type="radio" name="paypalMode" value="live" <?php if( MONEY_PAYPAL_MODE === 'live' ) echo 'checked'; ?> ><?php _e('Live - Production','ddabout'); ?>
        <br>
        <input type="radio" name="paypalMode" value="sandbox" <?php if( MONEY_PAYPAL_MODE === 'sandbox' ) echo 'checked'; ?> ><?php _e('Sandbox - Testing','ddabout'); ?>
    </p>

    <p>
        <strong><?php _e('Client ID','ddabout'); ?></strong>
        <br>
        <input type="password" name="clientID" value="<?php echo esc_attr( MONEY_PAYPAL_CLIENT_ID ); ?>">
    </p>

    <p>
        <strong><?php _e('Secret','ddabout'); ?></strong>
        <br>
        <input type="password" name="secret" value="<?php echo esc_attr( MONEY_PAYPAL_SECRET ); ?>">
    </p>

    <p>
        <strong><?php _e('Currency', 'ddabout'); ?></strong>
        <br>
        <select name="currency">
            <?php
            foreach ( MoneyHelper::adCurrencies() as $currency_key => $currency_name ) {
                if( MONEY_PAYPAL_CURRENCY == $currency_key ){
                    echo '<option value="' . esc_attr( $currency_key ) . '" selected="selected" >' . esc_html( $currency_name ) . '</option>';
                }
                else{
                    echo '<option value="' . esc_attr( $currency_key ) . '" >' . esc_html( $currency_name ) . '</option>';
                }
            }
            ?>
        </select>
        <br>
        <?php _e('You cant change currency when you are using sandbox Environment','ddabout'); ?>
    </p>

    <button class="button button-primary" type="submit">
        <?php _e('Save','ddabout'); ?>
    </button>

</form>