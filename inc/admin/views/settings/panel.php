<div class="wrap">
    
    <!--title -->
    <h1>
        <?php _e('Settings','ddabout'); ?>
    </h1>

    <!--panel-->
    <div class="mn-panel">

        <ul class="mn-panel-menu">

            <li class="<?php if( $panelPage === 'general') echo 'active'; ?>" >
                <a href="<?php echo admin_url('admin.php?page=money-settings&mn-panel-page=general'); ?>">
                    <?php _e('General','ddabout'); ?>
                </a>
            </li>

            <li class="<?php if( $panelPage === 'paypal') echo 'active'; ?>" >
                <a href="<?php echo admin_url('admin.php?page=money-settings&mn-panel-page=paypal'); ?>">
                    <?php _e('Paypal','ddabout'); ?>
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
