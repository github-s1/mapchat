
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
	
	<div class="clear"></div>
</div><!--search_block_end-->
<?php if(!empty($drivers)) { ?>
<table>
	<thead>
	<tr>
		<th>Статус</th>
		<th>Водитель</th>
		<th>Телефон</th>
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
		<tr class="<?php echo($i%2!=0?'active':''); echo($dr->moderation == 2?' no_moderate':''); echo($dr->moderation == 3?' no_moderate_edit':''); echo($dr->moderation == 0?' banned':'');?>">
			<td><?=$dr->status->name?></td>
			<td><?=$dr->user->surname.' '.$dr->user->name?></td>
			<td><?=$dr->user->phone?></td>
			<td><?=$dr->user->car->marka?></td>
			<td><?=$dr->user->car->model?></td>
			<td><?=$dr->user->car->color?></td>
			<td><?=$dr->user->car->number?></td>
			<td><?=$dr->user->price_class->name?></td>
			<td><?=$dr->user->rating?></td>
			<td><?=$dr->user->id?></td>
			<td>
			<?php if($dr->moderation == 3 ) { ?>
				<a href="<?php echo Yii::app()->request->baseUrl; ?>/dispatcher/drivers/edit_moderation/id/<?=$dr->user->id?>" class="edit_with_bg"></a>
			<?php } else  { ?>
				<a href="<?php echo Yii::app()->request->baseUrl; ?>/dispatcher/drivers/update/id/<?=$dr->user->id?>" class="edit_with_bg"></a>
			<?php } ?>
				<a href="<?php echo Yii::app()->request->baseUrl; ?>/dispatcher/drivers/delete/id/<?=$dr->user->id?>" class="delete_with_bg"></a>
				<a href="<?php echo Yii::app()->request->baseUrl; ?>/dispatcher/customers/AddToBlackList/id/<?=$dr->id_user?>" class="black-list"></a>
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
