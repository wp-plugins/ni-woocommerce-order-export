<?php 
include_once('ni-function.php');  
if( !class_exists( 'ni_order_summary' ) ) {
class ni_order_summary extends ni_function{
	public function __construct(){
		//echo "dsadas";
		$this->test();
	}
	/*Get Sales Total*/
	function get_sales($start_date=NULL,$end_date=NULL)
	{ 
		global $wpdb;	
		$query = "SELECT
				SUM(order_total.meta_value)as 'total_sales'
				FROM {$wpdb->prefix}posts as posts			
				LEFT JOIN  {$wpdb->prefix}postmeta as order_total ON order_total.post_id=posts.ID 
				
				WHERE 
				posts.post_type ='shop_order' 
			    AND order_total.meta_key='_order_total' ";
				if ($start_date!=NULL && $end_date!=NULL)
			$query .=" AND   date_format( posts.post_date, '%Y-%m-%d') BETWEEN '{$start_date}' AND '{$end_date}'";
				
		$results = $wpdb->get_var($query);
		$results = isset($results) ? $results : "0";
		return $results;
	}
	/*Get Sales Count*/
	function get_sales_count($start_date=NULL,$end_date=NULL)
	{ 
		global $wpdb;	
		$query = "SELECT
				count(order_total.meta_value)as 'sales_count'
				FROM {$wpdb->prefix}posts as posts			
				LEFT JOIN  {$wpdb->prefix}postmeta as order_total ON order_total.post_id=posts.ID 
				
				WHERE 
				posts.post_type ='shop_order' 
			    AND order_total.meta_key='_order_total' ";
				if ($start_date!=NULL && $end_date!=NULL)
			$query .=" AND   date_format( posts.post_date, '%Y-%m-%d') BETWEEN '{$start_date}' AND '{$end_date}'";
				
		$results = $wpdb->get_var($query);		
		return $results;
	}
	function get_customer($start_date=NULL,$end_date=NULL,$type="GUEST")
	{
		/*role_no  Customer = 2, Gest User =0 */
		global $wpdb;	
		$query = "SELECT
				count(customer_user.meta_value)as 'customer_count'
				FROM {$wpdb->prefix}posts as posts			
				LEFT JOIN  {$wpdb->prefix}postmeta as customer_user ON customer_user.post_id=posts.ID 
				
				WHERE 
				posts.post_type ='shop_order' 
			    AND customer_user.meta_key='_customer_user' ";
		if ($type=="GUEST")
			 $query .= " AND customer_user.meta_value='0'  ";
		else
			 $query .= " AND customer_user.meta_value!='0'  ";
			
			if ($start_date!=NULL && $end_date!=NULL)
			$query .=" AND   date_format( posts.post_date, '%Y-%m-%d') BETWEEN '{$start_date}' AND '{$end_date}'";
				
		$results = $wpdb->get_var($query);		
		return $results;
	} 
	function test()
	{//echo date("Y-m-d");
		?>
	   <div class="wrap">
        <div class="order-summary">
        	<div class="summary-title">Sales Summary</div>
            <div class="box">
            	<div class="circle">Total Sales  <br /><br /> <span> <?php  echo woocommerce_price( $this->get_sales())  ; ?>  </span></div>
            </div>
            <div class="box">
            	<div class="circle">Total Order  <br /> <br /> <span> # <?php  echo $this->get_sales_count();?>  </span></div>
            </div>
            <div class="box">
            	<div class="circle">Today Sales<br /><br /><span><?php echo  woocommerce_price(  $this->get_sales(date("Y-m-d"),date("Y-m-d"))); ?> </span> </div>
           </div>
           <div class="box">
            	<div class="circle">Today Order  <br /> <br /><span># <?php echo $this->get_sales_count(date("Y-m-d"),date("Y-m-d"))?> </span></div>
           </div>
           <div class="box">
            	<div class="circle">Today Customer  <br /> <br /><span># <?php echo $this->get_customer(date("Y-m-d"),date("Y-m-d"),"CUST");?> </span></div>
           </div>
           <div class="box">
            	<div class="circle">Today<br />  Guest Customer  <br /><br /> # <span><?php echo $this->get_customer(date("Y-m-d"),date("Y-m-d"),"GUEST");?></span>  </div>
           </div>
        </div>
       </div>
	 <?php
		
	}
}
}
?>