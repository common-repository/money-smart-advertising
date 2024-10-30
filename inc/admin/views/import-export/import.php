<form method="post">

    <input type="hidden" value="<?php echo wp_create_nonce('money'); ?>" name="nonce">
    <input type="hidden" value="true" name="import_page">

    <p>
        <textarea name="import_code" placeholder="<?php _e('paste the import code here','ddabout'); ?>"></textarea>
    </p>

    <button class="button button-primary" type="submit">
        <?php _e('Import now','ddabout'); ?>
    </button>

</form>