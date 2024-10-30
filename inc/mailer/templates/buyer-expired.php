<?php echo __('Hello','ddabout') ." ". $data['buyer_name'] .","; ?>
<br><br>
<p>
    <?php _e('Your ad has been expired','ddabout');?>
    <br>
    <?php _e('You can check your ad statistics from here','ddabout'); ?> :
    <br>
    <?php echo admin_url( 'admin.php?page=money-ad-stats&money-hash='.$data['hash'] ); ?>
</p>
<p>
    <?php _e( 'Regards', 'ddabout' ); ?>,
    <br>
    <?php echo bloginfo('name'); ?>
</p>