<div class="mn-dialog-shadow mn-dialog-shadow-open  " id="mn-dialog-step-step-4">

    <div class="mn-dialog">

        <header>
            <h3><?php _e( 'Plugin Setup', 'ddabout' ); ?></h3>
        </header>

        <main>
            <p>
                <?php _e('Make sure to enable the Membership option, so anyone can register ( required )','ddabout'); ?>
                <a href="<?php echo admin_url('options-general.php');?>">
                    <?php _e('from here','ddabout'); ?>
                </a>
            </p>
        </main>

        <footer>
            <button class="mn-dialog-btn-setup" data-whatToDo="open_step" data-step="5">
                <?php _e('Close', 'ddabout'); ?>
                <img src="<?php echo plugins_url( '/../../../../assets/images/ajax-loader.gif', __FILE__ ) ?>" >
            </button>
            <button class="mn-dialog-btn-setup" data-whatToDo="open_step" data-step="3">
                <?php _e('Prev','ddabout'); ?>
                <img src="<?php echo plugins_url( '/../../../../assets/images/ajax-loader.gif', __FILE__ ) ?>" >
            </button>
        </footer>

    </div>

</div>
