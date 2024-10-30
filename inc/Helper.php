<?php

class MoneyHelper{


    /**
     * Available ad status
     */
    public static function adStatusStr(){
        return array(
            'active' => __('Active','ddabout'),
            'pending' => __('Pending','ddabout'),
            'expired' => __('Expired','ddabout'),
            'refunded' => __('Refunded','ddabout'),
        );
    }


    /**
     * All Currencies
     */
    public static function adCurrencies(){
        return array(
            "USD" => __('U.S. Dollar','ddabout'),
            "EUR" => __('Euro','ddabout'),
            "AUD" => __('Australian Dollar','ddabout'),
            "CAD" => __('Canadian Dollar','ddabout'),
            "GBP" => __('Pounds Sterling','ddabout'),
            "JPY" => __('Japanese Yen','ddabout'),
        );
    }


    /**
     * Available content types
     */
    public static function adContentTypes(){
        return array(
            'video' => __('Video','ddabout'),
            'audio' => __('Audio','ddabout'),
            'image' => __('Image','ddabout'),
            'custom' => __('Custom','ddabout'),
        );
    }


    /**
     * Available strategies
     */
    public static function adStrategies(){
        return array(
            'views' => __('Impressions','ddabout'),
            'clicks' => __('Clicks','ddabout'),
            'days' => __('Days','ddabout'),
        );
    }


    /**
     * Available display when
     */
    public static function adDisplayWhen(){
        return array(
            'page_load' => __('Page has been loaded','ddabout'),
            'button_click' => __('Click on a button','ddabout'),
        );
    }


    /**
     * Available display when
     */
    public static function adDisplayOn(){
        return array(
            'all' => __('All','ddabout'),
            'homepage' => __('Home Page','ddabout'),
            'pages' => __('Pages','ddabout'),
            'posts' => __('Posts','ddabout'),
        );
    }


    /**
     * [ Shortcode ]
     * Display All Available Ads
     */
    public static function shortcodeAdsStore(){

        $adsModel = new MoneyAd();
        $adsDemoModel = new MoneyAdDemo();
        $adsSold = new MoneyAdSold();

        $storeUrl = admin_url('admin.php?page=money-store');
        $loginUrl = wp_login_url( $storeUrl );
        $registerUrl = wp_registration_url() .'&redirect_to='. $storeUrl;

        require __DIR__ . '/admin/views/shortcode-store.php';
        require __DIR__ . '/admin/views/user/dialog-register-login.php';
    }


    /**
     * paypal 1 decimal make problem, so add 0 to it
     * ex : 2.1 make a problem, solution 2.10
     * @param $float
     * @return string
     */
    public static function fixPaypalFloatIssue( $float ){

        if( strpos( $float, '.' ) !== false ){

            $taxParts = explode( '.', $float );
            $decimalPart = strlen( $taxParts[1] );

            if( $decimalPart === 1 ) {
                $float = $taxParts[0] . '.' . $taxParts[1] . '0';
            }
            elseif( $decimalPart > 2 ){
                $float = $taxParts[0] . '.' . substr( $taxParts[1], 0, 2 );
            }

        }

        return  strval( $float );
    }


    /**
     * Check if user can manage options
     */
    public static function userIsAdmin(){

        if ( current_user_can( 'manage_options' ) ) {
            return true;
        }

        return false;
    }


    /**
     * Check if normal user is connected
     */
    public static function userIsLoggedIn(){
        return is_user_logged_in();
    }


    /**
     * Get current user id
     */
    public static function userId(){
        return get_current_user_id();
    }


    /**
     * Get user data : email, name ....
     * @param $id
     * @return mixed
     */
    public static function userData( $id ){
        return get_userdata( $id );
    }


    /**
     * Pagination
     * @param $total
     * @param int $items_per_page
     * @param string $url
     * @param int $paged
     */
    public static function pagination( $total, $items_per_page = 5, $url = '', $paged = 0 ){

        $pages = ceil( $total / $items_per_page );
        $output = '';

        for( $i=0; $i < $pages; $i++ ){
            $output .= '<a href="'.$url.$i.'" '.( $i === $paged ? 'class="active"' : '' ).'>'.$i.'</a>';
        }

        if( $i > 1 ) echo $output;
    }


    /**
     * @param $ad
     * @return string
     */
    public static function getAdPreviewUrl( $ad ){

        $url = get_bloginfo('wpurl') . '/?money-mode=preview&money-id='.(int)$ad->id;
        $url = $url;

        // posts
        if( $ad->display_on === 'posts' ){
            $url = self::getUrlFromPostsSelection(
                $ad->display_on_posts,
                'post',
                (int)$ad->id,
                $url
            );
        }

        // pages
        elseif( $ad->display_on === 'pages' ){
            $url = self::getUrlFromPostsSelection(
                $ad->display_on_pages,
                'page',
                (int)$ad->id,
                $url
            );
        }

        return $url;
    }


    /**
     * @param $postsID
     * @param $postType
     * @param $adID
     * @param $url
     * @return string
     */
    private static function getUrlFromPostsSelection( $postsID, $postType, $adID, $url ){

        // random
        if ( $postsID === '' || $postsID === 'all' ) {
            $randomID = self::getRandomPostID( $postType );
            $url = get_bloginfo('wpurl') . '?p=' . $randomID . '&money-mode=preview&money-id=' . $adID;
            return $url;
        }

        // From selection
        else {

            $postsID = str_replace( 'all,', '', $postsID );
            $postsID = explode( ',', $postsID );

            foreach ($postsID as $ID) {

                if ( get_post( (int)$ID ) ){
                    return get_bloginfo('wpurl') . '?p=' . $ID . '&money-mode=preview&money-id=' . $adID;
                }

            }

            return $url;
        }

    }


    /**
     * Display json & exit
     * @param $array
     */
    public static function displayJsonAndExit( $array ){
        echo json_encode( $array );
        exit();
    }


    /**
     * HTML Select for display on option
     * @param string $type
     * @param string $selectedPosts
     */
    public static function displayOnSelect( $type='posts', $selectedPosts = '', $class = '' ){

        $type = preg_replace( '/s$/', '', $type ); // display on return posts or pages, so we need to remove the s from the end
        $selectedPosts = explode( ',', $selectedPosts );

        // The Loop
        $query = new WP_Query( array (
            'post_type'              => array( $type ),
            'post_status'            => array( 'publish' ),
            'posts_per_page'         => '40',
        ));

        $posts = $query->get_posts();
        array_unshift( $posts, (object)array( 'ID' => 'all', 'post_title' => __('All','ddabout') ) );

        if ( $query->have_posts() ) {
            echo '<select multiple name="'.( $type === 'post' ? 'posts' : 'pages' ).'" class="'.esc_attr($class).'">';

            foreach ( $posts as $post ){

                if( in_array( $post->ID, $selectedPosts ) ){
                    echo '<option value="'.(int)$post->ID.'" selected="selected">'.esc_html($post->post_title).'</option>';
                }
                else{
                    echo '<option value="'.(int)$post->ID.'">'.esc_html($post->post_title).'</option>';
                }

            }

            echo '</select>';
        }

        wp_reset_postdata();
    }


    /**
     * @param $postType
     * @return mixed
     */
    public static function getRandomPostID( $postType ){

        $query = new WP_Query( array (
            'post_type'              => array( $postType ),
            'post_status'            => array( 'publish' ),
            'posts_per_page'         => '1',
        ));
        $post = $query->get_posts();

        return $post[0]->ID;
    }


    /**
     * get ad from get request
     * @return bool|array
     */
    public static function getAdFromGET(){
        if( ! isset( $_GET['money-id'] ) ) return false;

        $adModel = new MoneyAd();
        return $adModel->get( intval( $_GET['money-id'] ) );
    }


    /**
     * Get posts/pages titles from the displayOn selection
     * @param $ad
     * @return string
     */
    public static function displayOnTitles( $ad ){

        if( $ad->display_on === 'all' || $ad->display_on === 'homepage' ){
            return self::adDisplayOn()[ $ad->display_on ];
        }

        if( $ad->display_on === 'pages' ){
            $ids = $ad->display_on_pages;
        }
        else{
            $ids = $ad->display_on_posts;
        }

        if( $ids === 'all' || $ids === '' || strpos( $ids, 'all,' ) !== false ){
            return self::adDisplayOn()[ $ad->display_on ];
        }
        elseif( strpos( $ids, ',' ) !== false ){

            $ids = explode( ',', $ids );

            $posts = array();
            foreach ( $ids as $id ){
                $temp = get_post( $id );
                $posts[] = $temp->post_title;
            }
            return self::adDisplayOn()[ $ad->display_on ] .' ( '. implode( ', ', $posts ) . ' )';

        }
        else{
            $temp = get_post( $ids );
            return self::adDisplayOn()[ $ad->display_on ] .' ( '. $temp->post_title . ' )';
        }

    }


    /**
     * @param $value
     * @return string
     */
    public static function cssValueDisplayFormat( $value ){
        return str_replace( array('px','%','em'), '', $value );
    }

}