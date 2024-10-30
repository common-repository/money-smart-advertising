<?php

class MoneyAd extends MoneyModelAbstract{

    protected $tableName = 'money_ads';


    /**
     * Add new ad to db
     * @param array $data
     * @return object
     */
    public function addNew( $data = array() ){

        global $wpdb;

        $default = array(
            'id' => null,
            'title' => 'My Ad Title',
            'description' => '',
            'content_type' => 'image',
            'content' => plugins_url( '/../../assets/images/demo-300x300.png', __FILE__ ),
            'url' => '',
            'price' => '10',
            'strategy_type' => 'views',
            'strategy_value' => '1000',
            'display_when' => 'page_load',
            'display_on' => 'all',
            'display_on_pages' => '',
            'display_on_posts' => '',
            'complexity' => 'simple',
            'advanced_timer' => '10',
            'advanced_action' => 'close',
            'advanced_url' => 'http://mysite.com/download.zip',
            'advanced_text' => 'Close',
            'style_shadow' => 'on',
            'style_responsive' => 'on',
            'style_position' => 'center',
            'style_top' => '30px',
            'style_bottom' => 'auto',
            'style_left' => '30px',
            'style_right' => 'auto',
            'style_width' => '300px',
            'style_height' => 'auto',
        );

        $wpdb->insert(
            $wpdb->base_prefix . $this->tableName,
            array_merge( $default, $data )
        );

        return $this->get( $wpdb->insert_id );
    }

}
