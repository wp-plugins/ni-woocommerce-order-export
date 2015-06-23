<?php 
if ( ! defined( 'ABSPATH' ) ) { exit;}
include_once('ni-function.php'); 
if( !class_exists( 'ni_order_list' ) ) {
	class ni_order_list extends ni_function{
		public function __construct(){
			
		}
		public function page_init(){
		?>
        <form id="ni_frm_order_export" class="ni-frm-order-export" name="ni_frm_order_export" action="" method="post">
            <table>
                <tr>
                    <td>Select Order</td>
                    <td><select name="select_order" id="select_order">
                    <option value="today">Today</option>
                    <option value="yesterday">Yesterday</option>
                    <option value="last_7_days">Last 7 days</option>
                    <option value="last_30_days">Last 30 days</option>
                    <option value="this_year">This year</option>
                    </select>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align:right"><input type="submit" value="Search" id="SearchOrder" /></td>
                </tr>
            </table>
            <input type="hidden" name="action" value="ni_action" />
            <input type="hidden" name="ni_action_ajax" value="ni_order_list" />
        </form>
        <div class="ajax_content"></div>
        <?php
			
		}
		function get_order_query($type="DEFAULT"){
			global $wpdb;	
			$today = date("Y-m-d");
	    	$select_order = $this->get_request("select_order");
			
			$query = "SELECT 
				posts.ID as order_id
				,post_status as order_status
				, date_format( posts.post_date, '%Y-%m-%d') as order_date 
				FROM {$wpdb->prefix}posts as posts			
				WHERE 
						posts.post_type ='shop_order' 
						";
				 switch ($select_order) {
					case "today":
						$query .= " AND   date_format( posts.post_date, '%Y-%m-%d') BETWEEN '{$today}' AND '{$today}'";
						break;
					case "yesterday":
						$query .= " AND  date_format( posts.post_date, '%Y-%m-%d') = date_format( DATE_SUB(CURDATE(), INTERVAL 1 DAY), '%Y-%m-%d')";
						break;
					case "last_7_days":
						$query .= " AND  date_format( posts.post_date, '%Y-%m-%d') BETWEEN date_format(DATE_SUB(CURDATE(), INTERVAL 7 DAY), '%Y-%m-%d') AND   '{$today}' ";
						break;
					case "last_30_days":
							$query .= " AND  date_format( posts.post_date, '%Y-%m-%d') BETWEEN date_format(DATE_SUB(CURDATE(), INTERVAL 30 DAY), '%Y-%m-%d') AND   '{$today}' ";
						break;	
					case "this_year":
						$query .= " AND  YEAR(date_format( posts.post_date, '%Y-%m-%d')) = YEAR(date_format(CURDATE(), '%Y-%m-%d'))";			
						break;		
					default:
						$query .= " AND   date_format( posts.post_date, '%Y-%m-%d') BETWEEN '{$today}' AND '{$today}'";
				}
			$query .= "order by posts.post_date DESC";	
			
		 if ($type=="ARRAY_A") /*Export*/
		 	$results = $wpdb->get_results( $query, ARRAY_A );
		 if($type=="DEFAULT") /*default*/
		 	$results = $wpdb->get_results( $query);	
		 if($type=="COUNT") /*Count only*/	
		 	$results = $wpdb->get_var($query);		
			//echo $query;
			echo mysql_error();
		//	$this->print_data($results);
			return $results;	
		}
		/*get_order_list*/
		function get_order_list()
		{
			$this->get_order_grid();	
			
			
		}
		function get_order_data()
		{
			$order_query = $this->get_order_query("DEFAULT");
			
		
			if(count($order_query)> 0){
				foreach($order_query as $k => $v){
					
					/*Order Data*/
					$order_id =$v->order_id;
					$order_detail = $this->get_order_detail($order_id);
					foreach($order_detail as $dkey => $dvalue)
					{
							$order_query[$k]->$dkey =$dvalue;
						
					}
				}
			}
			else
			{
				echo "No Record Found";
			}
			return $order_query;
		}
		function get_order_grid()
		{
			$order_total = 0;
			$order_data = $this->get_order_data();
			
			//$this->print_data ($order_data);
			
			if(count($order_data)> 0)
			{
				?>
                <div style="text-align:right;margin-bottom:10px">
                <form id="ni_frm_order_export" action="" method="post">
                    <input type="submit" value="Excel" name="btn_excel_export" id="btn_excel_export" />
                    <input type="submit" value="Print" name="btn_print" id="btn_print" />
                    <input type="hidden" name="select_order" value="<?php echo $this->get_request("select_order");  ?>" />
                </form>
                </div>
				<div class="data-table">
				<table>
					<tr>
						<th>#ID</th>
						<th>Order Date</th>
						<th>Billing First Name</th> 
						<th>Billing Email</th> 
						<th>Billing Country</th> 
						<th>Status</th>
                        <th>Order Currency</th>
						<th>Order Total</th>
					</tr>
				
				<?php
				foreach($order_data as $k => $v){
					$order_total += isset($v->order_total)?$v->order_total:0;
				?>
					<tr>
						<td> <?php echo $v->order_id;?> </td>
						<td> <?php echo $v->order_date;?> </td>
						<td> <?php echo $v->billing_first_name;?> </td>
						<td> <?php echo $v->billing_email;?> </td>
						<td> <?php echo $this->get_country_name($v->billing_country);?> </td>
                       	<td> <?php echo ucfirst ( str_replace("wc-","", $v->order_status));?> </td>
                        <td> <?php echo $v->order_currency;?> </td>
						<td style="text-align:right"> <?php echo woocommerce_price($v->order_total);?> </td>
					</tr>	
				<?php }?>
				</table>
                <div style="text-align:right; margin-top:10px">
                	<?php  echo woocommerce_price($order_total); ?>
                </div>
				<?php
				
				//$this->print_data(	$order_data );
			}
		}
		/*Get Order Header information*/
		function get_order_detail($order_id)
		{
			$order_detail	= get_post_meta($order_id);
			$order_detail_array = array();
			foreach($order_detail as $k => $v)
			{
				$k =substr($k,1);
				$order_detail_array[$k] =$v[0];
			}
			return 	$order_detail_array;
		}
		function ni_order_export($file_name,$file_format)
		{
			$columns = array(					
				 "billing_first_name"			=>"Billing First Name"
				,"billing_email"				=>"Billing Email"
			  );
			  
			  $rows =$this->get_order_data();
			  
			  $i = 0;
			$export_rows = array();
			foreach ( $rows as $rkey => $rvalue ):	
					foreach($columns as $key => $value):
						switch ($key) {
							default:
								$export_rows[$i][$key] = isset($rvalue->$key) ? $rvalue->$key : '';
								break;
				}
					endforeach;
					$i++;
			endforeach;
			$this->ExportToCsv($file_name ,$export_rows,$columns,$file_format); 
			//die;
		}
		function get_print_content(){
		?>
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
			<html xmlns="http://www.w3.org/1999/xhtml">
			<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<title>Print</title>
			<link rel='stylesheet' id='sales-report-style-css'  href='<?php echo  plugins_url( '../assets/css/ni-order-export-style.css', __FILE__ ); ?>' type='text/css' media='all' />
			</head>
			
			<body>
				<?php 
					$this->get_order_grid();
				?>
			  <div class="print_hide" style="text-align:right; margin-top:15px"><input type="button" value="Back" onClick="window.history.go(-1)"> <input type="button" value="Print this page" onClick="window.print()">	</div>
			 
			</body>
			</html>

		<?php
		}
	}
}
?>