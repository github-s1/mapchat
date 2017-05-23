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
	});
	
	function driver_view(id) {
		document.location.href = "<?=Yii::app()->params['siteUrl']?>/admin/statistics/driver_info/id/" + id;
	}

</script>

	<h1>Статистика</h1>
	<div class="stats">
		<a href="<?php echo Yii::app()->request->baseUrl; ?>/admin/statistics/index">Статистика</a>
		<a href="<?php echo Yii::app()->request->baseUrl; ?>/admin/statistics/drivers" class="active">Водители</a>
		<div class="stats_inner">
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
			<div class="stats_info clearfix">
				<section class="chartInner">
					<div id="container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
				</section>
				<div onclick="getChart(1)" class="curPointer chartLink active">Сумма<span><?=$completed_summ?> грн.</span></div>
				<div onclick="getChart(4)" class="curPointer chartLink">Выполнено<span><?=$completed_count?></span></div>
				<div onclick="getChart(2)" class="curPointer chartLink">Доход от заказов<span><?=$completed_income?>  грн.</span></div>
				<div onclick="getChart(3)" class="curPointer chartLink">Средняя стоимость<span><?=$average_cost?>  грн.</span></div>
			</div> 		  
			<table cellspacing="1" cellpadding="1">
				<thead>
					<tr>
						<th>Позывной</th>
						<th>Заказы</th>
						<th>Сумма</th>
						<th>Доход службы</th>
					</tr>
				</thead>
				<tbody>
				<?php if(!empty($drivers)) { 
					foreach($drivers as $dr) { ?>
					<tr onclick="driver_view(<?=$dr['id_user']?>);">
						<td><?=$dr['phone']?></td>
						<td><?=$dr['orders_count']?></td>
						<td><?=$dr['orders_summ']?> грн.</td>
						<td><?=$dr['orders_income']?>  грн.</td>
					</tr>
					<?php }
				} ?>
				</tbody>
			</table>
			<?php $this->widget('MyLinkPager', array(
				'pages' => $pages,
			)) ?>
		</div>
	</div>