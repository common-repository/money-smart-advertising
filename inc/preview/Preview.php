<?php

class MoneyPreview{

    public $plugin_url;
    public $ad;


    /**
     * MoneyPreview constructor.
     */
    public function __construct(){

        if( $this->checkAd() === false ) return;

        $this->plugin_url = plugin_dir_url( __FILE__ ) . '../../assets';

        add_action( 'wp_head', array( $this, 'loadAssets' ), 4 );
        add_filter( 'show_admin_bar', array( $this, 'removeAdminBar' ), 99 );
    }


    /**
     * Load preview assets
     */
    public function loadAssets(){

        global $_moneyMode;

        wp_enqueue_style(
            'money-front',
            $this->plugin_url . '/css/front.min.css',
            false,
            null
        );

        // RTL
        if( is_rtl() ){
            wp_enqueue_style( 'money-front-rtl', $this->plugin_url . '/css/front-rtl.css', false, null);
        }

        // JS
        wp_enqueue_script(
            'money-front',
            $this->plugin_url . '/js/front.min.js',
            array( 'jquery' ),
            false,
            false
        );

        $ad = $this->ad;

        wp_localize_script( 'money-front', 'mnObjAd', array(

            'adSoldId' => null,
            'adId' => $ad->id,

            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce('money'),
            'mode' => $_moneyMode,

            'showAfter' => 0,
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
     * Check Ad
     */
    private function checkAd(){
        if( ! isset( $_GET['money-id'] ) ) return false;

        $adModel = new MoneyAd();
        $this->ad = $adModel->get( $_GET['money-id'] );

        return $this->ad;
    }


    /**
     * Remove Top Bar
     */
    public function removeAdminBar(){
        return false;
    }
}