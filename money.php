<?php
/**
 * Plugin Name: Money
 * Plugin URI: http://ddabout.com/plugins/images-regenerator
 * Description: The best way to earn money from ads.
 * Version: 1.0.0
 * Author: ddabout
 * Author URI: http://ddabout.com
 * Text Domain: ddabout
**/


defined( 'ABSPATH' ) or die( 'Nope, not accessing this' );


/** @files required for all modes */
require_once __DIR__ . "/inc/Helper.php";
require_once __DIR__ . "/inc/Config.php";
require_once __DIR__ . "/inc/Ajax.php";
require_once __DIR__ . "/inc/Validator.php";
require_once __DIR__ . "/inc/mailer/Mailer.php";
require_once __DIR__ . '/inc/filters.php';
require_once __DIR__ . "/inc/models/ModelAbstract.php";
require_once __DIR__ . "/inc/models/Ad.php";
require_once __DIR__ . "/inc/models/AdSold.php";
require_once __DIR__ . "/inc/models/AdDemo.php";
require_once __DIR__ . '/inc/models/Statistic.php';
require_once __DIR__ . '/inc/paypal/Paypal.php';
require_once __DIR__ . '/inc/paypal/RestApi.php';


/** @config **/
new MoneyConfig();


/** @detect mode **/
$isAdmin = is_admin();
$_moneyMode = isset( $_GET['money-mode'] ) ? $_GET['money-mode'] : 'front';


/** All Modes **/
new MoneyAjax();

/** Admin **/
if( $isAdmin ){
    require_once __DIR__ . "/inc/ImportExport.php";
    require_once __DIR__ . "/inc/admin/Admin.php";
    require_once __DIR__ . "/inc/admin/PluginSetup.php";

    new MoneyAdmin();
}

/** Front **/
else{
    require_once __DIR__ . "/inc/front/Front.php";

    // Preview
    if( $_moneyMode === 'preview' ){
        require_once __DIR__ . "/inc/preview/Preview.php";
        new MoneyPreview();
    }
    else{
        new MoneyFront();
    }

}


/** @text domain **/
add_action( 'plugins_loaded', 'moneyLoadTextDomain' );
function moneyLoadTextDomain() {
    load_plugin_textdomain( 'ddabout', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
}


/** @activation hook **/
register_activation_hook( __FILE__, 'moneyActivationHook' );
function moneyActivationHook() {
    global $wpdb;

    require_once __DIR__ . '/inc/tables-sql.php';

    if ( ! function_exists('dbDelta') ) {
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    }

    dbDelta( $ads );
    dbDelta( $adsDemo );
    dbDelta( $adsSold );
    dbDelta( $statistics );
}