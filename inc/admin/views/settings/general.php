<form method="post">

    <input type="hidden" value="<?php echo wp_create_nonce('money'); ?>" name="nonce">

    <p>
        <strong><?php _e('Disable ads for','ddabout'); ?></strong>
        <br>
        <input type="checkbox" name="disableAdsAdmin" value="on" <?php if( MONEY_SETTINGS_DISABLE_ADS_ADMIN === 'on' ) echo 'checked'; ?> >
        <?php _e('Administrator','ddabout'); ?>
        <br>
        <input type="checkbox" name="disableAdsLoggedIn" value="on" <?php if( MONEY_SETTINGS_DISABLE_ADS_LOGGED_IN === 'on' ) echo 'checked'; ?> >
        <?php _e('Logged in users','ddabout'); ?>
    </p>

    <p>
        <strong><?php _e('Load ad after ','ddabout'); ?></strong>
        <br>
        <input type="text" name="loadAdAfter" value="<?php echo intval( MONEY_SETTINGS_LOAD_AFTER ); ?>">
        <?php _e('seconds','ddabout'); ?>
    </p>

    <p>
        <strong><?php _e('Email Address','ddabout'); ?></strong>
        <br>
        <input type="text" name="notificationsEmail" value="<?php echo esc_attr( MONEY_SETTINGS_EMAIL ); ?>">
        <br>
        <?php _e('You will receive notifications only!','ddabout'); ?>
    </p>

    <button class="button button-primary" type="submit">
        <?php _e('Save','ddabout'); ?>
    </button>

</form>