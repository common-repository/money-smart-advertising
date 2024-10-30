<div id="mn-iframe-wrap">
    <iframe id="mn-iframe" class="mn-iframe" width="100%" height="100%" src="<?php echo esc_url( MoneyHelper::getAdPreviewUrl( $ad ) ); ?>"></iframe>

    <div id="mn-loading" class="mn-hide">
        <div class="cssload-thecube">
            <div class="cssload-cube cssload-c1"></div>
            <div class="cssload-cube cssload-c2"></div>
            <div class="cssload-cube cssload-c4"></div>
            <div class="cssload-cube cssload-c3"></div>
        </div>
    </div>

    <div id="mn-errors" class="mn-hide">
        <h3><?php _e('Error(s)','ddabout'); ?></h3>
        <ul></ul>
    </div>

</div>