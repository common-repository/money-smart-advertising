<?php

class MoneyPaypalRestAPI{

    private $client_id;
    private $client_secret;
    private $accessToken;
    private $apiUrl = 'https://api.paypal.com/';

    
    /**
     * Create a new instance
     * @param string $client_id
     * @param string $client_secret
     * @param boolean $sandbox - optional
     * @throws Exception
     */
    public function __construct( $client_id, $client_secret, $sandbox = true ){

        if ($sandbox) $this->apiUrl = 'https://api.sandbox.paypal.com/';

        //validate
        if (! $client_id || ! $client_secret){
            throw new Exception('client_id and client_secret must be set before making request!');
        }

        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->accessToken = $this->getAccessToken();
    }


    /**
     * Private Method to get Access Token
     * @return string
     * @throws Exception
     */
    private function getAccessToken(){

        $endPoint = 'v1/oauth2/token?grant_type=client_credentials';
        $requestUrl = $this->apiUrl.$endPoint;
        $httpVerb = 'POST';
        $data = ['grant_type' => 'client_credentials'];
        $data = http_build_query($data);
        $header = [
            'Accept: application/json',
            'Accept-Language: en_US',
            'Authorization: Basic '.base64_encode($this->client_id.':'.$this->client_secret)
        ];

        $result = $this->curlRequest($requestUrl, $httpVerb, $data, $header);

        if (isset($result['error']) ) {
            throw new Exception('Error - ' . $result['error_description']);
        }
        else{
            return $result['access_token'];
        }

    }


    /**
     * Create Paypal Profile ID
     * @param $data
     * @return string
     */
    public function createWebProfile( $data = array() ){

        $site_name = get_bloginfo('name');

        if( MONEY_SITE_NAME === $site_name && MONEY_PAYPAL_PROFILE_ID ){
            return MONEY_PAYPAL_PROFILE_ID;
        }   

        $default = array(
            "name" => $site_name . " Money ADS",
            "presentation" => array(
                "brand_name" => $site_name,
            ),
            'input_fields' => array(
                'allow_note' => true,
                'no_shipping' => 1,
                'address_override' => 1
            ),
        );
        $data = array_merge( $default, $data );
        $request = $this->post('/v1/payment-experience/web-profiles', $data);

        update_option( 'money_paypal_web_proile_id', $request['id'] );
        update_option( 'money_site_name', $site_name );

        return $request['id'];
    }


    /**
     * Get Payment Approval Url
     * @param array $data
     * @return string
     */
    public function getApprovalUrl( $data = array() ){

        $default = array(
            'intent' => 'sale',
            'payer' => array(
                'payment_method' => 'paypal'
            ),
            'transactions' => array(
                array(
                    'amount' => array(
                        'total' => '12.55',
                        'currency' => 'USD'
                    ),
                    'description' => '',
                )
            ),
            'redirect_urls' => array(
                'return_url' => 'http://127.0.0.1/dialog-checkout-success.php',
                'cancel_url' => 'http://127.0.0.1/dialog-checkout-cancel.php'
            )
        );

        $data = array_merge( $default, $data );
        $request = $this->post('/v1/payments/payment', $data);

        return $request['links'][1]['href'];
    }


    /**
     * Execute payment
     * @param $paymentId
     * @param $payerId
     * @return array
     * @throws Exception
     */
    public function executePayment( $paymentId, $payerId ){

        if( ! $paymentId || ! $payerId ){
            throw new Exception('Payment ID & Payer ID are required');
        }

        return $this->post( '/v1/payments/payment/' .$paymentId. '/execute/', array( 'payer_id' => $payerId ) );
    }


    /**
     * Refund Compete Payment
     * @param $sale_id
     * @param $amount
     * @return array
     * @throws Exception
     */
    public function refundPayment( $sale_id, $amount ){

        if( is_array( $amount ) ) {

            $default = array(
                'total' => '20',
                'currency' => 'USD'
            );
            $amount = array( 'amount' => array_merge( $default, $amount ) );

        }

        return $this->post( '/v1/payments/sale/' .$sale_id. '/refund', $amount );
    }


    /**
     * Magic Method to request http verb
     * @param $method
     * @param $arguments
     * @return array
     * @throws Exception
     */
    public function __call($method, $arguments){

        $httpVerb = strtoupper($method);
        $allowedHttpVerbs = ['GET', 'POST', 'PATCH', 'PUT', 'DELETE'];

        //Validate http verb
        if (in_array($httpVerb, $allowedHttpVerbs)){
            $endPoint = $arguments[0];
            $data = isset($arguments[1]) ? $arguments[1] : [];
            return $this->request($httpVerb, $endPoint, $data);
        }

        throw new Exception('Invalid http verb!');
    }


    /**
     * Call PayPal API
     * @param string $httpVerb
     * @param string $endPoint - (https://developer.paypal.com/docs/api/)
     * @param mixed $data - Optional
     * @return array
     * @throws Exception
     */
    public function request( $httpVerb = 'GET', $endPoint, $data = false ){

        //validate Token
        if (! $this->accessToken){
            throw new Exception('AccessToken is required before making request!');
        }

        $endPoint = ltrim($endPoint, '/');
        $httpVerb = strtoupper($httpVerb);
        $requestUrl = $this->apiUrl.$endPoint;
        $header = [
            'Content-Type: application/json',
            'Authorization: Bearer '.$this->accessToken
        ];

        if ( is_array($data) ) {
            $data = json_encode($data);
        }

        return $this->curlRequest($requestUrl, $httpVerb, $data, $header);
    }


    /**
     * Request using curl extension
     * @param string $url
     * @param string $httpVerb
     * @param mixed $data - Optional
     * @param array $header
     * @param int $curlTimeout
     * @return array
     * @throws Exception
     */
    private function curlRequest($url, $httpVerb, $data = false, array $header = [], $curlTimeout = 15){

        if (function_exists('curl_init') && function_exists('curl_setopt')){

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_USERAGENT, 'VPS/PP-API');
            curl_setopt($ch, CURLOPT_TIMEOUT, $curlTimeout);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $httpVerb);

            if (!empty($header)){
                curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            }

            //Submit data
            if (!empty($data)){
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            }

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            curl_close($ch);

            return $result ? json_decode($result, true) : false;
        }

        throw new Exception('curl extension is missing!');
    }

}