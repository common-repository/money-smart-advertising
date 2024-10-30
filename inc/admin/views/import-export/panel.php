<div class="wrap" id="money-page-import-export">
    
    <!--title -->
    <h1>
        <?php _e('Import / Export','ddabout'); ?>
    </h1>

    <!--panel-->
    <div class="mn-panel">

        <ul class="mn-panel-menu">

            <li class="<?php if( $panelPage === 'export' ) echo 'active'; ?>" >
                <a href="<?php echo admin_url('admin.php?page=money-export-import&mn-panel-page=export'); ?>">
                    <?php _e('Export','ddabout'); ?>
                </a>
            </li>

            <li class="<?php if( $panelPage === 'import' ) echo 'active'; ?>" >
                <a href="<?php echo admin_url('admin.php?page=money-export-import&mn-panel-page=import'); ?>">
                    <?php _e('Import','ddabout'); ?>
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
