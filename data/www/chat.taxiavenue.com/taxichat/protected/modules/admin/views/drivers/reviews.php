<script>
$(document).ready(function() { 
	ClickPaginate();
	
}); 
	
	function ClickPaginate() {
		$('.pagination a').click(function () {
			var url = $(this).attr('href');
			$.ajax({
				url: url,
				type: 'get',
				success: function(data){	
					$('#tabs-2').html(data);	
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
  <div>Рейтинг:<span><?=$driver->rating?></span></div>
  <div>Комиссия:<span><?php echo($driver->commission > 0?$driver->commission.($driver->is_percent?'%':'грн'):$order_commission.'%')?></span></div>
  <div class="clearfix">
	<fieldset>
	  <legend>Отзывы клиентов</legend>
	  
		<?php if(!empty($reviews_driver)) {
			foreach($reviews_driver as $review) { ?>
				<div>
					<p><?=$review->date_review?><span><?=$review->customer->nickname?></span><span class="subRight">Оценка: <span><?=$review->evaluation->name?></span></span></p>
					<p><?=$review->text?></p>
				</div>
		<?php } 
		} else 
		echo('<p>Отзывов нет</p>'); ?>
	  
		<?php $this->widget('MyLinkPager', array('pages' => $pages)) ?>
	 
	</fieldset>
  </div>
