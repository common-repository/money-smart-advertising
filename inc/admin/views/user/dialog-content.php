<div class="mn-dialog-shadow mn-dialog-shadow-open" id="mn-dialog-buyer-content">

    <div class="mn-dialog">

        <header>
            <h3><?php _e( 'Final step, add your content', 'ddabout' ); ?></h3>
        </header>

        <main>
            <input type="hidden" id="mn-buyer-soldAd-id" value="<?php echo $adSold->id; ?>">

            <input id="mn-buyer-url" type="text" placeholder="<?php _e('Add your site url','ddabout'); ?>" value="" >
            <textarea id="mn-buyer-content" placeholder="<?php echo $contentTypePlaceHolder; ?>"></textarea>
            <ul id="mn-dialog-errors"></ul>

            <h5><?php _e('Do not reload the page before adding your content!', 'content'); ?></h5>
        </main>

        <footer>
            <button id="mn-dialog-btn-save">
                <?php _e('Save','ddabout'); ?>
                <img src="<?php echo plugins_url( '/../../../../assets/images/ajax-loader.gif', __FILE__ ) ?>" >
            </button>

            <button class="mn-dialog-btn-cancel">
                <?php _e('Cancel','ddabout'); ?>
            </button>
        </footer>

    </div>

</div>