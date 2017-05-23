<script>
	$(document).ready(function() { 
		ClickPaginate();
	}); 
	
	function ClickPaginate() {
		$('#tabsFour .pagination a').click(function () {
			var url = $(this).attr('href');
			$.ajax({
				url: url,
				type: 'get',
				success: function(data){	
					$('#tabsFour').html(data);	
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