<ul class="mn-store">

    <li class="mn-store-header">
        <span class="s1" ><?php _e('Description','ddabout'); ?></span>
        <span class="s2"><?php _e('Type / Size','ddabout'); ?></span>
        <span class="s3"><?php _e('Price','ddabout'); ?></span>
        <span class="s4"><?php _e('Strategy','ddabout'); ?></span>
        <span class="s5"><?php _e('Statistics','ddabout'); ?></span>
    </li>

    <?php foreach ( $ads as $sold ){ ; ?>

        <?php $ad = $adModel->get( $sold->ad_id ); ?>
        <?php $adStats = $statsModel->get( $sold->statistic_id ); ?>

        <li>
            <span class="s1">
                <strong><?php echo $ad->title; ?></strong>
                <br>
                <?php echo $ad->description; ?>
                <br>
                <?php echo $ad->url; ?>
                <br><br>

                <a class="mn-btn" href="<?php echo MoneyHelper::getAdPreviewUrl( $ad ); ?>" target="_blank" >
                    <span class="dashicons dashicons-laptop"></span>
                    <span class="mn-btn-title"><?php _e('Preview','ddabout'); ?></span>
                </a>

            </span>

            <span class="s2">

                <strong><?php _e('Type', 'ddabout'); ?></strong> : <?php echo MoneyHelper::adContentTypes()[ $ad->content_type ]; ?>
                <br>
                <strong><?php _e('Size', 'ddabout'); ?></strong> : <?php echo MoneyHelper::cssValueDisplayFormat( $ad->style_width )  .' x '.  MoneyHelper::cssValueDisplayFormat( $ad->style_height ); ?>

            </span>

            <span class="s3">
                <strong><?php _e('Price','ddabout'); ?></strong>  : <?php echo $ad->price; ?> <?php echo $sold->currency; ?>
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

                <strong><?php _e('Purchase date','ddabout'); ?></strong> : <?php echo $sold->date_purchase; ?><br><br>

                <strong><?php _e('Impressions','ddabout'); ?></strong> : <?php echo $adStats->total_views; ?><br>
                <strong><?php _e('Clicks','ddabout'); ?></strong> : <?php echo $adStats->total_clicks; ?><br>
                <strong><?php _e('Days','ddabout'); ?></strong> : <?php echo $adStats->total_days; ?><br><br>

            </span>

        </li>

    <?php } ?>

</ul>

<div class="mn-panel-pagination">
    <?php
    MoneyHelper::pagination(
        $pendingTotal,
        5,
        admin_url('admin.php?page=money-user-ads&mn-panel-page=pending&mn-paged='),
        $paged
    );
    ?>
</div>