<li>
    <span><?php _e('Title','ddabout'); ?></span>
    <input type="text" name="title" value="<?php echo esc_attr( $ad->title ); ?>">
</li>

<li>
    <span><?php _e('Description'); ?></span>
    <textarea name="description"><?php echo esc_html( $ad->description ); ?></textarea>
</li>

<li>
    <span><?php _e('Content','ddabout'); ?></span>

    <label>
        <input type="radio" name="content_type" value="image" <?php if( $ad->content_type == 'image' ) echo 'checked="checked"'; ?> >
        <?php _e('Image','ddabout'); ?>&nbsp;&nbsp;&nbsp;
    </label>

    <label>
        <input type="radio" name="content_type" value="video" <?php if( $ad->content_type == 'video') echo 'checked="checked"'; ?> >
        <?php _e('Video','ddabout'); ?>&nbsp;&nbsp;&nbsp;
    </label>

    <label>
        <input type="radio" name="content_type" value="audio" <?php if( $ad->content_type == 'audio') echo 'checked="checked"'; ?>>
        <?php _e('Audio','ddabout'); ?>&nbsp;&nbsp;&nbsp;
    </label>

    <br>

    <button class="button button-primary" id="mn-open-wp-media">
        <?php _e('Add Media','ddabout'); ?>
    </button>

    <textarea name="content"><?php echo esc_html( $ad->content ); ?></textarea>
</li>

<li">
    <span><?php _e('Url','ddabout'); ?></span>
    <input type="text" name="url" value="<?php echo esc_url( $ad->url ); ?>">
</li>