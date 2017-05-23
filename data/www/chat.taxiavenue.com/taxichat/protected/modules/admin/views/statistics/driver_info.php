<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.autocomplete.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/highcharts.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/exporting.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/chartScript.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$('#autocomplete').autocomplete("<?php echo Yii::app()->request->baseUrl; ?>/admin/statistics/get_driver", {	
			matchContains: false,
			scroll: true,
			highlight: false,
			width: 255,
			cache: false,
			autoFill:false,
			selectFirst:false,
			formatItem: function(row) {
				return "<a href='javascript: void(0);'>" + row[0] + ' '+ row[1] + "</a>";
			},
			formatResult: function(row) {
				return row[0].replace(/(<.+?>)/gi, '');
				
				//return true;
			},
		});
		
		 $( "#tabs" ).tabs();
		 
		 PaymentsHistory();
	});
	
	function PaymentsHistory() {
		$.ajax({
			url: "<?=Yii::app()->params['siteUrl']?>/admin/statistics/payments_history/id/<?=$id?><?php echo(isset($_GET['date_from']) && $_GET['date_from'] != ''?'/date_from/'.$_GET['date_from']:'');?><?php echo(isset($_GET['date_to']) && $_GET['date_to'] != ''?'/date_to/'.$_GET['date_to']:'');?>",
			type: 'get',
			success: function(data){	
				$('#tabs-2').html(data);	
				return false;
			},
			failure:function(){
			}
		});
	}
</script>

	<h1>Статистика</h1>
	<div class="stats">
		<a href="<?php echo Yii::app()->request->baseUrl; ?>/admin/statistics/index">Статистика</a>
		<a href="<?php echo Yii::app()->request->baseUrl; ?>/admin/statistics/drivers" class="active">Водители</a>
		<div class="stats_inner" id="tabs">
			<?php $form=$this->beginWidget('CActiveForm', array(
				'id'=>'filter-form',
				'enableAjaxValidation'=>false,
				'htmlOptions'=>array('enctype'=>'multipart/form-data'),
				'action'=>Yii::app()->request->baseUrl.'/admin/statistics/driver_view',
			)); ?>
			<label>Водитель:
				<input type="text" name="phone" id="autocomplete" autocomplete="off">
			</label>
			<button class="driver_submit">Выбрать</button>
			<?php $this->endWidget(); ?>
			<?php $form=$this->beginWidget('CActiveForm', array(
				'id'=>'filter-form',
				'enableAjaxValidation'=>false,
				'htmlOptions'=>array('enctype'=>'multipart/form-data'),
			)); ?>
			<label for="">Создан от:
				<input type="text" name="filter[date_from]" class="datepicker" value="<?php echo(!empty($date_from)?$date_from:''); ?>"/>
				<input type="hidden" name="dateFrom" value="<?php echo(!empty($date_from)?$date_from:''); ?>">
				<input type="hidden" name="driver_id" value="<?php echo(!empty($id)?$id:''); ?>">
			</label>
			<label for="">До:
				<input type="text" name="filter[date_to]" class="datepicker" value="<?php echo(!empty($date_to)?$date_to:''); ?>"/>
				<input type="hidden" name="dateFrom" value="<?php echo(!empty($date_to)?$date_to:''); ?>">
			</label>
			<input type="submit" value="Искать" class="search_button">
			<input type="radio" name="chartFilter" value="0">День
			<input type="radio" name="chartFilter" value="1" checked>Месяц
			<?php $this->endWidget(); ?>
			<div class="clearfix"></div>		  
			<p class="stats_name">Позывной:<span><?=$driver->phone?></span></p>
			<p>Имя:<span><?=$driver->surname.' '.$driver->name?></span></p>
			<ul class="hist_balance">
				<li><a href="#tabs-1">Заказы</a></li>
				<li><a href="#tabs-2">История баланса</a></li>
			</ul>
			<div id="tabs-1" class="hist_balance">
				<div class="stats_info clearfix">
					<section class="chartInner">
						<div id="container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
					</section>
					<div onclick="getChart(1)" data-fn="1" class="curPointer chartLink active">Сумма<span><?=$completed_summ?> грн.</span></div>
					<div onclick="getChart(4)" data-fn="4" class="curPointer chartLink">Выполнено<span><?=$completed_count?></span></div>
					<div onclick="getChart(2)" data-fn="2" class="curPointer chartLink">Доход от заказов<span><?=$completed_income?>  грн.</span></div>
					<div onclick="getChart(3)" data-fn="3" class="curPointer chartLink">Средняя стоимость<span><?=$average_cost?>  грн.</span></div>
				</div> 
				<table class="stat_driver">
					<thead>
						<tr>
							<th>ID</th>
							<th>Дата выполнения</th>
							<th>Откуда</th>
							<th>Куда</th>
							<th>Цена</th>
							<th>Статус</th>
						</tr>
					</thead>
					<tbody>
				<?php if(!empty($completed_orders)) {
					foreach($completed_orders as $order) { ?>
						<tr>
							<td><?=$order->id?></td>
							<td><?=$order->order_date?></td>
							<td><?php echo(!empty($order->from)?$order->from_adress->adress->name:'-');?></td>
							<td><?php echo(!empty($order->where)?$order->where_adress->adress->name:'-');?></td>
							<td><?=$order->price?> грн.</td>
							<td><?=$order->execut_status->name?></td>
						</tr>
					<?php }
				} ?>
					</tbody>
				</table>
				<?php $this->widget('MyLinkPager', array(
					'pages' => $pages_orders,
				)) ?>
			</div>				
			<div id="tabs-2" class="hist_balance">
			</div>
		</div>
	</div>