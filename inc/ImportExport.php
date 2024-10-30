<?php

class MoneyImportExport{


    /**
     * Export
     * @return array|string
     */
    public static function export(){

        // export
        if( isset( $_POST['export_page'] ) ){

            $data = array();

            // config
            $data['config']['money_settings_load_ad_after'] = MONEY_SETTINGS_LOAD_AFTER;
            $data['config']['money_settings_notifications_email'] = MONEY_SETTINGS_EMAIL;
            $data['config']['money_paypal_mode'] = MONEY_PAYPAL_MODE;
            $data['config']['money_paypal_client_id'] = MONEY_PAYPAL_CLIENT_ID;
            $data['config']['money_paypal_secret'] = MONEY_PAYPAL_SECRET;
            $data['config']['money_paypal_currency'] = MONEY_PAYPAL_CURRENCY;
            $data['config']['money_settings_load_ad_after'] = MONEY_SETTINGS_LOAD_AFTER;
            $data['config']['money_paypal_currency'] = MONEY_PAYPAL_CURRENCY;
            $data['config']['money_paypal_web_proile_id'] = MONEY_PAYPAL_PROFILE_ID;
            $data['config']['money_site_name'] = MONEY_SITE_NAME;
            $data['config']['money_settings_disable_ads_admin'] = MONEY_SETTINGS_DISABLE_ADS_ADMIN;
            $data['config']['money_settings_disable_ads_logged_in'] = MONEY_SETTINGS_DISABLE_ADS_LOGGED_IN;

            // ad zones table
            $adsModel = new MoneyAd();
            foreach ( $adsModel->all() as $ad ){
                $tmp = (array)$ad;
                $id = $tmp['id'];
                unset( $tmp['id'] );

                $data['ads'][ $id ] = $tmp;
            }

            // demos table, is not needed

            // sold table
            $soldModel = new MoneyAdSold();
            foreach ( $soldModel->all() as $sold ){
                $tmp = (array)$sold;
                $id = $tmp['id'];
                unset( $tmp['id'] );

                $data['ads_sold'][ $id ] = $tmp;

                // buyer
                $buyer = MoneyHelper::userData( $tmp['buyer_id'] );
                $data['buyers'][ $tmp['buyer_id'] ] = array(
                    'username' => $buyer->user_login,
                    'email' => $buyer->user_email,
                    'first_name' => $buyer->first_name,
                    'last_name' => $buyer->last_name
                );
            }

            // statistics table
            $statsModel = new MoneyStatistic();
            foreach ( $statsModel->all() as $stat ){
                $tmp = (array)$stat;
                $id = $tmp['id'];
                unset( $tmp['id'] );

                $data['statistics'][ $id ] = $tmp;
            }

            // encode
            $data = json_encode( $data );
            $data = base64_encode( $data );
            return $data;
        }

        return '';
    }


    /**
     * Import
     */
    public static function import(){

        if ( isset( $_POST['import_page'] ) ){

            if( $_POST['import_code'] === '' ) return;

            // decode
            $data = base64_decode( $_POST['import_code'] );
            $data = json_decode( $data, true );

            if( ! isset( $data['config'] ) ) return;

            $adsModel = new MoneyAd();
            $demosModel = new MoneyAdDemo();
            $soldsModel = new MoneyAdSold();
            $statsModel = new MoneyStatistic();

            // Config
            foreach ( $data['config'] as $key => $value ){

                // sanitize
                $key = sanitize_text_field( $key );
                $value = sanitize_text_field( $key );

                update_option( $key, $value );
            }

            // check if current sold ads are the same as the imported once
            // if you found a match, delete it !!!!
            foreach ( $soldsModel->all() as $sold ){
                foreach ( $data['ads_sold'] as $importedId => $importedVal ){
                    if( $sold->paypal_payment_id === $importedVal['paypal_payment_id'] ){
                        unset( $data['ads_sold'][ $importedId ] );
                    }
                }

            }

            // import sold ads
            foreach ( $data['ads_sold'] as $sold ){

                $ad_id = intval( $data['ads'][ $sold['ad_id'] ] );
                $stat_id = intval( $data['statistics'][ $sold['statistic_id'] ] );

                $tmpAd = $adsModel->addNew( $ad_id ); // this one is for Demo
                $tmpDemo = $demosModel->addNew( array( 'ad_id' => $tmpAd->id ) );
                $tmpStat = $statsModel->addNew( $stat_id );

                $user_email = sanitize_email( $data['buyers'][ intval( $sold['buyer_id'] ) ]['email'] );
                if( ! $tmpBuyer = get_user_by( 'email', $user_email ) ){
                    $tmpBuyer = wp_create_user(
                        sanitize_user( $data['buyers'][ $sold['buyer_id'] ]['username'] ),
                        uniqid( date('Y-m-d H:m:s') . $user_email ),
                        $user_email
                    );
                }
                else{
                    $tmpBuyer = $tmpBuyer->ID;
                }

                $tmpAd = $adsModel->addNew( $ad_id ); // this one is for Sold

                $sold['ad_id'] = $tmpAd->id;
                $sold['ad_demo_id'] = $tmpDemo->id;
                $sold['buyer_id'] = $tmpBuyer;
                $sold['statistic_id'] = $tmpStat->id;

                $soldsModel->addNew( $sold ); // add the sold one !
            }

            $showDialog = true;
            $dialogTitle = __('Successfully imported', 'ddabout');
            require __DIR__ . '/admin/views/settings/dialog-error.php';
        }

    }

}