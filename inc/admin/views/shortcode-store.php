<ul class="mn-store">

    <li class="mn-store-header">
        <span class="s1" ><?php _e('Description', 'ddabout'); ?></span>
        <span class="s2"><?php _e('Type / Size','ddabout'); ?></span>
        <span class="s3"><?php _e('Strategy','ddabout'); ?></span>
        <span class="s4"><?php _e('Price','ddabout'); ?></span>
        <span class="s5"><?php _e('Buy','ddabout'); ?></span>
    </li>

    <?php foreach ( $adsDemoModel->all( array( 'order_by' => 'id DESC' ) ) as $demo ){ ; ?>

        <?php $ad = $adsModel->get( $demo->ad_id ); ?>

        <li>
            <span class="s1">
                <strong><?php echo esc_html( $ad->title ); ?></strong>
                <br>
                <?php echo esc_html( $ad->description ); ?>
            </span>

            <span class="s2">

                <strong><?php _e('Type', 'ddabout'); ?></strong> : <?php echo esc_html( MoneyHelper::adContentTypes()[ $ad->content_type ] ); ?>
                <br>
                <strong><?php _e('Size', 'ddabout'); ?></strong> : <?php echo esc_html( MoneyHelper::cssValueDisplayFormat( $ad->style_width )  .' x '.  MoneyHelper::cssValueDisplayFormat( $ad->style_height ) ); ?>

            </span>

            <span class="s3">

                <strong><?php _e('Strategy','ddabout'); ?></strong> : <?php echo (int)$ad->strategy_value .' '. esc_html( MoneyHelper::adStrategies()[ $ad->strategy_type ] ); ?>
                <br>
                <strong><?php _e('Display when','ddabout'); ?></strong> : <?php echo esc_html( MoneyHelper::adDisplayWhen()[ $ad->display_when ] ); ?>

                <?php if( $ad->display_when === 'page_load' ) { ?>
                    <br>
                    <strong><?php _e('Display on','ddabout'); ?></strong> : <?php echo esc_html( MoneyHelper::displayOnTitles( $ad ) ); ?>
                <?php } ?>

            </span>

            <span class="s4">
                <strong><?php _e('Price','ddabout'); ?></strong>  : <?php echo (float)$ad->price; ?> <?php echo esc_html( MONEY_PAYPAL_CURRENCY ); ?>
            </span>

            <span class="s5">

                <img class="mn-ajax-loader" src="<?php echo plugins_url( '/../../../assets/images/ajax-loader.gif', __FILE__ ) ?>" >
                <br>
                <?php if( $adsSold->isDemoAdActive( $demo->id ) === false ) { ?>
                    <a class="mn-btn mn-btn-checkout" href="#" data-demo_id="<?php echo (int)$demo->id; ?>" >
                        <span class="dashicons dashicons-cart"></span>
                        <span class="mn-btn-title"><?php _e('Buy now','ddabout'); ?></span>
                    </a>
                <?php } else{ ?>
                    <?php _e('Add your add to the Queue!','ddabout') ?>
                    <br>
                    <a class="mn-btn mn-btn-checkout" href="#" data-demo_id="<?php echo (int)$demo->id; ?>" >
                        <span class="dashicons dashicons-cart"></span>
                        <span class="mn-btn-title"><?php _e('Waiting list','ddabout'); ?></span>
                    </a>
                <?php } ?>

                <a class="mn-btn" href="<?php echo esc_url( MoneyHelper::getAdPreviewUrl( $ad ) ); ?>" target="_blank" >
                    <span class="dashicons dashicons-laptop"></span>
                    <span class="mn-btn-title"><?php _e('Preview','ddabout'); ?></span>
                </a>

            </span>

        </li>

    <?php } ?>

</ul>