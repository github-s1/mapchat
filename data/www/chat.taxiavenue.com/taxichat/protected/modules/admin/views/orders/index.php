<script src="http://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places"></script>
<h1>Заказы</h1>
<div class="search_block"><!--search_block-->
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'filter-form',
		'enableAjaxValidation'=>false,
		'htmlOptions'=>array('enctype'=>'multipart/form-data'),
	)); ?>
		<label for="">Водитель:<input type="text" name="filter[driver]" value="<?php echo(!empty($_GET['driver'])?$_GET['driver']:''); ?>"/></label>
		<label for="">Создан от:<input type="text" name="filter[date_from]" class="datepicker" value="<?php echo(!empty($_GET['date_from'])?$_GET['date_from']:''); ?>"/></label>
		<label for="">До:<input type="text" name="filter[date_to]" class="datepicker" value="<?php echo(!empty($_GET['date_to'])?$_GET['date_to']:''); ?>"/></label>
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
		<tr class="<?php echo($i%2!=0?'active':'');?>" id="trline_<?=$or->id?>">
			<td><?=$or->status->name?></td>
			<td><?php echo(!empty($or->id_driver)?$or->driver->phone:'-');?></td>
			<td><?=$or->price_class->name?></td>
			<td><?=$or->customer->phone?></td>
			<td><?php echo(!empty($or->from_adress)?$or->from_adress->adress->name:'-');?></td>
			<td><?php echo(!empty($or->where_adress)?$or->where_adress->adress->name:'-');?></td>
			<td><?=$or->price?></td>
			<td><?=$or->id?></td>
			<td><?=$or->order_date?></td>
			<td>
				<a class="edit_with_bg" title="Редактировать" onclick="popup('<?php echo Yii::app()->request->baseUrl; ?>/dispatcher/orders/update/id/<?=$or->id?>', 'Редактирование заказа', 'pop_chat pop_zone pop_rating'); return false;"></a>
				<?php $tpr = "if(confirm('Вы уверены?')){
								$.get('".Yii::app()->request->baseUrl."/admin/orders/delete/id/".$or->id."', function(data){
									if(data==1){
										$('#trline_".$or->id."').fadeOut();
									} else {
										alert('Не удается удалить заказ.');
									} 
								});
							 }  
						return false;"; ?>
				<a href="<?php echo Yii::app()->request->baseUrl; ?>/admin/orders/delete/id/<?=$or->id?>" onclick="<?=$tpr?>" class="delete_with_bg"></a>
			</td>
		</tr>
		
	<?php } ?>
	</tbody>
</table>
<?php $this->widget('MyLinkPager', array(
    'pages' => $pages,
)) ?>
<?php } else { 
	echo('<p>Нет результатов</p>'); 
} ?>
