<div class="wrap">
    
    <?php MoneyPluginSetup::displayDialog(); ?>

    <!--title -->
    <h1>
        <?php _e('Ads list','ddabout'); ?>
        <button class="button button-primary" id="money-btn-add" >
            <?php _e('Add New Ad Zone','ddabout'); ?>
        </button>
    </h1>

    <!--panel-->
    <div class="mn-panel">

        <ul class="mn-panel-menu">

            <li class="<?php if( $panelPage === 'demo') echo 'active'; ?>" >
                <a href="<?php echo admin_url('admin.php?page=money-ads&mn-panel-page=demo'); ?>">
                    <?php echo __('Ad Zones','ddabout') . ' ('.$demoTotal.')'; ?>
                </a>
            </li>

            <li class="<?php if( $panelPage === 'active') echo 'active'; ?>" >
                <a href="<?php echo admin_url('admin.php?page=money-ads&mn-panel-page=active'); ?>">
                    <?php echo __('Active','ddabout') . ' ('.$activeTotal.')'; ?>
                </a>
            </li>

            <li class="<?php if( $panelPage === 'pending') echo 'active'; ?>" >
                <a href="<?php echo admin_url('admin.php?page=money-ads&mn-panel-page=pending'); ?>">
                    <?php echo __('Pending','ddabout') . ' ('.$pendingTotal.')'; ?>
                </a>
            </li>

            <li class="<?php if( $panelPage === 'expired') echo 'active'; ?>" >
                <a href="<?php echo admin_url('admin.php?page=money-ads&mn-panel-page=expired'); ?>">
                    <?php echo __('Expired','ddabout') . ' ('.$expiredTotal.')'; ?>
                </a>
            </li>

            <li class="<?php if( $panelPage === 'refunded') echo 'active'; ?>" >
                <a href="<?php echo admin_url('admin.php?page=money-ads&mn-panel-page=refunded'); ?>">
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
