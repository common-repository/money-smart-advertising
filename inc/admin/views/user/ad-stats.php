<div class="wrap">

    <h1>
        <span class="dashicons dashicons-chart-line"></span>
        <?php _e('Statistics','ddabout'); ?>
    </h1>

    <ul class="mn-store">

        <li class="mn-store-header">
            <span class="s1" ><?php _e('Description', 'ddabout'); ?></span>
            <span class="s2"><?php _e('Type / Size','ddabout'); ?></span>
            <span class="s3"><?php _e('Price','ddabout'); ?></span>
            <span class="s4"><?php _e('Strategy','ddabout'); ?></span>
            <span class="s5"><?php _e('Statistics','ddabout'); ?></span>
        </li>

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

                <strong><?php _e('Purchase date','ddabout'); ?></strong> : <?php echo $sold->date_purchase; ?><br>
                <strong><?php _e('Start date','ddabout'); ?></strong> : <?php echo $sold->date_start; ?><br>

                <?php if( $sold->status === 'expired' ){ ?>
                    <strong><?php _e('Expire date','ddabout'); ?></strong> : <?php echo $sold->date_start; ?><br>
                <?php } elseif( $sold->status === 'refunded' ){ ?>
                    <strong><?php _e('Refund date','ddabout'); ?></strong> : <?php echo $sold->date_start; ?><br>
                <?php } ?>

                <br>

                <strong><?php _e('Impressions','ddabout'); ?></strong> : <?php echo $stats->total_views; ?><br>
                <strong><?php _e('Clicks','ddabout'); ?></strong> : <?php echo $stats->total_clicks; ?><br>
                <strong><?php _e('Days','ddabout'); ?></strong> : <?php echo $stats->total_days; ?><br><br>

            </span>

        </li>

    </ul>

    <div id="money-chart-container">

        <div class="money-chart-buttons">

            <a href="<?php echo $statUrl; ?>" class="money-chart-btn <?php if( $statsType === 'views' ) echo 'active'; ?>">
                <?php _e('Views','ddabout'); ?>
            </a>
            <a href="<?php echo $statUrl . '&money-stats-type=clicks'; ?>" class="money-chart-btn <?php if( $statsType === 'clicks' ) echo 'active'; ?>">
                <?php _e('Clicks','ddabout'); ?>
            </a>

            <p>
                <?php _e('By','ddabout'); ?>:

                <input type="hidden" name="mn-chart-type" value="<?php echo $statsType; ?>">
                <input type="hidden" name="mn-stat-url" value="<?php echo $statUrl; ?>">

                <select name="mn-chart-per">
                    <?php foreach(
                        array(
                            'year' => __('Year','ddabout'),
                            'month' => __('Month','ddabout'),
                            'week' => __('Week','ddabout')
                        ) as $typeKey => $typeStr ) { ?>
                        <option value="<?php echo $typeKey ?>" <?php if( $statsPer === $typeKey ) echo 'selected="selected"'; ?> >
                            <?php echo $typeStr; ?>
                        </option>
                    <?php } ?>
                </select> -

                <select name="mn-chart-week" <?php if( $statsPer === 'year' || $statsPer === 'month' ) echo 'style="visibility:hidden; width:0px !important;"' ?>>
                    <?php foreach( $allWeeks[ $selectedYear ][ $selectedMonth ] as $weekKey => $weekVal ) { ?>

                        <option value="<?php echo $weekKey; ?>" <?php if( $selectedWeek == $weekKey ) echo 'selected="selected"'; ?>>
                            <?php echo $weekStr .' '. $weekVal; ?>
                        </option>

                    <?php } ?>
                </select>

                <select name="mn-chart-month" <?php if( $statsPer === 'year' ) echo 'style="visibility:hidden; width:0px !important;"' ?>>
                    <?php foreach( $allMonths[ $selectedYear ] as $monthKey => $monthVal ) { ?>

                        <option value="<?php echo $monthKey; ?>" <?php if( $selectedMonth == $monthKey ) echo 'selected="selected"'; ?>>
                            <?php echo date('F', mktime(0, 0, 0, intval( $monthVal ), 10)); ?>
                        </option>

                    <?php } ?>
                </select>

                <select name="mn-chart-year">
                    <?php foreach( $allYears as $yearKey => $yearVal ) { ?>
                        <option value="<?php echo $yearKey; ?>" <?php if( $selectedYear == $yearKey ) echo 'selected="selected"'; ?> >
                            <?php echo $yearKey; ?>
                        </option>
                    <?php } ?>
                </select>

            </p>

        </div>

        <div class="money-chart-wrap">

            <?php if( ! count( $statsDb ) ){ ?>

                <p><?php _e('No statistics available','ddabout'); ?></p>

            <?php } else { ?>

                <canvas id="money-chart" width="700" height="400"></canvas>
                <script type="text/javascript">

                    (function ( $, $document, $window ) {

                        var ctx = document.getElementById("money-chart");
                        var myChart = new Chart(ctx, {
                            type: 'line',
                            maintainAspectRatio: false,
                            data: {
                                labels: <?php echo $chartLabels; ?>,
                                datasets: [{
                                    fill: false,
                                    label: '<?php if( $statsType === 'views' ) _e('Views','ddabout'); else _e('Clicks','ddabout') ?>',
                                    pointBorderColor: "red",
                                    pointBackgroundColor: "red",
                                    pointBorderWidth: 6,
                                    pointHoverRadius: 9,
                                    data: <?php echo $chartData; ?>
                                }]
                            },
                            options: {
                                scales: {
                                    yAxes: [{
                                        ticks: {
                                            <?php if( ! $maxSizeBiggerThan5 ) echo 'stepSize: 5,'; ?>
                                            beginAtZero:true
                                        }
                                    }]
                                }
                            }
                        });

                    })( jQuery, jQuery(document), jQuery( window ) );

                </script>

            <?php } ?>

        </div>

    </div>

    <div id="money-countries-wrap">

        <h3><?php _e('Countries','ddabout'); ?></h3>

        <table border="0">

            <tr class="table-header">
                <td>Flag</td>
                <td>Code</td>
                <td>Impressions</td>
                <td>Clicks</td>
            </tr>

            <?php

            foreach ( $countries as $countryCode => $details ){ ?>

                <tr>
                    <td>
                        <img src="<?php echo plugins_url( '/../../../../assets/icons/flags/'.strtolower( $countryCode ).'.png', __FILE__ ) ?>" >
                    </td>
                    <td><?php echo $countryCode; ?></td>
                    <td><?php echo $details['views']; ?></td>
                    <td><?php echo $details['clicks']; ?></td>
                </tr>

            <?php } ?>

        </table>
    </div>

</div>