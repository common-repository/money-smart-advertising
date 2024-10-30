<div class="mn-dialog-shadow" id="mn-dialog-success">

    <div class="mn-dialog">

        <header>
            <h3><?php _e( 'Thank you for being one of our sponsors!', 'ddabout' ); ?></h3>
        </header>

        <main>
            <?php _e('Your ad now is pending review by the admin,','ddabout'); ?><br>
            <?php echo __('We sent you an email containing all the information\'s about your ad, to your email address:','ddabout') .' <strong>' . $buyer->user_email . '</strong>'; ?>
            <br>
            <?php _e('Also we will send you an email, once your ad is fully approved.','ddabout'); ?>
            <br><br>
            <h5><?php _e('If you did not receive an email, search your spam folder or contact the admin', 'ddabout'); ?></h5>
        </main>

        <footer>
            <button class="mn-dialog-btn-cancel">
                <?php _e('Close', 'ddabout'); ?>
            </button>
        </footer>

    </div>

</div>