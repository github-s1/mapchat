<script>
	$(document).ready(function() { 
		ClickPaginate();
	}); 
	
	function ClickPaginate() {
		$('#tabsOneOperator .pagination a').click(function () {
			var url = $(this).attr('href')+"/ajax/1";
			$.ajax({
				url: url,
				type: 'get',
				success: function(data){	
					$('#tabsOneOperator').html(data);	
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
<?php include('table_ordersOp.php'); ?>