<div id="mn-sidebar" class="mn-sidebar">

    <input type="hidden" id="mn-ajaxurl" value="<?php echo admin_url( 'admin-ajax.php' ); ?>">
    <input type="hidden" id="mn-nonce" value="<?php echo wp_create_nonce('money'); ?>">
    <input type="hidden" id="mn-adid" value="<?php echo (int)$ad->id; ?>">
    <input type="hidden" id="mn-siteurl" value="<?php bloginfo( 'wpurl' ); ?>">
    <input type="hidden" id="mn-random-id-posts" value="<?php echo (int)MoneyHelper::getRandomPostID( 'post' ); ?>">
    <input type="hidden" id="mn-random-id-pages" value="<?php echo (int)MoneyHelper::getRandomPostID( 'page' ); ?>">

    <header>
        <a href="<?php echo admin_url( 'admin.php?page=money-ads' ); ?>" class="button mn-btn-back">
            <?php _e('Back','ddabout'); ?>
        </a>

        <div class="mn-responsive-icons">
            <span class="active dashicons dashicons-desktop"></span>
            <span class="dashicons dashicons-tablet"></span>
            <span class="dashicons dashicons-smartphone"></span>
            <span class="dashicons dashicons-arrow-left-alt2"></span>
            <span class="dashicons dashicons-arrow-right-alt2"></span>
        </div>

        <div class="mn-clearfix"></div>
    </header>

    <div class="mn-sections-wrap">

        <div class="mn-section">
            <h3>
                <?php _e('Basic','ddabout'); ?>
                <span class="dashicons"></span>
            </h3>
            <ul><?php require __DIR__ . '/editor-bar-basic.php'; ?></ul>
        </div>

        <?php if( $editor_mode === 'demo_ad' ) { ?>
        <div class="mn-section">
            <h3>
                <?php _e('Strategy','ddabout'); ?>
                <span class="dashicons"></span>
            </h3>
            <ul><?php require __DIR__ . '/editor-bar-strategy.php'; ?></ul>
        </div>
        <?php } ?>

        <div class="mn-section">
            <h3>
                <?php _e('Advanced','ddabout'); ?>
                <span class="dashicons"></span>
            </h3>
            <ul><?php require __DIR__ . '/editor-bar-advanced.php'; ?></ul>
        </div>

        <div class="mn-section">
            <h3>
                <?php _e('Style','ddabout'); ?>
                <span class="dashicons"></span>
            </h3>
            <ul><?php require __DIR__ . '/editor-bar-style.php'; ?></ul>
        </div>

    </div>

</div>