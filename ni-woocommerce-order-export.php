<?php 
/*
Plugin Name: Ni Woocommerce Order Export
Description: Ni Woocommerce Order Export help top keep the track of daily, weekly, monthly, yearly sales report with date range filter option and export functionality.
Author: Anzar Ahmed
Version: 1.2
Author URI: http://mywebdesire.com/
License: GPLv2
*/
include_once('include/ni-order-export.php'); 

$constant_variable = array(
	'plugin_name' => 'Ni Woocommerce Order Export',
	'plugin_role' => 'manage_options',
	'plugin_key' => 'ni_order_export',
	'plugin_menu' => 'ni-order-export',
	"plugin_file" 			=> __FILE__
);
$GLOBALS['ni_order_export'] = new ni_order_export($constant_variable );
?>