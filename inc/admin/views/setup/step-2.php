<div class="mn-dialog-shadow mn-dialog-shadow-open" id="mn-dialog-step-step-2">

    <div class="mn-dialog">

        <header>
            <h3><?php _e( 'Plugin Setup', 'ddabout' ); ?></h3>
        </header>

        <main>
            <p>
                <?php _e('Select your advertisement page, so we can add the ads store shortcode on it!','ddabout'); ?>

                <br><br>

                <select name="ads_store_page">
                    <?php
                    foreach ( get_pages() as $page ) {
                        $option = '<option value="' .$page->ID. '">';
                        $option .= $page->post_title;
                        $option .= '</option>';
                        echo $option;
                    }
                    ?>
                </select>

            </p>
        </main>

        <footer>
            <button class="mn-dialog-btn-setup" data-whatToDo="select_store_page">
                <?php _e('Save page','ddabout'); ?>
                <img src="<?php echo plugins_url( '/../../../../assets/images/ajax-loader.gif', __FILE__ ) ?>" >
            </button>
            <button class="mn-dialog-btn-setup" data-whatToDo="open_step" data-step="1">
                <?php _e('Prev','ddabout'); ?>
                <img src="<?php echo plugins_url( '/../../../../assets/images/ajax-loader.gif', __FILE__ ) ?>" >
            </button>
        </footer>

    </div>

</div>
