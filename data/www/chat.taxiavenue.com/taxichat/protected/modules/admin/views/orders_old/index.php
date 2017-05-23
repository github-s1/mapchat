<h1>Заказы</h1>
<div class="search_block"><!--search_block-->
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'filter-form',
		'enableAjaxValidation'=>false,
		'htmlOptions'=>array('enctype'=>'multipart/form-data'),
	)); ?>
		<label for="">Водитель:<input type="text" name="filter[driver]" value="<?php echo(!empty($_GET['driver'])?$_GET['driver']:''); ?>"/></label>
		<label for="">Создан от:<input type="text" name="filter[date_from]" class="datepicker" value="<?php echo(!empty($_GET['date_from'])?$_GET['date_from']:''); ?>"/></label>
		<label for="">До:<input type="text" name="filter[date_to]" class="datepicker" value="<?php echo(!empty($_GET['date_to'])?$_GET['date_to']:''); ?>"//></label>
		<input class="search_button" type="submit" value="Искать"/>
	<?php $this->endWidget(); ?>
	<a href="<?php echo Yii::app()->request->baseUrl; ?>/admin/orders/create" class="add_button">Создать новый</a>
	<div class="clear"></div>
</div><!--search_block_end-->
<?php if(!empty($orders)) { ?>
<table>
	<thead>
	<tr>
		<th>Статус</th>
		<th>Позывной</th>
		<th>Класс</th>
		<th>Клиент</th>
		<th>Откуда</th>
		<th>Куда</th>
		<th>Цена</th>
		<th>ID заказа</th>
		<th>Дата</th>
		<th></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach($orders as $i => $or) { ?>
		<tr class="<?php echo($i%2!=0?'active':'');?>">
			<td><?=$or->status->name?></td>
			<td><?php echo(!empty($or->id_driver)?$or->driver->phone:'-');?></td>
			<td><?=$or->price_class->name?></td>
			<td><?=$or->customer->phone?></td>
			<td><?=$or->from_adress->name?></td>
			<td><?php echo(!empty($or->where)?$or->where_adress->name:'-');?></td>
			<td><?=$or->price?></td>
			<td><?=$or->id?></td>
			<td><?=$or->order_date?></td>
			<td><a href="<?php echo Yii::app()->request->baseUrl; ?>/admin/orders/update/id/<?=$or->id?>" class="edit_with_bg"></a><a href="<?php echo Yii::app()->request->baseUrl; ?>/admin/orders/delete/id/<?=$or->id?>" class="delete_with_bg"></a></td>
		</tr>
	<?php } ?>
	</tbody>
</table>
<?php $this->widget('CLinkPager', array(
    'pages' => $pages,
)) ?>
<?php } else { 
	echo('<p>Нет результатов</p>'); 
} ?>
