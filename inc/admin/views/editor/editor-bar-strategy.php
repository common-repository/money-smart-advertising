<li>
    <span><?php _e('Price','ddabout'); ?></span>
    <input type="text" name="price" value="<?php echo (float)$ad->price; ?>">
    <?php echo MONEY_PAYPAL_CURRENCY; ?>
</li>

<li>
    <span><?php _e('Strategy','ddabout') ?></span>

    <label>
        <input type="radio" name="strategy_type" value="views" <?php if ( $ad->strategy_type === 'views' ) echo 'checked="checked"';  ?>" >
        <?php _e('Views','ddabout'); ?>&nbsp;&nbsp;&nbsp;
    </label>

    <label>
        <input type="radio" name="strategy_type" value="clicks" <?php if ( $ad->strategy_type === 'clicks' ) echo 'checked="checked"';  ?>" >
        <?php _e('Clicks','ddabout'); ?>&nbsp;&nbsp;&nbsp;
    </label>

    <label>
        <input type="radio" name="strategy_type" value="days" <?php if ( $ad->strategy_type === 'days' ) echo 'checked="checked"';  ?>" >
        <?php _e('Days','ddabout'); ?>
    </label>

    <br>

    <input type="text" name="strategy_value" value="<?php echo (int)$ad->strategy_value; ?>" >
</li>

<li>
    <span><?php _e('Display when','ddabout') ?></span>

    <label>
        <input type="radio" name="display_when" value="page_load" <?php if ( $ad->display_when === 'page_load' ) echo 'checked="checked"';  ?>" >
        <?php _e('Page has fully loaded','ddabout'); ?>
    </label>

    <br>

    <label>
        <input type="radio" name="display_when" value="button_click" <?php if ( $ad->display_when === 'button_click' ) echo 'checked="checked"';  ?>" >
        <?php _e('Click on a button','ddabout'); ?>
    </label>

    <p class="<?php if ( $ad->display_when === 'page_load' ) echo 'mn-hide';  ?>">
        <?php _e('Paste this code into your post / page content','ddabout'); ?>

        <pre class="<?php if ( $ad->display_when === 'page_load' ) echo 'mn-hide';  ?>">
<?php echo '' . htmlspecialchars( '
<button class="mnad" id="mn-'.(int)$adDemo->id.'">
    Download
</button>' );
        ?>
        </pre>
    </p>

</li>

<li class="mn-item-display-on <?php if ( $ad->display_when === 'button_click' ) echo 'mn-hide';  ?>">
    <span><?php _e('Display on','ddabout') ?></span>

    <label>
        <input type="radio" name="display_on" value="all" <?php if ( $ad->display_on == 'all' ) echo 'checked="checked"';  ?>" >
        <?php _e('All','ddabout'); ?>&nbsp;&nbsp;&nbsp;
    </label>

    <label>
        <input type="radio" name="display_on" value="homepage" <?php if ( $ad->display_on == 'homepage' ) echo 'checked="checked"';  ?>" >
        <?php _e('Home Page','ddabout'); ?>&nbsp;&nbsp;&nbsp;
    </label>

    <label>
        <input type="radio" name="display_on" value="pages" <?php if ( $ad->display_on == 'pages' ) echo 'checked="checked"';  ?>" >
        <?php _e('Pages','ddabout'); ?>&nbsp;&nbsp;&nbsp;
    </label>

    <label>
        <input type="radio" name="display_on" value="posts" <?php if ( $ad->display_on == 'posts' ) echo 'checked="checked"';  ?>" >
        <?php _e('Posts','ddabout'); ?>&nbsp;&nbsp;&nbsp;
    </label>

    <div id="mn-display-on-ids" class="<?php if ( $ad->display_on === 'all' || $ad->display_on === 'homepage' ) echo 'mn-hide';  ?>" >
        <?php
            if( $ad->display_on === 'pages' ){
                MoneyHelper::displayOnSelect( 'pages', $ad->display_on_pages, '' );
                MoneyHelper::displayOnSelect( 'posts', $ad->display_on_posts, 'mn-hide' );
            }
            else{
                MoneyHelper::displayOnSelect( 'pages', $ad->display_on_pages, 'mn-hide' );
                MoneyHelper::displayOnSelect( 'posts', $ad->display_on_posts, '' );
            }
        ?>
        <input type="hidden" name="display_on_pages" value="<?php echo esc_attr( $ad->display_on_pages ); ?>">
        <input type="hidden" name="display_on_posts" value="<?php echo esc_attr( $ad->display_on_posts ); ?>">

    </div>
</li>