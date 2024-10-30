<?php

class MoneyFront{

    public $plugin_url;
    public $adSoldId;
    public $adContentType;
    public $buyerId;
    public $buyerEmail;


    /**
     * MoneyFront constructor.
     */
    public function __construct(){

        global $_moneyMode;

        $this->plugin_url = plugin_dir_url( __FILE__ ) . '../../assets';

        add_action( 'wp_head', array( $this, 'frontAssets' ), 4);

        if ( $_moneyMode === 'front' ){
            add_action('wp_head', array($this, 'createAdJSParams'), 5);
        }

        // Shortcodes
        add_shortcode( 'money_ads', array( 'MoneyHelper', 'shortcodeAdsStore' ) );

    }


    /**
     * Load front assets
     */
    public function frontAssets(){

        global $post, $_moneyMode;

        // CSS
        wp_enqueue_style('dashicons');
        wp_enqueue_style('money-front', $this->plugin_url . '/css/front.min.css', false, null);
        wp_enqueue_style('money-dialog', $this->plugin_url . '/css/dialog.min.css', false, null);
        wp_enqueue_style('money-store', $this->plugin_url . '/css/store.min.css', false, null);

        // RTL
        if( is_rtl() ){
            wp_enqueue_style( 'money-front-rtl', $this->plugin_url . '/css/front-rtl.css', false, null);
            wp_enqueue_style( 'money-store-rtl', $this->plugin_url . '/css/store-rtl.css', false, null);
            wp_enqueue_style( 'money-dialog-rtl', $this->plugin_url . '/css/dialog-rtl.css', false, null);
        }

        // JS
        wp_enqueue_script(
            'money-front',
            $this->plugin_url . '/js/front.min.js',
            array( 'jquery' ),
            false,
            false
        );

        wp_enqueue_script(
            'money-paypal',
            $this->plugin_url . '/js/store.min.js',
            array( 'money-front' ),
            false,
            true
        );

        wp_localize_script( 'money-front', 'mnObjFront', array(
            'nonce' => wp_create_nonce('money'),
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
        ));

    }


    /**
     * Create AD JS Params
     */
    public function createAdJSParams(){

        if( MoneyHelper::userIsAdmin() && MONEY_SETTINGS_DISABLE_ADS_ADMIN === 'on' ) return false;
        if( MoneyHelper::userIsLoggedIn() && MONEY_SETTINGS_DISABLE_ADS_LOGGED_IN === 'on' ) return false;

        global $post, $wpdb, $_moneyMode;

        // dont display ads on the store page
        if( is_object( $post ) ){
            if( property_exists( $post, 'ID' ) ) {
                if ( get_option('money_store_page', 1) == $post->ID ) return false;
            }
        }

        $adsModel = new MoneyAd();
        $adsSoldModel = new MoneyAdSold();
        $statsModel = new MoneyStatistic();

        $adsTableName = $wpdb->base_prefix . $adsModel->getTableName();
        $soldTableName = $wpdb->base_prefix . $adsSoldModel->getTableName();

        $allSold = $adsSoldModel->all( array(
            'columns' => "
                {$soldTableName}.id as sold_id,
                {$soldTableName}.status as sold_status,
                {$soldTableName}.hash as sold_hash,
                {$adsTableName}.id as ad_id,
                {$adsTableName}.display_when as ad_display_when,
                {$adsTableName}.strategy_type as ad_strategy_type,
                {$adsTableName}.strategy_value as ad_strategy_value,
                {$adsTableName}.style_shadow as ad_style_shadow,
                {$adsTableName}.display_on as ad_display_on,
                {$adsTableName}.display_on_pages as ad_display_on_pages,
                {$adsTableName}.display_on_posts as ad_display_on_posts
            ",
            'join' => "
                LEFT JOIN {$adsTableName}
                ON {$soldTableName}.ad_id = {$adsTableName}.id
            ",
            'where' => "{$soldTableName}.status='active' AND {$adsTableName}.display_when='page_load'",

        ));

        // no ads, match our sql query !
        if( ! count( $allSold ) ) return false;

        $adsList = $this->getAdsReadyToDisplay( $allSold );

        // no ads, after the filter
        if( ! count( $adsList ) ) return false;

        // get a random id
        $randomId = array_rand( $adsList, 1);
        $ad = $adsModel->get( $adsList[ $randomId ]->ad_id );

        wp_localize_script( 'money-front', 'mnObjAd', array(

            'adSoldId' => $adsList[ $randomId ]->sold_id,
            'adId' => $adsList[ $randomId ]->ad_id,

            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce('money'),
            'mode' => $_moneyMode,

            'showAfter' => MONEY_SETTINGS_LOAD_AFTER,
            'type' => $ad->content_type,
            'complexity' => $ad->complexity,
            'timer' => intval( $ad->advanced_timer ),
            'action' => $ad->advanced_action,
            'goToLink' => $ad->advanced_url,

            'shadow' => $ad->style_shadow,
            'responsive' => $ad->style_responsive,
            'position' => $ad->style_position,
            'width' => $ad->style_width,
            'height' => $ad->style_height,
            'top' => $ad->style_top,
            'bottom' => $ad->style_bottom,
            'left' => $ad->style_left,
            'right' => $ad->style_right

        ));

    }


    /**
     * Get ads ready to display
     * @param $allSold
     * @return array
     */
    private function getAdsReadyToDisplay( $allSold ){
        global $post;

        $adsList = array();
        foreach ( $allSold as $adSold ){

            if( $adSold->ad_display_on === 'all' ){
                $adsList[] = $adSold;
            }

            elseif( $adSold->ad_display_on === 'homepage' && is_front_page() ){
                $adsList[] = $adSold;
            }

            elseif( $adSold->ad_display_on === 'pages' && is_page() ){

                $pages = $adSold->ad_display_on_pages;

                if( $pages === '' || preg_match( '/all,?/', $pages ) ){
                    $adsList[] = $adSold;
                }

                elseif( strpos( $pages, ',' ) !== false && in_array( $post->ID , explode( ',', $pages ) ) ){
                    $adsList[] = $adSold;
                }

                elseif( $post->ID == $pages ){
                    $adsList[] = $adSold;
                }

            }

            elseif( $adSold->ad_display_on === 'posts' && is_single() ){

                $posts = $adSold->ad_display_on_posts;

                if( $posts === '' || preg_match( '/all,?/', $posts ) ){
                    $adsList[] = $adSold;
                }

                elseif( strpos( $posts, ',' ) !== false && in_array( $post->ID , explode( ',', $posts ) ) ){
                    $adsList[] = $adSold;
                }

                elseif( $post->ID == $posts ){
                    $adsList[] = $adSold;
                }

            }

        }

        return $adsList;
    }

}