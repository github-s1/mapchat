<script>
	$(document).ready(function() { 
		ClickPaginate();
	}); 
	
	function ClickPaginate() {
		$('#tabsSixApp .pagination a').click(function () {
			var url = $(this).attr('href')+"/ajax/1";
			$.ajax({
				url: url,
				type: 'get',
				success: function(data){	
					$('#tabsSixApp').html(data);	
					return false;
				},
				failure:function(){
				}
			});
			return false;
		});
		return false;
	}	
</script> 
<?php include('table_orders.php'); ?>