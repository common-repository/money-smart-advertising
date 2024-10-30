<?php _e('Hello','ddabout'); ?> <?php echo $data['buyer_name']; ?>,
<br><br>
<p>
    <?php _e('Your ad is pending review by the admin','ddabout'); ?>,
    <br>
    <?php _e('We will send you an email, once your ad is fully approved.','ddabout'); ?>
</p>
<p>
    <?php _e('Ad','ddabout'); ?> : <?php echo $data['ad_title']; ?>
    <br>
    <?php _e('Url','ddabout'); ?> : <?php echo $data['buyer_url']; ?>
    <br>
    <?php _e('Content','ddabout'); ?> : <?php echo htmlspecialchars( $data['buyer_content']); ?>
    <br>
    <?php _e('Price','ddabout'); ?> : <?php echo $data['total']; ?>
</p>
<p>
    <?php _e('Regards', 'ddabout' ); ?>
    <br>
    <?php echo bloginfo('name'); ?>
</p>
