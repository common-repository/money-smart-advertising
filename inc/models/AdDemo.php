<?php

class MoneyAdDemo extends MoneyModelAbstract{

    protected $tableName = 'money_ads_demo';


    /**
     * Add New
     * @param array $data
     * @return object
     */
    public function addNew( $data = array() ){
        
        global $wpdb;

        $default = array(
            'id' => null,
            'ad_id' => null
        );

        $wpdb->insert(
            $wpdb->base_prefix . $this->tableName,
            array_merge( $default, $data )
        );

        return $this->get( $wpdb->insert_id );
    }

}