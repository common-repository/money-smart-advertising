<?php

class MoneyAjax{


    /**
     * MoneyAjax constructor.
     */
    public function __construct(){

        // Admin
        add_action('wp_ajax_money_add_new_zone', array($this, 'addNewZone'));
        add_action('wp_ajax_money_clone_ad', array($this, 'cloneAd'));
        add_action('wp_ajax_money_delete_demo_ad', array($this, 'deleteDemoAd') );
        add_action('wp_ajax_money_delete_sold_ad', array($this, 'deleteSoldAd') );
        add_action('wp_ajax_money_approve_ad', array($this, 'approveAd') );
        add_action('wp_ajax_money_plugin_setup', array($this, 'pluginSetup'));

        // Editor
        add_action('wp_ajax_money_save_option', array($this, 'saveOption'));
        add_action('wp_ajax_money_get_iframe_url', array($this, 'getIframeUrl'));

        // Paypal
        add_action('wp_ajax_money_paypal_checkout', array($this, 'paypalCheckout'));
        add_action('wp_ajax_nopriv_money_paypal_checkout', array($this, 'paypalCheckout'));

        add_action('wp_ajax_money_save_buyer_content', array($this, 'saveBuyerContent'));
        add_action('wp_ajax_nopriv_money_save_buyer_content', array($this, 'saveBuyerContent'));

        // Front
        add_action('wp_ajax_money_front_ad_output', array($this, 'getAdOutput'));
        add_action('wp_ajax_nopriv_money_front_ad_output', array($this, 'getAdOutput'));

        add_action('wp_ajax_money_front_ad_update_stats', array($this, 'updateSoldAdStatistics'));
        add_action('wp_ajax_nopriv_money_front_ad_update_stats', array($this, 'updateSoldAdStatistics'));

        add_action('wp_ajax_money_front_ad_update_clicks_stat', array($this, 'updateSoldAdClicksStat'));
        add_action('wp_ajax_nopriv_money_front_ad_update_clicks_stat', array($this, 'updateSoldAdClicksStat'));

        add_action('wp_ajax_money_front_get_ad_js_params', array($this, 'getAdJsParams'));
        add_action('wp_ajax_nopriv_money_front_get_ad_js_params', array($this, 'getAdJsParams'));


    }


    /**
     * Add new AD Zone
     */
    public function addNewZone(){

        $this->hardCheck( array( 'permissions', 'nonce' ) );

        // New Ad
        $adModel = new MoneyAd();
        $ad = $adModel->addNew();

        // its available
        $adsDemoModel = new MoneyAdDemo();
        $adsDemoModel->addNew( array( 'ad_id' => $ad->id ) );

        MoneyHelper::displayJsonAndExit( array(
            'url' => admin_url( 'admin.php?page=money-editor&money-id=' . $ad->id )
        ));

    }


    /**
     * Clone an AD
     */
    public function cloneAd(){

        $this->hardCheck( array( 'permissions', 'nonce', 'ad' ) );

        $adModel = new MoneyAd();
        $adsDemoModel = new MoneyAdDemo();

        $adDemo = $adsDemoModel->get( (int)$_POST['ad_id'] );
        $ad = $adModel->get( $adDemo->ad_id );

        $clone = (array)$ad;
        unset($clone['id']);

        $cloneAd = $adModel->addNew( $clone );
        $adsDemoModel->addNew( array( 'ad_id' => $cloneAd->id ) );

        MoneyHelper::displayJsonAndExit( array(
            'success' => true
        ));
    }


    /**
     * Delete an Available AD
     */
    public function deleteDemoAd(){

        $this->hardCheck( array( 'permissions', 'nonce', 'demo_ad' ) );

        $adModel = new MoneyAd();
        $adsDemoModel = new MoneyAdDemo();
        $adsSold = new MoneyAdSold();

        // check if ad active or waiting approval
        $adDemo = $adsDemoModel->get( (int)$_POST['ad_id'] );

        if( $adsSold->isDemoAdActive( $adDemo->id ) ){
            MoneyHelper::displayJsonAndExit( array( 'errors' => array( 'Cant delete this ad, its active or pending' ) ) );
        }

        // delete
        $adsDemoModel->delete( array( 'id' => $adDemo->id ) );
        $adModel->delete( array( 'id' => $adDemo->ad_id ) );

        MoneyHelper::displayJsonAndExit( array( 'success' => true ));
    }


    /**
     * Delete Sold AD [ Refund Include ]
     */
    public function deleteSoldAd(){

        $this->hardCheck( array( 'permissions', 'nonce', 'sold_ad' ) );

        $adsModel = new MoneyAd();
        $adsSoldModel = new MoneyAdSold();
        $statsModel = new MoneyStatistic();

        $adSold = $adsSoldModel->get( (int)$_POST['ad_id'] );
        $ad = $adsModel->get( $adSold->ad_id );
        $stat = $statsModel->get( $adSold->statistic_id );
        $buyer = MoneyHelper::userData( $adSold->buyer_id );

        // calc percentage
        if( $ad->strategy_type === 'clicks' ){
            $percentage = ( intval( $stat->total_clicks ) * 100 ) / intval( $ad->strategy_value );
        }
        elseif( $ad->strategy_type === 'views' ){
            $percentage = ( intval( $stat->total_views ) * 100 ) / intval( $ad->strategy_value );
        }
        else{
            $percentage = ( intval( $stat->total_views ) * 100 ) / intval( $ad->strategy_value );
        }

        $buyer_spend = floatval( $ad->price );
        $our_money = ( $percentage / 100 ) * $buyer_spend;
        $refund_money = $buyer_spend - $our_money;

        // refund
        $paypalRestAPI = MoneyPaypal::getRestApiInstance();

        $request = $paypalRestAPI->refundPayment( $adSold->paypal_sale_id, array(
            'total' => MoneyHelper::fixPaypalFloatIssue( $refund_money ),
        ));

        if( ! isset( $request['links'], $request['state'] ) ){
            MoneyHelper::displayJsonAndExit( array( 'errors' => array( $refund_money, $adSold->currency, __( 'Refund fails', 'ddabout' ), $request ) ));
        }

        // change Ad status to refund
        $adsSoldModel->update( $adSold->id,  array(
            'status' => 'refunded',
            'refund_amount' => MoneyHelper::fixPaypalFloatIssue( $refund_money ),
            'date_end' => date("Y-m-d H:i:s"),
        ));

        // send emails
        $mailer = new MoneyMailer();
        $mailer->refund( array(
            'admin_email' => MONEY_SETTINGS_EMAIL,
            'hash' => $adSold->hash,
            'refund_total' => $refund_money .' '. $adSold->currency,
            'buyer_name' => $buyer->user_login,
            'buyer_email' => $buyer->user_email,
        ));

        MoneyHelper::displayJsonAndExit( array( 'success' => true ));
    }


    /**
     * approve AD
     */
    public function approveAd(){

        $this->hardCheck( array( 'permissions', 'nonce', 'sold_ad' ) );

        $adsModel = new MoneyAd();
        $adsSoldModel = new MoneyAdSold();

        $adSold = $adsSoldModel->get( (int)$_POST['ad_id'] );
        $ad = $adsModel->get( $adSold->ad_id );
        $buyer = MoneyHelper::userData( $adSold->buyer_id );
        $isActive = $adsSoldModel->isDemoAdActive( $adSold->ad_demo_id );

        if( $isActive === 'active' ){
            MoneyHelper::displayJsonAndExit( array( 'errors' => array( __('An ad is running in the same zone, please approve this when the zone is free!','ddabout') ) ));
        }

        // approve Ad
        $adsSoldModel->update( $adSold->id, array(
            'status' => 'active',
            'date_start' => date("Y-m-d H:i:s")
        ));

        $mailer = new MoneyMailer();
        $mailer->approved(array(
            'admin_email' => MONEY_SETTINGS_EMAIL,
            'ad_title' => $ad->id,
            'hash' => $adSold->hash,
            'buyer_name' => $buyer->user_login,
            'buyer_email' => $buyer->user_email,
            'buyer_url' => $ad->url,
            'buyer_content' => $ad->content,
            'ad_content_type' => $ad->content_type,
        ));

        MoneyHelper::displayJsonAndExit( array( 'success' => true ));
    }


    /**
     * Save AD option
     */
    public function saveOption(){

        $this->hardCheck( array( 'permissions', 'nonce', 'ad' ) );

        $id = (int)$_POST['ad_id'];

        unset( $_POST['ad_id'] );
        unset( $_POST['nonce'] );
        unset( $_POST['action'] );

        $data = $this->softCheck();

        // Save option
        $adModel = new MoneyAd();
        $adModel->update( $id, $data );

        MoneyHelper::displayJsonAndExit( array(
            'success' => true,
            'previewUrl' => MoneyHelper::getAdPreviewUrl( $adModel->get( $id ) ),
        ));
    }


    /**
     * Get AD js params
     * when someone click a the ad button
     */
    public function getAdJsParams(){

        $this->hardCheck( array( 'nonce', 'demo_ad' ) );

        $adsModel = new MoneyAd();
        $adsDemoModel = new MoneyAdDemo();
        $adsSoldModel = new MoneyAdSold();

        $adDemo = $adsDemoModel->get( (int)$_POST['ad_id'] );
        $adSold = $adsSoldModel->all(array(
            'where' => 'ad_demo_id='.$adDemo->id
        ));

        // no sold ad found, so we get the default demo ad
        if( ! count( $adSold ) ){

            $ad = $adsModel->get( $adDemo->ad_id );
            MoneyHelper::displayJsonAndExit( array( 'actionLink' => esc_url( $ad->advanced_url ) ) );
        }
        else{

            // search for an active ad
            $activeAd = false;
            foreach ( $adSold as $sold ){

                if( $sold->status === 'active' ){
                    $activeAd = $sold;
                    break;
                }
            }

            if( $activeAd ){
                $ad = $adsModel->get( $activeAd->ad_id );
                MoneyHelper::displayJsonAndExit( array( 'adParams' => array(

                    'adSoldId' => $activeAd->id,
                    'adId' => $ad->id,

                    'ajaxUrl' => admin_url( 'admin-ajax.php' ),
                    'nonce' => wp_create_nonce('money'),
                    'mode' => 'front',

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

                )));
            }
            else{
                $adSold = $adSold[0];
                $ad = $adsModel->get( $adSold->ad_id );
                MoneyHelper::displayJsonAndExit( array( 'actionLink' => $ad->advanced_url ) );
            }

        }

    }


    /**
     * Paypal :: AD Checkout
     * return paypal validate url or errors
     */
    public function paypalCheckout(){

        $this->hardCheck( array( 'login', 'nonce', 'demo_ad' ) );

        $adsModel = new MoneyAd();
        $adsDemoModel = new MoneyAdDemo();
        $adDemo = $adsDemoModel->get( (int)$_POST['ad_id'] );
        $ad = $adsModel->get( $adDemo->ad_id );

        // Get the approval url
        $restAPI = MoneyPaypal::getRestApiInstance();
        $profile_id = $restAPI->createWebProfile();
        $currency = MONEY_PAYPAL_CURRENCY;

        $total = MoneyHelper::fixPaypalFloatIssue( $ad->price );
        $data = array(
            'experience_profile_id' => $profile_id,
            'transactions' => array(
                array(
                    'amount' => array(
                        'total' =>  $total,
                        'currency' => $currency
                    ),
                    'description' => $ad->title,
                )
            ),
            'redirect_urls' => array(
                'return_url' => add_query_arg(
                    array(
                        'money-checkout' => 'success',
                        'money-demoId' => $adDemo->id,
                    ),
                    admin_url('admin.php?page=money-store&money-checkout=success')
                ),
                'cancel_url' => add_query_arg(
                    array(
                        'money-checkout' => 'cancel',
                        'money-demoId' => $adDemo->id,
                    ),
                    admin_url('admin.php?page=money-store&money-checkout=cancel')
                )
            )
        );
        $approval_url = $restAPI->getApprovalUrl( $data );

        MoneyHelper::displayJsonAndExit( array( 'approval_url' => $approval_url ) );
    }


    /**
     * Paypal :: Save Buyer content
     */
    public function saveBuyerContent(){

        $this->hardCheck( array( 'login', 'nonce', 'demo_ad' ) );

        $adsModel = new MoneyAd();
        $adsSoldModel = new MoneyAdSold();

        $adSold = $adsSoldModel->get( (int)$_POST['ad_id'] );
        $ad = $adsModel->get( $adSold->ad_id );

        // check buyer site url
        if( ! $url = MoneyValidator::adUrl( $_POST, 'buyer_url' ) ){
            MoneyHelper::displayJsonAndExit( array( 'errors' => array( __('Invalid site url', 'ddabout') ) ) );
        }

        // check buyer content
        if( in_array( $ad->content_type, array( 'image', 'video', 'audio' ) ) ){
            if( MoneyValidator::adUrl( $_POST, 'buyer_content' ) === false ){
                MoneyHelper::displayJsonAndExit( array( 'errors' => array( __('Invalid content url', 'ddabout') ) ) );
            }
            else{
                $buyerContent = $_POST['buyer_content'];
            }
        }
        else{
            if( $_POST['buyer_content'] == '' ){
                MoneyHelper::displayJsonAndExit( array( 'errors' => array('Custom code is required') ) );
            }
            else{
                $buyerContent = sanitize_text_field( $_POST['buyer_content'] );
            }
        }

        // Save
        $adsModel->update( $ad->id, array(
            'url' => $url,
            'content' => $buyerContent
        ));

        // Send emails for admin & buyer
        $adminEmail = MONEY_SETTINGS_EMAIL;
        $buyer = MoneyHelper::userData( $adSold->buyer_id );

        $mailer = new MoneyMailer();
        $mailer->newAd(array(
            'admin_email' => $adminEmail,
            'ad_title' => $ad->id,
            'ad_price' => $ad->price,
            'total' => $ad->price .' '. $adSold->currency,
            'buyer_name' => $buyer->first_name . ' ' . $buyer->last_name,
            'buyer_email' => $buyer->user_email,
            'buyer_url' => $url,
            'buyer_content' => $buyerContent,
            'ad_content_type' => $ad->content_type,
        ));

        MoneyHelper::displayJsonAndExit( array( 'success' => true ) );
    }


    /**
     * Front : Get AD output
     */
    public function getAdOutput(){

        $this->hardCheck( array( 'nonce', 'ad' ) );

        $adsModel = new MoneyAd();
        $ad = $adsModel->get( (int)$_POST['ad_id'] );

        ob_start();
        require __DIR__ . '/front/views/ad.php';
        $output = ob_get_contents();
        ob_end_clean();

        MoneyHelper::displayJsonAndExit( array( 'output' => $output ) );
    }


    /**
     * Front : Update AD Views / Days Stats
     */
    public function updateSoldAdStatistics(){

        $this->hardCheck( array( 'nonce', 'sold_ad' ) );

        $adsSoldModel = new MoneyAdSold();
        $statModel = new MoneyStatistic();
        $adsModel = new MoneyAd();

        $soldAd = $adsSoldModel->get( (int)$_POST['ad_id'] );
        $stat = $statModel->get( $soldAd->statistic_id );
        $ad = $adsModel->get( $soldAd->ad_id );
        $buyer = MoneyHelper::userData( $soldAd->buyer_id );

        // Update Stats
        $countries = unserialize( $stat->countries );
        $total_days = $stat->total_days;
        $total_views = $stat->total_views;
        $views_per_day = unserialize( $stat->views_per_day );
        $todayDate = date("Y-m-d");

        if( ! isset( $views_per_day[ $todayDate ] ) ){
            $total_days++;
            $views_per_day[ $todayDate ] = 1;
        }
        else{
            $views_per_day[ $todayDate ]++;
        }
        $total_views++;

        $statModel->update( $stat->id, array(
            'views_per_day' => serialize( $views_per_day ),
            'total_days' => $total_days,
            'total_views' => $total_views
        ));

        // check if is ended
        if( $ad->strategy_type === 'views' && $total_views == $ad->strategy_value ){
            $this->endSoldAd($adsSoldModel, $soldAd, $buyer);
        }
        elseif( $ad->strategy_type === 'days' && $total_days > $ad->strategy_value ){ // leave it > here !
            $this->endSoldAd($adsSoldModel, $soldAd, $buyer);
        }

        // update country last thing, because it takes time
        $geo = unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.$_SERVER['REMOTE_ADDR']));

        if( $geo['geoplugin_countryCode'] != ''  ) {
            if( isset( $countries[ $geo['geoplugin_countryCode'] ] ) ){
                $countries[ $geo['geoplugin_countryCode'] ]['views']++;
            }
            else{
                $countries[ $geo['geoplugin_countryCode'] ] = array( 'views' => 1, 'clicks' => 0 );
            }

            $statModel->update( $stat->id, array(
                'countries' => serialize( $countries ),
            ));
        }

        MoneyHelper::displayJsonAndExit( array( 'success' => true ) );
    }


    /**
     * Front : Update AD clicks Stats
     */
    public function updateSoldAdClicksStat(){

        $this->hardCheck( array( 'nonce', 'sold_ad' ) );

        $adsSoldModel = new MoneyAdSold();
        $statModel = new MoneyStatistic();
        $adsModel = new MoneyAd();

        $soldAd = $adsSoldModel->get( (int)$_POST['ad_id'] );
        $stat = $statModel->get( $soldAd->statistic_id );
        $ad = $adsModel->get( $soldAd->ad_id );
        $buyer = MoneyHelper::userData( $soldAd->buyer_id );

        // Update Stats
        $countries = unserialize( $stat->countries );
        $total_clicks = $stat->total_clicks + 1;
        $clicks_per_day = unserialize( $stat->clicks_per_day );
        $todayDate = date("Y-m-d");

        if( ! isset( $clicks_per_day[ $todayDate ] ) ){
            $clicks_per_day[ $todayDate ] = 1;
        }
        else{
            $clicks_per_day[ $todayDate ]++;
        }

        $statModel->update( $stat->id, array(
            'clicks_per_day' => serialize( $clicks_per_day ),
            'total_clicks' => $total_clicks
        ));

        // check if is ended
        if( $ad->strategy_type === 'clicks' && $total_clicks == $ad->strategy_value ){
            $this->endSoldAd($adsSoldModel, $soldAd, $buyer);
        }

        // update country last thing, because it takes time
        $geo = unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.$_SERVER['REMOTE_ADDR']));
        if( $geo['geoplugin_countryCode'] != '' ){
            $countries[ $geo['geoplugin_countryCode'] ]['clicks']++;

            $statModel->update( $stat->id, array(
                'countries' => serialize( $countries ),
            ));

        }

        MoneyHelper::displayJsonAndExit( array( 'success' => true ) );
    }


    /**
     * Admin plugin setup
     */
    public function pluginSetup(){

        $this->hardCheck( array( 'permissions', 'nonce' ) );

        // create store page
        if( $_POST['whatToDo'] === 'create_store_page' ){

            $id = wp_insert_post( array(
                'post_title' => __('Advertisement','ddabout'),
                'post_content' => '[money_ads]',
                'post_type' => 'page',
                'post_status' => 'publish'
            ));

            update_option( 'money_setup_step', 3 );
            update_option( 'money_store_page', $id );
        }

        // Select store page
        elseif ( $_POST['whatToDo'] === 'select_store_page' ){

            $id = intval( $_POST['store_page_id'] );
            $page = get_post( $id );

            // Update the post into the database
            if( strpos( $page->post_content, '[money_ads]' ) === false ){
                wp_update_post( array(
                    'ID'           => $id,
                    'post_content' => $page->post_content . '[money_ads]',
                ));
            }

            update_option( 'money_setup_step', 3 );
            update_option( 'money_store_page', $id );
        }

        // open step
        elseif ( $_POST['whatToDo'] === 'open_step' ){
            update_option( 'money_setup_step', intval( $_POST['step'] ) );
        }

        MoneyHelper::displayJsonAndExit( array( 'success' => true ));
    }


    /**
     * Hard check
     * @param array $whatToCheck
     */
    private function hardCheck( $whatToCheck = array( 'permissions', 'nonce' , 'ad', 'demo_ad', 'sold_ad' ) ){

        // json header
        header('Content-Type: application/json');

        MoneyValidator::clearErrors();

        if( in_array( 'permissions', $whatToCheck ) ){
            if( MoneyHelper::userIsAdmin() === false ){
                MoneyHelper::displayJsonAndExit(array(
                    'errors' => array(__( 'You do not have sufficient permissions to access this page.' ))
                ));
            }
        }

        if( in_array( 'login', $whatToCheck ) ){
            if( MoneyHelper::userIsLoggedIn() === false ){
                MoneyHelper::displayJsonAndExit(array(
                    'errors' => array( 'userLogin' => false )
                ));
            }
        }

        if( in_array( 'nonce', $whatToCheck ) ){
            if ( MoneyValidator::nonce( $_POST ) === false ){
                MoneyHelper::displayJsonAndExit( array( 'errors' => MoneyValidator::getErrors() ) );
            }
        }

        if( in_array( 'ad', $whatToCheck ) ){
            if( MoneyValidator::ad( new MoneyAd(), $_POST ) === false ){
                MoneyHelper::displayJsonAndExit(array('errors' => MoneyValidator::getErrors()));
            }
        }

        if( in_array( 'demo_ad', $whatToCheck ) ){
            if( MoneyValidator::ad( new MoneyAdDemo(), $_POST ) === false ){
                MoneyHelper::displayJsonAndExit(array('errors' => MoneyValidator::getErrors()));
            }
        }

        if( in_array( 'sold_ad', $whatToCheck ) ){
            if( MoneyValidator::ad( new MoneyAdSold(), $_POST ) === false ){
                MoneyHelper::displayJsonAndExit(array('errors' => MoneyValidator::getErrors()));
            }
        }
    }


    /**
     * check options
     * @return array
     */
    private function softCheck(){

        MoneyValidator::clearErrors();

        foreach ( $_POST as $key => $value ){

            $method = 'ad';

            foreach ( explode( '_', $key ) as $part ){
                $method .= ucfirst( $part );
            }

            if( method_exists( 'MoneyValidator', $method ) ){
                $_POST[ $key ] = MoneyValidator::$method( $_POST, $key );
            }
            else{
                MoneyHelper::displayJsonAndExit( array( 'errors' => array( 'Busted!' ) ) );
            }
        }

        if( count( MoneyValidator::getErrors() ) ){
            MoneyHelper::displayJsonAndExit( array( 'errors' => MoneyValidator::getErrors() ) );
        }

        return $_POST;
    }


    /**
     * @param $adsSoldModel
     * @param $soldAd
     * @param $buyer
     */
    private function endSoldAd($adsSoldModel, $soldAd, $buyer ){

        $adsSoldModel->update($soldAd->id, array(
            'status' => 'expired',
            'date_end' => date("Y-m-d H:i:s")
        ));

        // send emails
        $mailer = new MoneyMailer();
        $mailer->expired(array(
            'admin_email' => MONEY_SETTINGS_EMAIL,
            'hash' => $soldAd->hash,
            'buyer_name' => $buyer->user_login,
            'buyer_email' => $buyer->user_email,
        ));

    }

}