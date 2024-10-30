<form method="post">

    <input type="hidden" value="<?php echo wp_create_nonce('money'); ?>" name="nonce">
    <input type="hidden" value="true" name="export_page">

    <p <?php if( ! isset( $_POST['export_page'] ) ) echo 'style="display:none !important"'; ?> >
        <textarea><?php echo esc_html( $data ); ?></textarea>
    </p>

    <button class="button button-primary" type="submit" <?php if( isset( $_POST['export_page'] ) ) echo 'style="display:none !important"'; ?>>
        <?php _e('Get the export code','ddabout'); ?>
    </button>

</form>