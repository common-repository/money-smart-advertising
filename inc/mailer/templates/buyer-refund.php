<?php _e('Hello','ddabout'); ?> <?php echo $data['buyer_name']; ?>,
<br><br>
<p>
    <?php _e('Your ad has been deleted and a refund of','ddabout'); ?> : <?php echo $data['refund_total']; ?>
    <br>
    <?php _e('is sent to your paypal account.','ddabout'); ?>
</p>
<p>
    <?php _e('You can check your ad statistics from here','ddabout'); ?> :
    <br>
    <?php echo admin_url( 'admin.php?page=money-ad-stats&money-hash='.$data['hash'] ); ?>
</p>
<p>
    <?php _e('Regards', 'ddabout' ); ?>,
    <br>
    <?php echo bloginfo('name'); ?>
</p>
