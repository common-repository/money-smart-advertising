<?php

class MoneyAdSold extends MoneyModelAbstract{

    protected $tableName = 'money_ads_sold';


    /**
     * Add New
     * @param array $data
     * @return object
     */
    public function addNew( $data = array() ){

        global $wpdb;

        $default = array(
            'id' => null,
            'ad_id' => null,
            'buyer_id' => null,
            'statistic_id' => null,
            'date_purchase' => null,
            'date_start' => null,
            'date_end' => null,
            'status' => null,
            'hash' => null,
            'refund_amount' => null,
        );

        $wpdb->insert(
            $wpdb->base_prefix . $this->tableName,
            array_merge( $default, $data )
        );

        return $this->get( $wpdb->insert_id );
    }


    /**
     * Check if demo AD is active
     * @param $demo_ad_id
     * @return bool|string
     */
    public function isDemoAdActive( $demo_ad_id ){

        global $wpdb;

        // Active ?
        $result = $wpdb->get_row('
            SELECT * 
            FROM '.$wpdb->base_prefix.$this->tableName.' 
            WHERE status="active" AND ad_demo_id = '.$demo_ad_id
        );

        if( $result !== null ) return "active";

        // Waiting ?
        $result = $wpdb->get_row('
            SELECT * 
            FROM '.$wpdb->base_prefix.$this->tableName.' 
            WHERE status="pending" AND ad_demo_id = '.$demo_ad_id
        );

        if( $result !== null ) return 'waiting';

        // its not active or waiting
        return false;
    }
    
}