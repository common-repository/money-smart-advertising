<div class="mn-dialog-shadow" id="mn-dialog-login">

    <div class="mn-dialog">

        <header>
            <h3><?php _e( 'Please login / register before you continue!', 'ddabout' ); ?></h3>
        </header>

        <main></main>

        <footer>
            <button class="mn-dialog-btn-cancel">
                <?php _e('Close', 'ddabout'); ?>
            </button>

            <a href="<?php echo $registerUrl; ?>" class="mn-dialog-btn">
                <?php _e('Register','ddabout'); ?>
            </a>

            <a href="<?php echo $loginUrl; ?>" class="mn-dialog-btn">
                <?php _e('Login','ddabout'); ?>
            </a>
        </footer>

    </div>

</div>