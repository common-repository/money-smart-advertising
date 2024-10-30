<div class="wrap">
    
    <!--title -->
    <h1>
        <?php _e('Your ads','ddabout'); ?>
        <a href="<?php echo admin_url('admin.php?page=money-store'); ?>" class="button button-primary" ><?php _e('Store','ddabout'); ?></a>
    </h1>

    <!--panel-->
    <div class="mn-panel">

        <ul class="mn-panel-menu">

            <li class="<?php if( $panelPage === 'active') echo 'active'; ?>" >
                <a href="<?php echo admin_url('admin.php?page=money-user-ads&mn-panel-page=active'); ?>">
                    <?php echo __('Active','ddabout') . ' ('.$activeTotal.')'; ?>
                </a>
            </li>

            <li class="<?php if( $panelPage === 'pending') echo 'active'; ?>" >
                <a href="<?php echo admin_url('admin.php?page=money-user-ads&mn-panel-page=pending'); ?>">
                    <?php echo __('Pending','ddabout') . ' ('.$pendingTotal.')'; ?>
                </a>
            </li>

            <li class="<?php if( $panelPage === 'expired') echo 'active'; ?>" >
                <a href="<?php echo admin_url('admin.php?page=money-user-ads&mn-panel-page=expired'); ?>">
                    <?php echo __('Expired','ddabout') . ' ('.$expiredTotal.')'; ?>
                </a>
            </li>

            <li class="<?php if( $panelPage === 'refunded') echo 'active'; ?>" >
                <a href="<?php echo admin_url('admin.php?page=money-user-ads&mn-panel-page=refunded'); ?>">
                    <?php echo __('Refunded','ddabout') . ' ('.$refundedTotal.')'; ?>
                </a>
            </li>

        </ul>

        <div class="mn-panel-content">

            <div class="mn-panel-page active">
                <?php require __DIR__ . '/' . $panelPage . '.php'; ?>
            </div>

        </div>

    </div>

</div>
