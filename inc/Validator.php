<?php

class MoneyValidator{

    private static $errors = array();


    /**
     * Verify Nonce
     * @param $haystack
     * @param string $key
     * @return bool|string
     */
    public static function nonce( $haystack, $key = 'nonce' ){

        $message = __( 'Invalid nonce', 'ddabout' );
        $value = self::required( $haystack, $key, $message );

        // This nonce is not valid.
        if ( ! wp_verify_nonce( $value, 'money' ) ) {
            self::addError( $message );
            return false;
        }

        return $value;
    }


    /**
     * Check Ad
     * @param MoneyModelAbstract $adModel
     * @param $haystack
     * @param string $key
     * @return null|object
     */
    public static function ad( MoneyModelAbstract $adModel, $haystack, $key = 'ad_id' ){

        $message = __('Invalid Ad','ddabout');
        $id = (int)self::required( $haystack, $key, $message);

        if( ! $ad = $adModel->get( $id ) ){
            self::addError( $message );
        }

        return $ad;
    }


    /**
     * Validate ad titles
     * @param $haystack
     * @param string $key
     * @return bool|string
     */
    public static function adTitle( $haystack, $key = 'title' ){
        $title = self::required( $haystack, $key, __('Invalid title','ddabout') );
        return sanitize_text_field( $title );
    }


    /**
     * Validate ad description [ not required ]
     * @param $haystack
     * @param string $key
     * @return bool|string
     */
    public static function adDescription( $haystack, $key = 'description' ){
        return sanitize_text_field( $haystack[ $key ] );
    }


    /**
     * Validate ad content type
     * @param $haystack
     * @param string $key
     * @return bool|string
     */
    public static function adContentType( $haystack, $key = 'content' ){
        $message =  __('Invalid content type','ddabout');
        $value = self::required( $haystack, $key, $message );

        if( ! in_array( $value, array('image', 'video', 'audio', 'custom') ) ){
            self::addError( $message );
            return false;
        }

        return $value;
    }


    /**
     * Validate Ad price
     * @param $haystack
     * @param string $key
     * @return false|float
     */
    public static function adPrice( $haystack, $key = 'pricce' ){
        return self::isFloat( $haystack, $key, __('Invalid price','ddabout'));
    }


    /**
     * Validate ad Currency
     * @param $haystack
     * @param string $key
     * @return bool|string
     */
    public static function adCurrency( $haystack, $key = 'currency' ){

        $allCurrencies = MoneyHelper::adCurrencies();
        $message = __('Invalid currency','ddabout');
        $value = self::required( $haystack, $key, $message );

        if( ! array_key_exists( $value, $allCurrencies ) ){
            self::addError( $message );
            return false;
        }

        return $value;
    }


    /**
     * Validate ad Content
     * @param $haystack
     * @param string $key
     * @return bool
     */
    public static function adContent( $haystack, $key = 'content' ){
        $content = self::required( $haystack, $key, __('Invalid content','ddabout') );
        return sanitize_text_field( $content );
    }


    /**
     * Validate ad url
     * @param $haystack
     * @param string $key
     * @return bool|string
     */
    public static function adUrl( $haystack, $key = 'url' ){
        return self::isUrl( $haystack, $key, __('Invalid url','ddabout') );;
    }


    /**
     * Validate ad strategy
     * @param $haystack
     * @param string $key
     * @return bool
     */
    public static function adStrategyType( $haystack, $key = 'strategy_type' ){

        $message = __('Invalid strategy','ddabout');
        $value = self::required( $haystack, $key, $message );

        if( ! in_array( $value, array( 'days', 'clicks', 'views') ) ){
            self::addError( $message );
            return false;
        }

        return $value;
    }


    /**
     * Validate ad strategy value
     * @param $haystack
     * @param string $key
     * @return bool
     */
    public static function adStrategyValue( $haystack, $key = 'strategy_value' ){
        return self::positive( $haystack, $key, __('Invalid strategy number','ddabout'));
    }


    /**
     * Validate ad display when
     * @param $haystack
     * @param string $key
     * @return bool
     */
    public static function adDisplayWhen( $haystack, $key = 'display_when' ){

        $message = __('Invalid display when','ddabout');
        $value = self::required( $haystack, $key, $message );

        if( ! in_array( $value, array( 'page_load', 'button_click' ) ) ){
            self::addError( $message );
            return false;
        }

        return $value;
    }


    /**
     * Validate ad display on
     * @param $haystack
     * @param string $key
     * @return bool
     */
    public static function adDisplayOn( $haystack, $key = 'display_on' ){

        $message = __('Invalid display on','ddabout');
        $value = self::required( $haystack, $key, $message );

        if( ! in_array( $value, array( 'all', 'homepage', 'pages', 'posts' ) ) ){
            self::addError( $message );
            return false;
        }

        return $value;
    }


    /**
     * Its not required, but it must be called
     * @param $haystack
     * @param string $key
     * @return bool
     */
    public static function adDisplayOnPages( $haystack, $key = 'display_on_pages' ){ return sanitize_text_field( $haystack[$key] ); }
    public static function adDisplayOnPosts( $haystack, $key = 'display_on_posts'  ){ return sanitize_text_field( $haystack[$key] ); }


    /**
     * Validate ad complexity
     * @param $haystack
     * @param string $key
     * @return bool
     */
    public static function adComplexity( $haystack, $key = 'complexity' ){

        $message = __('Invalid advanced option','ddabout');
        $value = self::required( $haystack, $key, $message );

        if( ! in_array( $value, array( 'simple', 'advanced' ) ) ){
            self::addError( $message );
            return false;
        }

        return $value;
    }


    /**
     * Validate ad complexity
     * @param $haystack
     * @param string $key
     * @return bool
     */
    public static function adAdvancedTimer( $haystack, $key = 'advanced_timer' ){

        $message = __('Invalid display time','ddabout');
        $value = intval( self::required( $haystack, $key, $message ) );

        if( $value < 1 ){
            self::addError( $message );
            return false;
        }

        return $value;
    }


    /**
     * Validate ad advanced action
     * @param $haystack
     * @param string $key
     * @return bool
     */
    public static function adAdvancedAction( $haystack, $key = 'advanced_action' ){

        $message = __('Invalid action','ddabout');
        $value = self::required( $haystack, $key, $message );

        if( ! in_array( $value, array( 'close', 'gotolink' ) ) ){
            self::addError( $message );
            return false;
        }

        return $value;
    }


    /**
     * Validate ad action url
     * @param $haystack
     * @param string $key
     * @return bool
     */
    public static function adAdvancedUrl( $haystack, $key = 'advanced_url' ){
        return self::isUrl( $haystack, $key, __('Invalid url','ddabout') );;
    }


    /**
     * Validate ad action text
     * @param $haystack
     * @param string $key
     * @return bool
     */
    public static function adAdvancedText( $haystack, $key = 'advanced_text' ){
        $text = self::required( $haystack, $key, __('Invalid action text','ddabout') );
        return sanitize_text_field( $text );
    }


    /**
     * Validate ad style shadow
     * @param $haystack
     * @param string $key
     * @return bool
     */
    public static function adStyleShadow( $haystack, $key = 'style_shadow' ){
        return self::isEnabled( $haystack, $key, __('Invalid shadow','ddabout') );
    }


    /**
     * Validate ad style responsive
     * @param $haystack
     * @param string $key
     * @return bool
     */
    public static function adStyleResponsive( $haystack, $key = 'style_responsive' ){
        return self::isEnabled( $haystack, $key, __('Invalid Responsive','ddabout') );
    }


    /**
     * Validate ad style position
     * @param $haystack
     * @param string $key
     * @return bool
     */
    public static function adStylePosition( $haystack, $key = 'style_position' ){

        $message = __('Invalid position','ddabout');
        $value = self::required( $haystack, $key, $message );

        if( ! in_array( $value, array( 'center', 'custom' ) ) ){
            self::addError( $message );
            return false;
        }

        return $value;
    }


    /**
     * Validate ad style top
     * @param $haystack
     * @param string $key
     * @return bool
     */
    public static function adStyleTop( $haystack, $key = 'style_top' ){
        $prop = self::styleProperty( $haystack, $key, __('Invalid top position','ddabout') );
        return sanitize_text_field( $prop );
    }


    /**
     * Validate ad style bottom
     * @param $haystack
     * @param string $key
     * @return bool
     */
    public static function adStyleBottom( $haystack, $key = 'style_bottom' ){
        $prop = self::styleProperty( $haystack, $key, __('Invalid bottom position','ddabout') );
        return sanitize_text_field( $prop );
    }


    /**
     * Validate ad style right
     * @param $haystack
     * @param string $key
     * @return bool
     */
    public static function adStyleRight( $haystack, $key = 'style_right' ){
        $prop = self::styleProperty( $haystack, $key, __('Invalid right position','ddabout') );
        return sanitize_text_field( $prop );
    }


    /**
     * Validate ad style left
     * @param $haystack
     * @param string $key
     * @return bool
     */
    public static function adStyleLeft( $haystack, $key = 'style_left' ){
        $prop = self::styleProperty( $haystack, $key, __('Invalid left position','ddabout') );
        return sanitize_text_field( $prop );
    }


    /**
     * Validate ad style width
     * @param $haystack
     * @param string $key
     * @return bool
     */
    public static function adStyleWidth( $haystack, $key = 'style_width' ){
        $prop = self::styleProperty( $haystack, $key, __('Invalid width','ddabout') );
        return sanitize_text_field( $prop );
    }


    /**
     * Validate ad style height
     * @param $haystack
     * @param string $key
     * @return bool
     */
    public static function adStyleHeight( $haystack, $key = 'style_height' ){
        $prop = self::styleProperty( $haystack, $key, __('Invalid height','ddabout') );
        return sanitize_text_field( $prop );
    }


    /**
     * Validate email
     * @param $haystack
     * @param string $key
     * @return bool|string
     */
    public static function email( $haystack, $key = 'email' ){

        $message = __('Invalid email','ddabout');
        $value = self::required( $haystack, $key, $message );

        if ( filter_var( $value, FILTER_VALIDATE_EMAIL ) === false ) {
            self::addError( $message );
            return false;
        }

        return $value;
    }


    /**
     * Validate paypal mode
     * @param $haystack
     * @param string $key
     * @return bool|string
     */
    public static function paypalMode( $haystack, $key = 'paypal_mode' ){

        $message = __('Invalid paypal mode','ddabout');
        $value = self::required( $haystack, $key, $message );

        if( ! in_array( $value, array( 'live', 'sandbox' ) ) ){
            self::addError( $message );
            return false;
        }

        return $value;
    }


    /**
     * This key is required
     * @param $haystack
     * @param $key
     * @param $message
     * @return mixed
     */
    public static function required( $haystack, $key, $message ){

        if( ! isset( $haystack[ $key ] ) ){
            self::addError( $message );
            return false;
        }

        if( $haystack[ $key ] == '' ){
            self::addError( $message );
            return false;
        }

        return $haystack[ $key ];
    }


    /**
     * Check for positive
     * @param $haystack
     * @param $key
     * @param $message
     * @return bool
     */
    private static function positive( $haystack, $key, $message ){
        $value = intval( self::required( $haystack, $key, $message ) );

        if( $value < 1 ){
            self::addError( $message );
            return false;
        }

        return $value;
    }


    /**
     * Check if its enabled or disabled
     * @param $haystack
     * @param $key
     * @param $message
     * @return bool|string
     */
    public static function isEnabled( $haystack, $key, $message ){

        $value = self::required( $haystack, $key, $message );

        if( ! in_array( $value, array( 'on', 'off' ) ) ){
            self::addError( $message );
            return false;
        }

        return $value;
    }


    /**
     * Validate float
     * @param $haystack
     * @param $key
     * @param $message
     * @return bool|float
     */
    private static function isFloat( $haystack, $key, $message ){
        $value = self::required( $haystack, $key, $message);

        // use the filter, is_float make problems
        if ( filter_var( $value, FILTER_VALIDATE_FLOAT ) === false ) {
            self::addError($message);
            return false;
        }

        return MoneyHelper::fixPaypalFloatIssue( $value );
    }


    /**
     * Validate & return proper css value
     * @param $haystack
     * @param $key
     * @param $message
     * @return mixed|string
     */
    private static function styleProperty( $haystack, $key, $message){

        $value = self::required( $haystack, $key, $message );

        if( $value !== 'auto' && preg_match( '/^[0-9]+$/', $value ) ){
            $value .= 'px';
        }

        return $value;
    }


    /**
     * Is Valid url
     * @param $haystack
     * @param $key
     * @param $message
     * @return bool|mixed
     */
    private static function isUrl( $haystack, $key, $message ){

        $value = self::required($haystack, $key, $message);

        if (filter_var($value, FILTER_VALIDATE_URL) === false) {
            self::addError($message);
            return false;
        }

        return $value;
    }


    /**
     * Add Error
     * @param $message
     */
    public static function addError( $message ){
        if( ! in_array( $message, self::$errors ) ){
            self::$errors[] = $message;
        }
    }


    /**
     * Return errors
     * @return array
     */
    public static function getErrors(){
        return self::$errors;
    }


    /**
     * Clear errors
     */
    public static function clearErrors(){
        self::$errors = array();
    }

}