<table border="1">
    <tr class="header">
        <td><?php _e('Currency','ddabout'); ?></td>
        <td><?php _e('Total','ddabout'); ?></td>
        <td><?php _e('Total refund','ddabout'); ?></td>
        <td><?php _e('Net','ddabout'); ?></td>
    </tr>
<?php foreach ( $revenueArr as $revByCurrency => $revVal ) { ?>
    <tr>
        <td>
            <?php echo $revByCurrency; ?>
        </td>

        <td>
            <?php echo $revVal['total_price']; ?>
        </td>
        
        <td>
            <?php echo $revVal['total_refund']; ?>
        </td>

        <td>
            <?php echo $revVal['total_price'] - $revVal['total_refund']; ?>
        </td>

    </tr>
<?php } ?>
</table>

<ul class="mn-store">

    <li class="mn-store-header">
        <span class="s1" ><?php _e('Ad','ddabout'); ?></span>
        <span class="s2"><?php _e('Status','ddabout'); ?></span>
        <span class="s3"><?php _e('Buyer','ddabout'); ?></span>
        <span class="s4"><?php _e('Price','ddabout'); ?></span>
        <span class="s5"><?php _e('Refund','ddabout'); ?></span>
    </li>

    <?php foreach ( $allSold as $sold ){ ; ?>

        <?php
        $ad = $adsModel->get( $sold->ad_id );
        $buyer = MoneyHelper::userData( $sold->buyer_id );
        $buyerLink = get_edit_user_link( $sold->buyer_id );
        $statUrl = admin_url('admin.php?page=money-ad-stats&money-hash='.$sold->hash);
        ?>

        <li>
            <span class="s1">
                <a href="<?php echo $statUrl ?>" target="_blank"><?php echo $ad->title; ?></a>
                <br>
                <?php echo $ad->description; ?>
            </span>

            <span class="s2">
                <strong><?php echo MoneyHelper::adStatusStr()[ $sold->status ]; ?></strong>
            </span>

            <span class="s3">

                <a href="<?php echo $buyerLink; ?>" target="_blank">
                    <?php echo $buyer->user_login; ?>
                </a>

            </span>

            <span class="s4">

                <strong><?php _e('Price','ddabout'); ?></strong>  : <?php echo $ad->price; ?> <?php echo $sold->currency; ?>

            </span>

            <span class="s5">
                <?php if( $sold->status === 'refunded' ) { ?>

                    <strong>
                        <?php
                        $total = MoneyHelper::fixPaypalFloatIssue( $ad->price );

                        if( MoneyHelper::fixPaypalFloatIssue( $sold->refund_amount ) == $total ) {
                            _e('Complete refund','ddabout');
                        } else {
                            _e('Partial refund','ddabout');
                        }
                        ?>
                    </strong>

                    <br><br>

                    <strong><?php _e('Amount','ddabout'); ?></strong> : <?php echo $sold->refund_amount; ?>
                    <br>
                    <strong><?php _e('Currency','ddabout'); ?></strong> : <?php echo $sold->currency; ?>

                <?php } ?>
            </span>

        </li>

    <?php } ?>

</ul>


<div class="mn-panel-pagination">
    <?php
    MoneyHelper::pagination(
        $adsCount,
        5,
        admin_url('admin.php?page=money-revenue&mn-panel-page='.$panelPage.'&mn-paged='),
        $paged
    );
    ?>
</div>