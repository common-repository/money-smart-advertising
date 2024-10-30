<?php


class MoneyPaypal{
    

    /**
     * Get Rest Api Instance
     */
    public static function getRestApiInstance(){

        $paypalMode = MONEY_PAYPAL_MODE;
        $clientID = MONEY_PAYPAL_CLIENT_ID;
        $secret = MONEY_PAYPAL_SECRET;

        $sandbox = false;
        if( $paypalMode === 'sandbox' ){
            $sandbox = true;
        }

        // check paypal client id / secret
        try{
            return new MoneyPaypalRestAPI( $clientID, $secret, $sandbox );
        }
        catch ( Exception $e ){
            MoneyHelper::displayJsonAndExit( array( 'errors' => array( __( 'Invalid paypal client id / secret', 'ddabout' ) ) ) );
        }

    }


    /**
     * Execute Payment
     */
    public function executePayment( MoneyAd $adsModel, MoneyAdDemo $adsDemoModel, MoneyAdSold $adsSoldModel  ){

        if( ! isset( $_GET['money-demoId'], $_GET['PayerID'], $_GET['paymentId'], $_GET['token'] ) ) {
            throw new Exception('Cheating !');
        }

        $adDemo = $adsDemoModel->get( $_GET['money-demoId'] );

        // check ad
        if( ! $adDemo ) {
            throw new Exception('This is not a valid id');
        }

        $paypalRestAPI = MoneyPaypal::getRestApiInstance();
        $request = $paypalRestAPI->executePayment( $_GET['paymentId'], $_GET['PayerID'] );

        // Something is wrong
        if( ! isset( $request['payer'] ) ) return false;

        // Payment executed !
        return array(
            'ad_demo_id' => intval( $_GET['money-demoId'] ),
            'email' => $request['payer']['payer_info']['email'],
            'first_name' => $request['payer']['payer_info']['first_name'],
            'last_name' => $request['payer']['payer_info']['last_name'],
            'paypal_sale_id' => $request['transactions'][0]['related_resources'][0]['sale']['id'],
            'paypal_payer_id' => $request['payer']['payer_info']['payer_id'],
            'paypal_payment_id' => $request['id'],
        );
    }

}