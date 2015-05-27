<?php 
if ( ! defined( 'ABSPATH' ) ) { exit;}
include_once('ni-function.php'); 
if( !class_exists( 'ni_order_export' ) ) {
	class ni_order_export extends ni_function
	{
		var $constant_variable = array();
		
		public function __construct($constant_variable)
		{
			$this->constant_variable = $constant_variable;
			add_action( 'admin_menu',  array(&$this,'admin_menu' ));
			add_action( 'admin_enqueue_scripts',  array(&$this,'admin_enqueue_scripts' ));
			add_action( 'wp_ajax_ni_action',  array(&$this,'ni_ajax_action' )); /*used in form field name="action" value="my_action"*/
			add_action('admin_init', array( &$this, 'admin_init' ) );
		}
		/*Add Admin Menu*/
		function admin_menu()
		{
			
			
			add_menu_page('Order Export','Order Export','manage_options',$this->constant_variable['plugin_menu'],array(&$this,'add_menu_page')
		,plugins_url( '../images/icon.png', __FILE__ )
		,6);
    	add_submenu_page($this->constant_variable['plugin_menu'], 'Summary', 'Sales Summary', 'manage_options',$this->constant_variable['plugin_menu'] , array(&$this,'add_menu_page'));
    	add_submenu_page($this->constant_variable['plugin_menu'], 'Order List', 'Order List', 'manage_options', 'ni-order-list' , array(&$this,'add_menu_page'));
		}
		/*Add page to menu*/
		function add_menu_page()
		{
			$page=$this->get_request("page");
			//echo $page;
			if ($page=="ni-order-list")
			{
				include_once("ni-order-list.php");
				$obj =  new ni_order_list();
				$obj->page_init();
				
			}
			if ($page=="ni-order-export")
			{
				include_once("ni-order-summary.php");
				$obj =  new ni_order_summary();
				//$obj->page_init();
				
			}
		}
		function admin_enqueue_scripts(){
			 wp_enqueue_script( 'ajax-script', plugins_url( '../assets/js/ni-order-export-script.js', __FILE__ ), array('jquery') );
			 wp_localize_script( 'ajax-script', 'ajax_object',array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'we_value' => 1234 ) );
			 wp_register_style( 'sales-report-style', plugins_url( '../assets/css/ni-order-export-style.css', __FILE__ ));
			 wp_enqueue_style( 'sales-report-style' );
		}
		function ni_ajax_action()
		{
			$ni_action_ajax=$this->get_request("ni_action_ajax");
			if($ni_action_ajax =="ni_order_list")
			{
				//$this->print_data($_REQUEST);
				include_once("ni-order-list.php");
				$obj =  new ni_order_list();
				$obj->get_order_list();	
			}
			die;
		}
		function admin_init(){
			if(isset($_REQUEST['btn_excel_export'])){
				$today = date_i18n("Y-m-d-H-i-s");				
				$FileName = "order-list"."-".$today.".xls";	
				
				include_once("ni-order-list.php");
				$obj = new ni_order_list();
				$obj->ni_order_export($FileName,"xls");
				die;
			}	
		}
	}
}
?>