<?php

class MoneyStatistic extends MoneyModelAbstract{

    protected $tableName = 'money_statistics';


    /**
     * Add new
     * @param array $data
     * @return object
     */
    public function addNew( $data = array() ){

        global $wpdb;

        $emptyArrayS = serialize(array());

        $default = array(
            'id' => null,
            'views_per_day' => $emptyArrayS,
            'clicks_per_day' => $emptyArrayS,
            'countries' => $emptyArrayS,
            'total_days' => 0,
            'total_views' => 0,
            'total_clicks' => 0,
        );
        
        $wpdb->insert(
            $wpdb->base_prefix . $this->tableName,
            array_merge( $default, $data )
        );

        return $this->get( $wpdb->insert_id );
    }

}
