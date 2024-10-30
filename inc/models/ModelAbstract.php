<?php

abstract class MoneyModelAbstract{

    protected static $_instance;
    protected $tableName;

    
    /**
     * MoneyModelAbstract constructor.
     */
    public function __construct(){

        // table name is required
        if( $this->tableName === null ){
            throw new Exception('Table name is required');
        }

    }


    /**
     * Add New
     * @param array $data
     * @return object
     */
    abstract public function addNew( $data = array() );


    /**
     * Return Ad if exists
     * @param $id
     * @return null|object
     */
    public function get( $id ){
        global $wpdb;

        $sql = 'SELECT * FROM ' . $wpdb->base_prefix . $this->tableName . ' WHERE id = %d';
        $stmt = $wpdb->prepare( $sql, intval( $id ) );
        $row = $wpdb->get_row( $stmt );

        return $row;
    }


    /**
     * Update
     * @param $id
     * @param $data
     * @return bool
     */
    public function update( $id, $data ){

        if( ! $this->get( $id ) ) return false;

        global $wpdb;

        return $wpdb->update(
            $wpdb->base_prefix. $this->tableName,
            $data,
            array( 'id' => $id ) // where
        );
    }


    /**
     * Delete
     * @param array $where
     * @param null $where_format , ex : array( %d, %s )
     * @return bool
     */
    public function delete( $where, $where_format = null ){

        global $wpdb;

        return $wpdb->delete(
            $wpdb->base_prefix. $this->tableName,
            $where,
            $where_format
        );
    }


    /**
     * Get All, pagination support
     * @param array $data
     * @return array
     */
    public function all( $data = array() ){
        global $wpdb;

        $default = array(
            'pagination' => false,
            'page' => 0,
            'items_per_page' => 5,
            'columns' => '*',
            'join' => '',
            'where' => '',
            'order_by' => ''
        );
        $data = array_merge( $default, $data );

        // Where prepare
        if( $data['where'] !== '' ){
            $data['where'] = ' WHERE ' . $data['where'] . ' ';
        }

        // order by prepare
        if( $data['order_by'] !== '' ){
            $data['order_by'] = ' ORDER BY ' . $data['order_by'] . ' ';
        }

        if( $data['pagination'] ){

            $data['page'] = $data['page'] * $data['items_per_page'];

            $sql = "
                SELECT {$data['columns']}
                FROM {$wpdb->base_prefix}{$this->tableName} 
                {$data['join']}
                {$data['where']}
                {$data['order_by']}
                LIMIT {$data['page']},{$data['items_per_page']}";

            return $wpdb->get_results( $sql );
        }
        else{

            $sql = "
                SELECT {$data['columns']}
                FROM {$wpdb->base_prefix}{$this->tableName} 
                {$data['join']}
                {$data['where']}
                {$data['order_by']}";

            return $wpdb->get_results( $sql );
        }

    }


    /**
     * Get table name
     */
    public function getTableName(){
        return $this->tableName;
    }

}