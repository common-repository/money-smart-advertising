<li>
    <span><?php _e('Shadow','ddabout'); ?></span>
    <input <?php if( $ad->style_shadow === 'on' ) echo 'checked'; ?> type="checkbox" class="mn-op-enable" id="style_shadow" name="style_shadow" value="true">
    <label class="mn-op-enable-btn" for="style_shadow"><span></span></label>
</li>

<li>
    <span><?php _e('Responsive','ddabout'); ?></span>
    <input <?php if( $ad->style_shadow === 'on' ) echo 'checked'; ?> type="checkbox" class="mn-op-enable" id="style_responsive" name="style_responsive" value="true">
    <label class="mn-op-enable-btn" for="style_responsive"><span></span></label>
</li>

<li>
    <span><?php _e('Width','ddabout'); ?></span>
    <input type="text" name="style_width" value="<?php echo esc_attr( $ad->style_width ); ?>">
</li>

<li>
    <span><?php _e('Height','ddabout'); ?></span>
    <input type="text" name="style_height" value="<?php echo esc_attr( $ad->style_height ); ?>">
</li>

<li>
    <span><?php _e('Position','ddabout'); ?></span>
    <select name="style_position">
        <option value="custom" <?php if( $ad->style_position == 'custom' ) echo 'selected="selected"'; ?> ><?php _e('Custom','ddabout') ?></option>
        <option value="center" <?php if( $ad->style_position == 'center' ) echo 'selected="selected"'; ?> ><?php _e('Center','ddabout') ?></option>
    </select>
</li>

<li class="mn-op-custom-position <?php if( $ad->style_position == 'center' ) echo 'mn-hide'; ?>">
    <span><?php _e('Top','ddabout'); ?></span>
    <input type="text" name="style_top" value="<?php echo esc_attr( $ad->style_top ); ?>">
</li>

<li class="mn-op-custom-position <?php if( $ad->style_position == 'center' ) echo 'mn-hide'; ?>">
    <span><?php _e('Bottom','ddabout'); ?></span>
    <input type="text" name="style_bottom" value="<?php echo esc_attr( $ad->style_bottom ); ?>">
</li>

<li class="mn-op-custom-position <?php if( $ad->style_position == 'center' ) echo 'mn-hide'; ?>">
    <span><?php _e('Left','ddabout'); ?></span>
    <input type="text" name="style_left" value="<?php echo esc_attr( $ad->style_left ); ?>">
</li>

<li class="mn-op-custom-position <?php if( $ad->style_position == 'center' ) echo 'mn-hide'; ?>">
    <span><?php _e('Right','ddabout'); ?></span>
    <input type="text" name="style_right" value="<?php echo esc_attr( $ad->style_right ); ?>">
</li>

