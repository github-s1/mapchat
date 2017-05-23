<script src="https://maps.googleapis.com/maps/api/js?v=3.exp"></script>


<h1>Архив заказов</h1>
<div class="search_block"><!--search_block-->
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'filter-form',
		'enableAjaxValidation'=>false,
		'htmlOptions'=>array('enctype'=>'multipart/form-data'),
	)); ?>
	  <label for="">Заказ:<input type="text" name="filter[order]" value="<?php echo(!empty($_GET['order'])?$_GET['order']:''); ?>"/></label>
	    <label for="">Клиент:<input type="text" name="filter[client]" value="<?php echo(!empty($_GET['client'])?$_GET['client']:''); ?>"/></label>
		<label for="">Водитель:<input type="text" name="filter[driver]" value="<?php echo(!empty($_GET['driver'])?$_GET['driver']:''); ?>"/></label>
		<label for="">Создан от:<input type="text" name="filter[date_from]" class="datepicker" value="<?php echo(!empty($_GET['date_from'])?$_GET['date_from']:''); ?>"/></label>
		<label for="">До:<input type="text" name="filter[date_to]" class="datepicker" value="<?php echo(!empty($_GET['date_to'])?$_GET['date_to']:''); ?>"//></label>
		<input class="search_button" type="submit" value="Искать"/>
	<?php $this->endWidget(); ?>
	<a href="<?php echo Yii::app()->request->baseUrl; ?>/dispatcher/orders/create" class="add_button">Создать новый</a>
	<div class="clear"></div>
	<div class="clear"></div>
</div><!--search_block_end-->

<table>
	<thead>
	<tr>
		<th>Статус</th>
		<th>На когда</th>
		<th>Позывной</th>
		<th>Класс</th>
		<th>Клиент</th>
		<th>Телефон клиента</th>
		<th>Откуда</th>
		<th>Куда</th>
		<th>Цена</th>
		<th>ID</th>
		<th>Дата</th>
	</tr>
  </thead>
	<tbody>
	<?php foreach($orders as $i => $or) { ?>
		<?php if($or->execution_status == 3):
		   	include('order_field.php');
		  endif;?>
	<?php } ?>
	</tbody>
</table>

<?php //} else { echo('<p>Нет результатов</p>'); } ?>


<div id="map-canvas" style="width:600px; height:500px;margin-top: 20px;"></div>