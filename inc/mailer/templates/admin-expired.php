<?php _e('Hello','ddabout'); ?>,
<br><br>
<p>
    <?php _e('An ad has been expired','ddabout'); ?>
    <br>
    <?php _e('You can check the ad statistics from here','ddabout'); ?> :
    <br>
    <?php echo admin_url( 'admin.php?page=money-ad-stats&money-hash='.$data['hash'] ); ?>
</p>
<p>
    Wordpress Money Plugin
</p>
