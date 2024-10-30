<li>
    <span><?php _e('Method','ddabout') ?></span>

    <label>
        <input type="radio" name="complexity" value="simple" <?php if ( $ad->complexity === 'simple' ) echo 'checked="checked"';  ?> >
        <?php _e('Simple ( with close button )','ddabout'); ?><br>
    </label>

    <label>
        <input type="radio" name="complexity" value="advanced" <?php if ( $ad->complexity === 'advanced' ) echo 'checked="checked"';  ?> >
        <?php _e('Advanced ( with timer )','ddabout'); ?>
    </label>
</li>

<li class="mn-item-display-time <?php if ( $ad->complexity === 'simple' ) echo 'mn-hide';  ?>">
    <span><?php _e('Display time','ddabout') ?></span>

    <input type="text" name="advanced_timer" value="<?php echo (int)$ad->advanced_timer ?>">
    <p><?php _e('in seconds','ddabout'); ?></p>
</li>

<li class="mn-item-action <?php if ( $ad->complexity === 'simple' ) echo 'mn-hide';  ?>">
    <span><?php _e('Action','ddabout') ?></span>

    <label>
        <input type="radio" name="advanced_action" value="close" <?php if ( $ad->advanced_action === 'close' ) echo 'checked="checked"';  ?>" >
        <?php _e('Close', 'ddabout'); ?>&nbsp;&nbsp;&nbsp;
    </label>

    <label>
        <input type="radio" name="advanced_action" value="gotolink" <?php if ( $ad->advanced_action === 'gotolink' ) echo 'checked="checked"';  ?>" >
        <?php _e('Go to link','ddabout'); ?>
    </label>

    <input type="text" name="advanced_url" class="<?php if ( $ad->advanced_action === 'close' ) echo 'mn-hide';  ?>" value="<?php echo esc_url( $ad->advanced_url ); ?>" >
</li>

<li class="mn-item-action-text <?php if ( $ad->complexity === 'simple' ) echo 'mn-hide';  ?>">
    <span><?php _e('Action Text','ddabout') ?></span>
    <input type="text" name="advanced_text" value="<?php echo esc_attr( $ad->advanced_text ); ?>">
</li>