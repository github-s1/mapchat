<script>
$(document).ready(function() { 
	ClickPaginate();
}); 
	
	function ClickPaginate() {
		$('#tabs-2 .pagination a').click(function () {
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
	
	function add_payment(flag = true) {
		var params = {};
		$('.stats_search').find('input').each(function(){
			params[$(this).attr("name")] = $(this).val();	
		});
		
		if(flag) {
			params['flag'] = 1;
		} else {
			params['flag'] = 0;
		}
		
		$.ajax({
			url: "<?=Yii::app()->params['siteUrl']?>/admin/statistics/new_payment/id/<?=$id?>",
			type: 'post',
			data: params,
			success: function(data){
				if(data == 1) {
					PaymentsHistory();
				}	
				return false;
			}
		});
		
	}
	
		
</script> 
 
 <div class="stats_info history clearfix">
            <div>Баланс<span><?=$driver->balance?> грн.</span></div>
            <div>Комиссия<span><?php echo($driver->commission > 0?$driver->commission.($driver->is_percent?'%':'грн'):$order_commission.'%')?></span></div>
          </div>
          <div class="stats_search clearfix">
            <?php $form=$this->beginWidget('CActiveForm', array(
				'enableAjaxValidation'=>false,
				'htmlOptions'=>array('enctype'=>'multipart/form-data'),
			)); ?>
              <label>Сумма:
                 <?php echo $form->textField($new_payment,'value'); ?>
              </label>
              <label>Описание:
                <?php echo $form->textField($new_payment,'descr'); ?>
              </label>
			  <a href="javascript: void(0);" onclick="add_payment(true);" class="add_button">Добавить</a>
			  <a href="javascript: void(0);" onclick="add_payment(false);" class="add_button del">Снять</a>
            <?php $this->endWidget(); ?>
          </div>
          <table class="stat_driver">
            <thead>
              <tr>
                <th>ID</th>
                <th>Дата</th>
                <th>Операция</th>
                <th>Сумма</th>
                <th>Баланс</th>
                <th>Рейтинг</th>
              </tr>
            </thead>
            <tbody>
				<?php if(!empty($payments_driver)) { 
					foreach($payments_driver as $p) { ?>
					  <tr>
						<td><?=$p->id?></td>
						<td><?=$p->date_create?></td>
						<td><?=$p->type->name?></td>
						<td><?=$p->value?> грн.</td>
						<td><?=$p->balance?> грн.</td>
						<td><?=$p->rating?></td>
					  </tr>
				<?php } 
				} ?>
            </tbody>
          </table>
           <?php $this->widget('MyLinkPager', array(
				'pages' => $pages_payments,
			)) ?>