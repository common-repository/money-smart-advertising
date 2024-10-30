<div id="money-ad-<?php echo (int)$ad->id; ?>" class="money-ad <?php echo esc_attr( $ad->content_type ); ?>-ad" data-id="<?php echo (int)$ad->id; ?>" >

    <!-- bar -->
    <div class="money-ad-bar">

        <div class="money-ad-label">
            <a href="<?php echo esc_url( $ad->url ); ?>" target="_blank">
                <?php _e('Ad', 'ddabout'); ?>
            </a>
            <a href="<?php echo esc_url( $ad->url ); ?>" target="_blank">
                <?php
                if( $ad->url != '' ){
                    $url = parse_url( $ad->url );
                    echo $url['host'];
                }
                ?>
            </a>
        </div>

        <?php if( $ad->complexity == 'simple' ): ?>
            <button class="money-ad-close">x</button>
        <?php elseif( $ad->content_type != 'simple' ): ?>
            <span class="money-ad-timer">
                <?php echo esc_html( $ad->advanced_text ); ?>
                (<span><?php echo esc_html( $ad->advanced_timer ); ?></span>)
            </span>
        <?php endif; ?>

    </div>

    <!-- content -->
    <div class="money-ad-content">

        <?php if( $ad->content_type === 'image' ){ ?>

            <img src="<?php echo esc_url( $ad->content ); ?>" alt="<?php echo esc_attr( $ad->title ); ?>">

        <?php } else { ?>

                <?php echo apply_filters( 'money_embed', $ad->content, $ad->content_type ); ?>

        <?php } ?>

        <!-- url -->
        <?php if( $ad->content_type != 'custom' && wp_is_mobile() === false ): ?>

            <a class="money-ad-url" target="_blank" href="<?php echo esc_url( $ad->url ); ?>"></a>

        <?php endif; ?>

    </div>

</div>