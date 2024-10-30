<div class="mn-dialog-shadow mn-dialog-shadow-open  " id="mn-dialog-step-step-3">

    <div class="mn-dialog">

        <header>
            <h3><?php _e( 'Plugin Setup', 'ddabout' ); ?></h3>
        </header>

        <main>
            <p>
                <?php _e('Set your paypal settings','ddabout'); ?>
                <a href="<?php echo admin_url('admin.php?page=money-settings&mn-panel-page=paypal');?>">
                    <?php _e('from here','ddabout'); ?>
                </a>
            </p>
        </main>

        <footer>
            <button class="mn-dialog-btn-setup" data-whatToDo="open_step" data-step="4">
                <?php _e('Next','ddabout'); ?>
                <img src="<?php echo plugins_url( '/../../../../assets/images/ajax-loader.gif', __FILE__ ) ?>" >
            </button>
            <button class="mn-dialog-btn-setup" data-whatToDo="open_step" data-step="2">
                <?php _e('Prev','ddabout'); ?>
                <img src="<?php echo plugins_url( '/../../../../assets/images/ajax-loader.gif', __FILE__ ) ?>" >
            </button>
        </footer>

    </div>

</div>
