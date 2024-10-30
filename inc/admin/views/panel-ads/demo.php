<ul class="mn-store">

    <li class="mn-store-header">
        <span class="s1" ><?php _e('Description', 'ddabout'); ?></span>
        <span class="s2"><?php _e('Type / Size','ddabout'); ?></span>
        <span class="s3"><?php _e('Price','ddabout'); ?></span>
        <span class="s4"><?php _e('Strategy','ddabout'); ?></span>
    </li>

    <?php foreach ( $ads as $demo ){ ; ?>

        <?php $ad = $adModel->get( $demo->ad_id ); ?>

        <li>
            <span class="s1">
                <strong><?php echo $ad->title; ?></strong>
                <br>
                <?php echo $ad->description; ?>
                <br>
                <?php echo $ad->url; ?>
                <br>
                <img  class="mn-ajax-loader" src="<?php echo plugins_url( '/../../../../assets/images/ajax-loader.gif', __FILE__ ) ?>" >
                <br>

                <a class="mn-btn mn-btn-edit" href="<?php echo admin_url( 'admin.php?page=money-editor&money-id=' . $ad->id ); ?>" target="_blank" >
                    <span class="dashicons dashicons-admin-generic"></span>
                    <span class="mn-btn-title"><?php _e('Edit', 'ddabout'); ?></span>
                </a>

                <a class="mn-btn" href="<?php echo MoneyHelper::getAdPreviewUrl( $ad ); ?>" target="_blank" >
                    <span class="dashicons dashicons-laptop"></span>
                    <span class="mn-btn-title"><?php _e('Preview', 'ddabout'); ?></span>
                </a>

                <button class="mn-btn mn-btn-delete-zone" data-id="<?php echo $demo->id; ?>" >
                    <span class="dashicons dashicons-trash"></span>
                    <span class="mn-btn-title"><?php _e('Delete', 'ddabout'); ?></span>
                </button>

            </span>

            <span class="s2">

                <strong><?php _e('Type', 'ddabout'); ?></strong> : <?php echo MoneyHelper::adContentTypes()[ $ad->content_type ]; ?>
                <br>
                <strong><?php _e('Size', 'ddabout'); ?></strong> : <?php echo MoneyHelper::cssValueDisplayFormat( $ad->style_width )  .' x '.  MoneyHelper::cssValueDisplayFormat( $ad->style_height ); ?>

            </span>

            <span class="s3">
                <strong><?php _e('Price','ddabout'); ?></strong>  : <?php echo $ad->price; ?> <?php echo MONEY_PAYPAL_CURRENCY; ?>
            </span>

            <span class="s4">

                <strong><?php _e('Strategy','ddabout'); ?></strong> : <?php echo $ad->strategy_value .' '. MoneyHelper::adStrategies()[ $ad->strategy_type ]; ?>
                <br>
                <strong><?php _e('Display when','ddabout'); ?></strong> : <?php echo MoneyHelper::adDisplayWhen()[ $ad->display_when ]; ?>
                <?php if( $ad->display_when === 'page_load' ) { ?>
                    <br>
                    <strong><?php _e('Display on','ddabout'); ?></strong> : <?php echo MoneyHelper::displayOnTitles( $ad ); ?>
                <?php } ?>
            </span>

            <span class="s5">
            </span>

        </li>

    <?php } ?>

</ul>

<div class="mn-panel-pagination">
    <?php
    MoneyHelper::pagination(
        $demoTotal,
        5,
        admin_url('admin.php?page=money-ads&mn-panel-page=demo&mn-paged='),
        $paged
    );
    ?>
</div>