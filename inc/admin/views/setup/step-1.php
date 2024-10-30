<div class="mn-dialog-shadow mn-dialog-shadow-open" id="mn-dialog-step-step-1">

    <div class="mn-dialog">

        <header>
            <h3><?php _e( 'Plugin Setup', 'ddabout' ); ?></h3>
        </header>

        <main>
            <p>
                <?php _e('Create an advertisement page, so you can display the available ads on it!','ddabout'); ?>
            </p>
        </main>

        <footer>
            <button class="mn-dialog-btn-setup" data-whatToDo="create_store_page">
                <?php _e('Create page','ddabout'); ?>
                <img src="<?php echo plugins_url( '/../../../../assets/images/ajax-loader.gif', __FILE__ ) ?>" >
            </button>
            <button class="mn-dialog-btn-setup" data-whatToDo="open_step" data-step="2">
                <?php _e('I have a page!','ddabout'); ?>
                <img src="<?php echo plugins_url( '/../../../../assets/images/ajax-loader.gif', __FILE__ ) ?>" >
            </button>
        </footer>

    </div>

</div>
