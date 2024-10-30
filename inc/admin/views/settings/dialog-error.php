<div class="mn-dialog-shadow <?php if( isset( $showDialog ) ) echo 'mn-dialog-shadow-open' ?>">

    <div class="mn-dialog">

        <header>
            <h3>
                <?php
                if( isset( $dialogTitle ) ) {
                    echo $dialogTitle;
                }
                else {
                    _e('Error(s)', 'ddabout');
                }
                ?>
            </h3>
        </header>

        <main>
            <ul class="mn-dialog-errors">
                <?php if( isset( $dialogErrors ) ) echo $dialogErrors; ?>
            </ul>
        </main>

        <footer>
            <button class="mn-dialog-btn-cancel">
                <?php _e('Close', 'ddabout'); ?>
            </button>
        </footer>

    </div>

</div>