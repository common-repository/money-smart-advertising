<?php


class MoneyConfig{


    /**
     * MoneyConfig constructor.
     */
    public function __construct(){
        add_action( 'init', array( $this, 'saveGeneral' ) );
        add_action( 'init', array( $this, 'savePaypal' ) );
        add_action( 'init', array( $this, 'setDefined' ) );
    }


    /**
     * Set defined config
     */
    public static function setDefined(){

        define( 'MONEY_VERSION', '1.0.0' );

        // General
        define( 'MONEY_SITE_NAME', get_option( 'money_site_name', get_bloginfo('name') ) );
        define( 'MONEY_SETTINGS_LOAD_AFTER', get_option('money_settings_load_ad_after', 2) );
        define( 'MONEY_SETTINGS_EMAIL', get_option('money_settings_notifications_email', get_option('admin_email') ) );
        define( 'MONEY_SETTINGS_DISABLE_ADS_ADMIN', get_option('money_settings_disable_ads_admin', '' ) );
        define( 'MONEY_SETTINGS_DISABLE_ADS_LOGGED_IN', get_option('money_settings_disable_ads_logged_in', '' ) );

        // Paypal
        define( 'MONEY_PAYPAL_MODE', get_option('money_paypal_mode', 'sandbox') );
        define( 'MONEY_PAYPAL_CLIENT_ID', get_option('money_paypal_client_id', 'xxx-xxxx-xxxx') );
        define( 'MONEY_PAYPAL_SECRET', get_option('money_paypal_secret', 'xxx-xxxx-xxx') );
        define( 'MONEY_PAYPAL_CURRENCY', get_option('money_paypal_currency', 'USD') );
        define( 'MONEY_PAYPAL_PROFILE_ID', get_option('money_paypal_web_proile_id') );

    }


    /**
     * Save geenral Settings
     */
    public function saveGeneral(){

        if( isset( $_POST['nonce'], $_POST['notificationsEmail'] ) ){

            $nonce = MoneyValidator::nonce( $_POST, 'nonce' );
            $loadAdAfter = isset( $_POST['loadAdAfter'] ) && $_POST['loadAdAfter'] != '' ? intval( $_POST['loadAdAfter'] ) : 2;
            $email = MoneyValidator::email( $_POST, 'notificationsEmail' );

            $disableAdsAdmin = isset( $_POST['disableAdsAdmin'] ) ? 'on' : '';
            $disableAdsLoggedIn = isset( $_POST['disableAdsLoggedIn'] ) ? 'on' : '';
            
            if( count( MoneyValidator::getErrors() ) ){
                $this->errorsMessage();
            }

            else{

                update_option( 'money_settings_load_ad_after', $loadAdAfter );
                update_option( 'money_settings_notifications_email', $email );
                update_option( 'money_settings_disable_ads_admin', $disableAdsAdmin );
                update_option( 'money_settings_disable_ads_logged_in', $disableAdsLoggedIn );

                $this->successMessage();
            }

        }

    }


    /**
     * Save Paypal Settings
     */
    public function savePaypal(){

        if( isset( $_POST['nonce'], $_POST['paypalMode'] ) ){

            $nonce = MoneyValidator::nonce( $_POST, 'nonce' );
            $paypalMode = MoneyValidator::paypalMode( $_POST, 'paypalMode' );
            $clientID = MoneyValidator::required( $_POST, 'clientID', __('Invalid Client ID', 'ddabout') );
            $secret = MoneyValidator::required( $_POST, 'secret', __('Invalid Secret', 'ddabout') );
            $currency = MoneyValidator::adCurrency( $_POST, 'currency' );

            if( $paypalMode === 'sandbox' ){
                $currency = 'USD';
            }

            if( count( MoneyValidator::getErrors() ) ){
                $this->errorsMessage();
            }

            else{

                update_option( 'money_paypal_currency', $currency );
                update_option( 'money_paypal_mode', $paypalMode );
                update_option( 'money_paypal_client_id', $clientID );
                update_option( 'money_paypal_secret', $secret );

                $this->successMessage();
            }

        }

    }


    /**
     * Display errors dialog
     */
    private function errorsMessage(){

        $showDialog = true;
        $dialogErrors = '';

        foreach (MoneyValidator::getErrors() as $error) {
            $dialogErrors .= '<li>' . $error . '</li>';
        }

        require_once __DIR__ . "/admin/views/settings/dialog-error.php";
    }


    /**
     * Print success message
     */
    private function successMessage(){
        require_once __DIR__ . "/admin/views/settings/dialog-success.php";
    }

}