
<h1>База водителей</h1>
<div class="search_block"><!--search_block-->
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'filter-form',
		'enableAjaxValidation'=>false,
		'htmlOptions'=>array('enctype'=>'multipart/form-data'),
	)); ?>
		<label for="">Строка поиска:<input type="text" size="50"  name="filter[driver]" value="<?php echo(!empty($_GET['driver'])?$_GET['driver']:''); ?>"/></label>
		<input class="search_button" type="submit" value="Искать"/>
	<?php $this->endWidget(); ?>
	<a href="javascript: void(0);" class="add_button popup" onclick="popup('<?php echo Yii::app()->request->baseUrl; ?>/agent/drivers/create', 'Новый водитель', 'pop_chat pop_zone pop_rating'); return false;">Создать новый</a>
	<div class="clear"></div>
</div><!--search_block_end-->
<?php if(!empty($drivers)) { ?>
<table>
	<thead>
	<tr>
		<th>Статус</th>
		<th>Водитель</th>
		<th>Машина</th>
		<th>Марка</th>
		<th>Цвет</th>
		<th>Номер</th>
		<th>Класс</th>
		<th>Рейтинг</th>
		<th>ID</th>
		<th></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach($drivers as $i => $dr) { ?>
		<tr class="<?php echo($dr->moderation == 2?' new':($dr->moderation == 3?' modering':($dr->moderation == 0?' new':'')));?>" id="trline_<?=$dr->user->id?>">
			<td><?php echo($dr->moderation == 2?'Новый':($dr->moderation == 3?'Модерация':($dr->moderation == 0?'Забанен':$dr->status->name)));?></td>
			<td><?=$dr->user->surname.' '.$dr->user->name?></td>
			<td><?=$dr->user->car->marka?></td>
			<td><?=$dr->user->car->model?></td>
			<td><?=$dr->user->car->color?></td>
			<td><?=$dr->user->car->number?></td>
			<td><?=$dr->user->price_class->name?></td>
			<td><?=$dr->user->rating?></td>
			<td><?=$dr->user->id?></td>
			<td>
				<a href="javascript: void(0);" class="edit_with_bg popup" title="Редактировать" onclick="popup('<?php echo Yii::app()->request->baseUrl; ?>/agent/drivers/update/id/<?=$dr->user->id?>', 'Водители', 'pop_chat pop_zone pop_rating'); return false;"></a>
				<?php $tpr = "if(confirm('Вы уверены?')){
								$.get('".Yii::app()->request->baseUrl."/agent/drivers/delete/id/".$dr->user->id."', function(data){
									if(data==1){
										$('#trline_".$dr->user->id."').fadeOut();
									} else {
										alert('Не удается удалить водителя.');
									} 
								});
							 } 
						return false;"; ?>
				<a href="<?php echo Yii::app()->request->baseUrl; ?>/agent/drivers/delete/id/<?=$dr->user->id?>" onclick="<?=$tpr?>" class="delete_with_bg" title="Удалить"></a>
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
