<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/highcharts.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/exporting.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/chartScript.js"></script>
	<h1>Статистика</h1>
	<div class="stats">
		<a href="<?php echo Yii::app()->request->baseUrl; ?>/agent/statistics/index" class="active">Статистика</a>
		<a href="<?php echo Yii::app()->request->baseUrl; ?>/agent/statistics/drivers">Водители</a>
		<div class="stats_inner">
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
				<div onclick="getChart(6)" data-fn="6" class="curPointer chartLink active">Заказов создано<span><?=$orders_count?></span></div>
				<div onclick="getChart(4)" data-fn="4" class="curPointer chartLink">Выполнено<span><?=$completed_count?></span></div>
				<div onclick="getChart(5)" data-fn="5" class="curPointer chartLink">Отменено<span><?=$cancel_count?></span></div>
				<div onclick="getChart(1)" data-fn="1" class="curPointer chartLink">Сумма<span><?=$completed_summ?> грн.</span></div>
				<div onclick="getChart(2)" data-fn="2" class="curPointer chartLink">Доход от заказов<span><?=$completed_income?>  грн.</span></div>
				<div onclick="getChart(3)" data-fn="3" class="curPointer chartLink">Средняя стоимость<span><?=$average_cost?>  грн.</span></div>
				<div>Средний маршрут<span><?=$average_distance?> м.</span></div>
			</div>
		</div>
	</div>