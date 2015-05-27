// JavaScript Document
jQuery(function($){


	$("#ni_frm_order_export" ).submit(function( event ) {
		$.ajax({
			url:ajax_object.ajaxurl,
			data:$( "#ni_frm_order_export" ).serialize() ,
			success:function(data) {
				$(".ajax_content").html(data);
			},
			error: function(errorThrown){
				console.log(errorThrown);
				alert("e");
			}
		}); 
		
		return false;
	});
	/*Form Submit*/
	$("#ni_frm_order_export").trigger("submit");
});