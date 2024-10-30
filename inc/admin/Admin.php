<?php

class MoneyAdmin{

    public $plugin_url;


    /**
     * MoneyAdminPage constructor.
     */
    public function __construct(){

        $this->plugin_url = plugin_dir_url( __FILE__ ) . '../../assets';

        // Add Pages for
        add_action( 'admin_menu', array( $this, 'addPages' ) );

        // Load Assets
        add_action( 'admin_enqueue_scripts', array( $this, 'adminAssets' ) );

        // Shortcodes
        add_shortcode( 'money_ads', array( 'MoneyHelper', 'shortcodeAdsStore' ) );
    }


    /**
     * Admin Assets
     * @return bool
     */
    public function adminAssets(){

        if( ! isset( $_GET['page'] ) ) return false;
        if( ! in_array( $_GET['page'], array(
            'money-editor',
            'money-ads',
            'money-settings',
            'money-export-import',
            'money-store',
            'money-user-ads',
            'money-ad-stats',
            'money-revenue',
        ))) return false;

        $page = $_GET['page'];

        // CSS
        wp_enqueue_style( 'money-store', $this->plugin_url . '/css/store.css', false, MONEY_VERSION);
        wp_enqueue_style( 'money-dialog', $this->plugin_url . '/css/dialog.css', false, MONEY_VERSION);
        wp_enqueue_style( 'money-admin', $this->plugin_url . '/css/admin.css', false, MONEY_VERSION);

        // RTL
        if( is_rtl() ){
            wp_enqueue_style( 'money-store-store', $this->plugin_url . '/css/store-rtl.css', false, MONEY_VERSION);
            wp_enqueue_style( 'money-dialog-rtl', $this->plugin_url . '/css/dialog-rtl.css', false, MONEY_VERSION);
            wp_enqueue_style( 'money-admin-rtl', $this->plugin_url . '/css/admin-rtl.css', false, MONEY_VERSION);
        }

        //JS
        if( $page === 'money-ad-stats' ) {
            wp_enqueue_script('money-chartjs', $this->plugin_url . '/js/chart.js', array('jquery'), MONEY_VERSION, false);
        }

        wp_enqueue_script(
            'money-store',
            $this->plugin_url . '/js/store.js',
            array( 'jquery' ),
            MONEY_VERSION,
            true
        );

        wp_localize_script( 'money-store', 'mnObjFront', array(
            'nonce' => wp_create_nonce('money'),
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
        ));

        // only administrator
        if( MoneyHelper::userIsAdmin() ){

            wp_enqueue_script( 'money-admin', $this->plugin_url . '/js/admin.js', array('jquery'), MONEY_VERSION, true );

            wp_localize_script( 'money-admin', 'mnObjAdmin', array(
                'nonce' => wp_create_nonce('money'),
                'delete_confirm_text' => __('Are you sure ?', 'ddabout'),
                'delete_waiting_approval_confirm_text' => __('A complete refund will be send to the buyer paypal account ?'),
                'delete_active_confirm_text' => __('A partial refund will be send to the buyer paypal account ?'),
                'dialog_errors_title' => __('Error(s)','ddabout'),
                'dialog_zone_not_free' => __('Zone is not free', 'ddabout'),
                'media_uploader_select' => __('Select','ddabout')
            ));

            if( $page === 'money-editor' ) {

                wp_enqueue_media();
                wp_enqueue_style('money-editor', $this->plugin_url . '/css/editor.css', false, MONEY_VERSION);

                if ( is_rtl() ) {
                    wp_enqueue_style(
                        'money-editor-rtl',
                        $this->plugin_url . '/css/editor-rtl.css',
                        false,
                        MONEY_VERSION);
                }

                wp_enqueue_script(
                    'money-editor',
                    $this->plugin_url . '/js/editor.js',
                    array('jquery', 'media-editor'),
                    MONEY_VERSION,
                    true
                );
            }

        }

    }


    /**
     * Add page
     */
    public function addPages() {

        global $submenu;

        if( MoneyHelper::userIsAdmin() ){

            add_menu_page(
                __('Money Ads','ddabout'),
                __('Money Ads','ddabout'),
                'manage_options',
                'money-ads',
                array( $this, 'adminAdsList' )
            );

            add_submenu_page (
                'money-ads',
                __('Settings','ddabout'),
                __('Settings','ddabout'),
                'manage_options',
                'money-settings',
                array( $this, 'adminSettings' )
            );

            add_submenu_page (
                'money-ads',
                __('Revenue Report','ddabout'),
                __('Revenue Report','ddabout'),
                'manage_options',
                'money-revenue',
                array( $this, 'adminRevenueReport' )
            );

            add_submenu_page (
                'money-ads',
                __('Export / Import','ddabout'),
                __('Export / Import','ddabout'),
                'manage_options',
                'money-export-import',
                array( $this, 'adminExportImport' )
            );

            add_submenu_page (
                'money-ads',
                __('Store','ddabout'),
                __('Store','ddabout'),
                'read',
                'money-store',
                array( $this, 'userAdsStore' )
            );

            add_submenu_page (
                null,
                '',
                '',
                'manage_options',
                'money-editor',
                array( $this, 'adminEditor' )
            );

            $submenu['money-ads'][0][0] = __('Ads','ddabout');
        }
        elseif( MoneyHelper::userIsLoggedIn() ){

            add_menu_page(
                __('Money Ads','ddabout'),
                __('Money Ads','ddabout'),
                'read',
                'money-store',
                array( $this, 'userAdsStore' )
            );

            add_submenu_page (
                'money-store',
                __('Your ads','ddabout'),
                __('Your ads','ddabout'),
                'read',
                'money-user-ads',
                array( $this, 'userAdsList' )
            );

            $submenu['money-store'][0][0] = __('Store','ddabout');
        }

        // for all
        add_submenu_page (
            null,
            '',
            '',
            'read',
            'money-ad-stats',
            array( $this, 'userAdStats' )
        );
    }


    /**
     * Administrator : Ads List Page
     */
    public function adminAdsList() {

        $adModel = new MoneyAd();
        $adDemoModel = new MoneyAdDemo();
        $adSoldModel = new MoneyAdSold();
        $statsModel = new MoneyStatistic();

        $tipNum = get_option('money_setup_step', 1 );
        $paged = isset( $_GET['mn-paged'] ) ? intval( $_GET['mn-paged'] ) : 0;
        $panelPage = isset( $_GET['mn-panel-page'] ) ? $_GET['mn-panel-page'] : 'demo';

        // total
        $demoTotal = count( $adDemoModel->all() );
        $activeTotal = count($adSoldModel->all(array( 'where' => 'status="active"' )));
        $pendingTotal = count($adSoldModel->all(array( 'where' => 'status="pending" ' )));
        $expiredTotal = count($adSoldModel->all(array( 'where' => 'status="expired" ' )));
        $refundedTotal = count($adSoldModel->all(array( 'where' => 'status="refunded" ')));

        // Demo Ads
        if( $panelPage === 'demo' ) {
            $ads = $adDemoModel->all(array(
                'pagination' => true,
                'page' => $paged,
                'order_by' => 'id DESC'
            ));
        }

        // Active Ads
        elseif ( $panelPage === 'active' ) {
            $ads = $adSoldModel->all(array(
                'pagination' => true,
                'page' => $paged,
                'order_by' => 'id DESC',
                'where' => 'status="active"'
            ));
        }

        // Pending, older first
        elseif ( $panelPage === 'pending' ) {
            $ads = $adSoldModel->all(array(
                'pagination' => true,
                'page' => $paged,
                'order_by' => 'id ASC', // order by older
                'where' => 'status="pending"'
            ));
        }

        // Expired
        elseif ( $panelPage === 'expired' ) {
            $ads = $adSoldModel->all(array(
                'pagination' => true,
                'page' => $paged,
                'order_by' => 'id DESC',
                'where' => 'status="expired"'
            ));
        }

        // Refunded Ads
        elseif ( $panelPage === 'refunded' ) {
            $ads = $adSoldModel->all(array(
                'pagination' => true,
                'page' => $paged,
                'order_by' => 'id DESC',
                'where' => 'status="refunded"'
            ));
        }

        require_once __DIR__ . "/views/panel-ads/panel.php";
        require_once __DIR__ . "/views/settings/dialog-error.php";
    }


    /**
     * Administrator : Settings Page
     */
    public function adminSettings() {
        $panelPage = isset( $_GET['mn-panel-page'] ) ? sanitize_text_field( $_GET['mn-panel-page'] ) : 'general';
        require_once __DIR__ . "/views/settings/panel.php";
    }


    /**
     * Administrator : Revenue report
     */
    public function adminRevenueReport() {

        global $wpdb;

        $soldModel = new MoneyAdSold();
        $adsModel = new MoneyAd();
        $statsModel = new MoneyStatistic();

        $paged = isset( $_GET['mn-paged'] ) ? intval( $_GET['mn-paged'] ) : 0;
        $panelPage = isset( $_GET['mn-panel-page'] ) ? $_GET['mn-panel-page'] : 'today';

        if( $panelPage === 'today' ) {
            $where = ' date_purchase LIKE "%' . date('Y-m-d') . '%"';
        }

        elseif( $panelPage === 'month' ) {
            $where = ' date_purchase LIKE "%' . date('Y-m-') . '%"';
        }

        elseif( $panelPage === 'year' ) {
            $where = ' date_purchase LIKE "%' . date('Y-') . '%"';
        }

        // All time
        else{
            $where = '';
        }

        $allSold = $soldModel->all(array( 'where' => $where ));
        $adsCount = count( $allSold );

        // Calc revenue
        $revenueArr = array();
        foreach ( $allSold as $sold ){
            $ad = $adsModel->get( $sold->ad_id );
            $tempPrice = floatval( $ad->price );
            $tempRefund = floatval( $sold->refund_amount );

            if( ! isset( $revenueArr[ $sold->currency ] ) ){

                $revenueArr[ $sold->currency ][ 'total_price' ] = $tempPrice;
                $revenueArr[ $sold->currency ][ 'total_refund' ] = $tempRefund;

            }
            else{

                $revenueArr[ $sold->currency ][ 'total_price' ] += $tempPrice;
                $revenueArr[ $sold->currency ][ 'total_refund' ] += $tempRefund;

            }

        }

        // ads by pagination
        $allSold = $soldModel->all(array(
            'where' => $where,
            'order_by' => 'id DESC',
            'pagination' => true,
            'page' => $paged,
        ));

        require_once __DIR__ . "/views/revenue/panel.php";
    }


    /**
     * Administrator : Import / Export Page
     */
    public function adminExportImport() {
        $panelPage = isset( $_GET['mn-panel-page'] ) ? $_GET['mn-panel-page'] : 'export';

        MoneyImportExport::import();
        $data = MoneyImportExport::export();

        require_once __DIR__ . "/views/import-export/panel.php";
    }


    /**
     * Administrator : editor view
     */
    public function adminEditor(){
        $ad = MoneyHelper::getAdFromGET();
        if( ! $ad ) return;

        $adsDemoModel = new MoneyAdDemo();
        $adDemo = $adsDemoModel->all(array(
            'where' => 'ad_id=' . $ad->id
        ));
        $adDemo = $adDemo[0];
        $editor_mode = isset( $_GET['money-ad-type'] ) ? sanitize_text_field( $_GET['money-ad-type'] ) : 'demo_ad';

        require_once __DIR__ . '/views/editor/editor.php';
    }


    /**
     * User : Ads Store
     */
    public function userAdsStore(){

        // checkout
        if( isset( $_GET['money-checkout'] ) ){

            // success
            if( $_GET['money-checkout'] === 'success' ){
                $this->execPayment();
            }

            // cancel
            elseif( $_GET['money-checkout'] === 'cancel' ){
                require __DIR__ . '/views/user/dialog-checkout-cancel.php';
            }

        }

        require __DIR__ . '/views/user/store.php';
    }


    /**
     * User : Ads List page
     */
    public function userAdsList(){

        $adModel = new MoneyAd();
        $adDemoModel = new MoneyAdDemo();
        $adSoldModel = new MoneyAdSold();
        $statsModel = new MoneyStatistic();

        $paged = isset( $_GET['mn-paged'] ) ? intval( $_GET['mn-paged'] ) : 0;
        $panelPage = isset( $_GET['mn-panel-page'] ) ? $_GET['mn-panel-page'] : 'active';

        // total
        $activeTotal = count($adSoldModel->all(array(
            'where' => 'status="active" AND buyer_id='.MoneyHelper::userId()
        )));
        $pendingTotal = count($adSoldModel->all(array(
            'where' => 'status="pending" AND buyer_id='.MoneyHelper::userId()
        )));
        $expiredTotal = count($adSoldModel->all(array(
            'where' => 'status="expired" AND buyer_id='.MoneyHelper::userId()
        )));
        $refundedTotal = count($adSoldModel->all(array(
            'where' => 'status="refunded" AND buyer_id='.MoneyHelper::userId()
        )));

        // Active Ads
        if ( $panelPage === 'active' ) {
            $ads = $adSoldModel->all(array(
                'pagination' => true,
                'page' => $paged,
                'order_by' => 'id DESC',
                'where' => 'status="active" AND buyer_id='.MoneyHelper::userId()
            ));
        }

        // pending, older first
        elseif ( $panelPage === 'pending' ) {
            $ads = $adSoldModel->all(array(
                'pagination' => true,
                'page' => $paged,
                'order_by' => 'id ASC', // order by older
                'where' => 'status="pending" AND buyer_id='.MoneyHelper::userId()
            ));
        }

        // expired Ads
        elseif ( $panelPage === 'expired' ) {
            $ads = $adSoldModel->all(array(
                'pagination' => true,
                'page' => $paged,
                'order_by' => 'id ASC', // order by older
                'where' => 'status="expired" AND buyer_id='.MoneyHelper::userId()
            ));
        }

        // Refunded Ads
        elseif ( $panelPage === 'refunded' ) {
            $ads = $adSoldModel->all(array(
                'pagination' => true,
                'page' => $paged,
                'order_by' => 'id DESC', // order by older
                'where' => 'status="refunded" AND buyer_id='.MoneyHelper::userId()
            ));
        }

        require_once __DIR__ . "/views/user/list/panel.php";
    }


    /**
     * Ad Stats Page
     */
    public function userAdStats(){

        // check hash
        if( ! isset( $_GET['money-hash'] ) ) return false;

        // security check
        if( ! preg_match( '/^[0-9a-z]{32}$/i', $_GET['money-hash'] ) ) return false;

        $soldModel = new MoneyAdSold();
        $adsModel = new MoneyAd();
        $statsModel = new MoneyStatistic();

        // check if ad exists
        $sold = $soldModel->all(array(
            'where' => ' hash="'.$_GET['money-hash'].'" '
        ));

        if( ! count( $sold ) ) return false;

        // everything is ok!
        $sold = $sold[0];
        $ad = $adsModel->get( $sold->ad_id );
        $stats = $statsModel->get( $sold->statistic_id );
        $buyer = MoneyHelper::userData( $sold->buyer_id );
        $statUrl = admin_url('admin.php?page=money-ad-stats&money-hash='.$sold->hash);
        $countries = unserialize( $stats->countries );

        $chartLabels = '[';
        $chartData = '[';
        $allData = array();
        $weekStr = __('Week','ddabout');
        $dayStr = __('Day','ddabout');

        $statsType = isset( $_GET['money-stats-type'] ) ? $_GET['money-stats-type'] : 'views';
        $statsPer = isset( $_GET['money-stats-per'] ) ? $_GET['money-stats-per'] : 'week';

        if( $statsType === 'views' ){
            $statsDb = unserialize( $stats->views_per_day );
        }
        else{
            $statsDb = unserialize( $stats->clicks_per_day );
        }

        // no available stats
        if( ! count( $statsDb ) ) {
            $chartLabels = '[]';
            $chartData = '[]';

            require __DIR__ . '/views/user/ad-stats.php';
            return false;
        }

        // Get Available Years, Months, Weeks
        $allYears = array();
        $allMonths = array();
        $allWeeks = array();
        $allDays = array();

        foreach ( $statsDb as $keyDate => $keyVal ){

            $parts = explode( '-', $keyDate );
            $allYears[ $parts[0] ] = $parts[0];
            $allMonths[ $parts[0] ][ $parts[1] ] = $parts[1];
            $allDays[ $parts[0] ][ $parts[1] ] [ $parts[2] ] = $parts[2];

            if( $parts[2] > 0 && $parts[2] < 8 ){
                $week = '1';
            }
            elseif( $parts[2] > 7 && $parts[2] < 15 ){
                $week = '2';
            }
            elseif( $parts[2] > 14 && $parts[2] < 22 ){
                $week = '3';
            }
            elseif( $parts[2] > 21 && $parts[2] < 28 ){
                $week = '4';
            }
            else{
                $week = '5';
            }

            $allWeeks[ $parts[0] ][ $parts[1] ] [ $week ] = $week;
        }

        // Stats per
        $selectedYear = isset( $_GET['money-year'] ) ? $_GET['money-year'] : end( $allYears );
        $selectedMonth = isset( $_GET['money-month'] ) ? $_GET['money-month'] : end( $allMonths[ $selectedYear ] );
        $selectedWeek = isset( $_GET['money-week'] ) ? $_GET['money-week'] : end( $allWeeks[ $selectedYear ][ $selectedMonth ] );

        // months of the year
        if( $statsPer === 'year' ){

            foreach ( $statsDb as $key => $value ){

                $date = new DateTime( $key );

                if( $date->format('Y') == $selectedYear  ){
                    $month = $date->format('M');

                    if( isset( $allData[$month] ) ){
                        $allData[$month] = $allData[$month] + intval( $value );
                    }
                    else{
                        $allData[$month] = intval( $value );
                    }
                }
            }
        }

        // weeks of the month
        elseif( $statsPer === 'month' ){

            foreach ( $statsDb as $key => $value ){

                $date = new DateTime( $key );
                $keyMonth = $date->format('m');
                $keyDay = intval( $date->format('d') );

                if( $date->format('Y') == $selectedYear && $keyMonth == $selectedMonth ){

                    if( $keyDay > 0 && $keyDay < 8 ){
                        $week = $weekStr.' 1';
                    }
                    elseif( $keyDay > 7 && $keyDay < 15 ){
                        $week = $weekStr.' 2';
                    }
                    elseif( $keyDay > 14 && $keyDay < 22 ){
                        $week = $weekStr.' 3';
                    }
                    elseif( $keyDay > 21 && $keyDay < 29 ){
                        $week = $weekStr.' 4';
                    }
                    elseif( $keyDay > 28 && $keyDay < 32 ){
                        $week = $weekStr.' 5';
                    }

                    if( isset( $allData[$week] ) ){
                        $allData[$week] = $allData[$week] + intval( $value );
                    }
                    else{
                        $allData[$week] = intval( $value );
                    }

                }

            }

        }

        // days of the week
        elseif( $statsPer === 'week' ){

            if( $selectedWeek === '1' ){
                $min = 0;
                $max = 8;
            }
            elseif( $selectedWeek === '2' ){
                $min = 7;
                $max = 15;
            }
            elseif( $selectedWeek === '3' ){
                $min = 14;
                $max = 22;
            }
            elseif( $selectedWeek === '4' ){
                $min = 21;
                $max = 29;
            }
            else{
                $min = 28;
                $max = 32;
            }

            foreach ( $statsDb as $key => $value ){

                $date = new DateTime( $key );
                $keyDay = intval( $date->format('d') );

                if( $date->format('Y') == $selectedYear && $date->format('m') == $selectedMonth && ( $keyDay > $min && $keyDay < $max ) ){
                    $allData[ $date->format('M') .' '. $keyDay ] = intval( $value );
                }

            }

        }

        // prepare chart data
        $maxSizeBiggerThan5 = false;
        foreach ( $allData as $key => $value ){
            $chartLabels .= '"' . $key . '",';
            $chartData .= '"' . $value . '",';

            if( $value > 5 ) $maxSizeBiggerThan5 = true;
        }

        if( strpos( $chartData, ',' ) !== false ) {
            $chartLabels = substr($chartLabels, 0, -1) . ']';
            $chartData = substr($chartData, 0, -1) . ']';
        }

        require __DIR__ . '/views/user/ad-stats.php';

    }


    /**
     * Exec Payment, Save new ad / buyer
     */
    private function execPayment(){

        $paypal = new MoneyPaypal();
        $adsModel = new MoneyAd();
        $adsDemoModel = new MoneyAdDemo();
        $adsSoldModel = new MoneyAdSold();
        $statModel = new MoneyStatistic();

        // check if the payment id in db
        $count = count( $adsSoldModel->all( array(
            'where' => 'paypal_payment_id="'.$_GET['paymentId'].'"'
        )));
        if( $count ) return false;

        // everything is ok !
        if( $data = $paypal->executePayment( $adsModel, $adsDemoModel, $adsSoldModel ) ){

            $adDemo = $adsDemoModel->get( $data['ad_demo_id'] );

            // Clone ad
            $clone = (array) $adsModel->get( $adDemo->ad_id );
            unset( $clone['id'] );
            $ad = $adsModel->addNew( $clone );

            // create statistic
            $stat = $statModel->addNew();

            // create sold ad
            $adSold = $adsSoldModel->addNew(array(
                'ad_id' => $ad->id,
                'ad_demo_id' => $adDemo->id,
                'buyer_id' => MoneyHelper::userId(),
                'statistic_id' => $stat->id,
                'date_purchase' => date("Y-m-d H:i:s"),
                'date_start' => '0000-00-00 00:00:00',
                'date_end' => '0000-00-00 00:00:00',
                'paypal_sale_id' => $data['paypal_sale_id'],
                'paypal_payment_id' => $data['paypal_payment_id'],
                'paypal_payer_id' => $data['paypal_payer_id'],
                'status' => 'pending',
                'hash' => md5( MoneyHelper::userId() . uniqid() . 'money' ),
                'refund_amount' => '0',
                'currency' => MONEY_PAYPAL_CURRENCY
            ));

            $contentTypeText = array(
                'image' => __('Add your image url','ddabout'),
                'video' => __('Add your video url','ddabout'),
                'audio' => __('Add your audio url','ddabout'),
                'custom' => __('Add your custom code','ddabout')
            );
            $contentTypePlaceHolder = $contentTypeText[ $ad->content_type ];

            $buyer = MoneyHelper::userData( MoneyHelper::userId() );

            require __DIR__ . '/views/user/dialog-content.php';
            require __DIR__ . '/views/user/dialog-checkout-success.php';
        }
        else{
            require __DIR__ . '/views/user/dialog-checkout-error.php';
        }

    }

}
