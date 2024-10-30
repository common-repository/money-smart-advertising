<?php

class MoneyPluginSetup{


    /**
     * Display current step dialog
     */
    public static function displayDialog(){

        $step = get_option('money_setup_step', 1);
        $filePath = __DIR__ . '/views/setup/step-' . $step . '.php';

        if ( file_exists( $filePath ) ) {
            require_once $filePath;
        }
    }


}