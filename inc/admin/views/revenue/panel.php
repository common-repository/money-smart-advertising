<div class="wrap">
    
    <!--title -->
    <h1>
        <?php _e('Revenue Report','ddabout'); ?>
    </h1>

    <!--panel-->
    <div class="mn-panel">

        <ul class="mn-panel-menu">

            <li class="<?php if( $panelPage === 'today') echo 'active'; ?>" >
                <a href="<?php echo admin_url('admin.php?page=money-revenue&mn-panel-page=today'); ?>">
                    <?php echo __('Today','ddabout'); ?>
                </a>
            </li>

            <li class="<?php if( $panelPage === 'month') echo 'active'; ?>" >
                <a href="<?php echo admin_url('admin.php?page=money-revenue&mn-panel-page=month'); ?>">
                    <?php echo __('Month','ddabout'); ?>
                </a>
            </li>

            <li class="<?php if( $panelPage === 'year') echo 'active'; ?>" >
                <a href="<?php echo admin_url('admin.php?page=money-revenue&mn-panel-page=year'); ?>">
                    <?php echo __('Year','ddabout'); ?>
                </a>
            </li>

            <li class="<?php if( $panelPage === 'all') echo 'active'; ?>" >
                <a href="<?php echo admin_url('admin.php?page=money-revenue&mn-panel-page=all'); ?>">
                    <?php echo __('All time','ddabout'); ?>
                </a>
            </li>

        </ul>

        <div class="mn-panel-content">

            <div class="mn-panel-page active">
                <?php require __DIR__ . '/template.php'; ?>
            </div>

        </div>

    </div>

</div>
