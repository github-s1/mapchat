<h1>Пользователи</h1>
<div class="search_block"><!--search_block-->
	<a href="<?php echo Yii::app()->request->baseUrl; ?>/admin/users/create" class="add_button">Добавить</a>
	<div class="clear"></div>
</div><!--search_block_end-->

<?php if(!empty($users)) { ?>
<table>
	<thead>
	<tr>
		<th>Ник</th>
		<th>Тип</th>
		<th>Телефон</th>
		<th>Email</th>
		<th></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach($users as $i => $cs) { ?>
		<tr class="<?php echo($i%2!=0?'active':'');?>">
			<td><?=$cs->nickname?></td>
			<td><?=$cs->type->name?></td>
			<td><?=$cs->phone?></td>
			<td><?=$cs->email?></td>
			<td>
			<?php if($cs->id_type == 4) { ?>	
				<a href="<?php echo Yii::app()->request->baseUrl; ?>/admin/users/update/id/<?=$cs->id?>" class="edit_with_bg"></a>
			<?php } ?>
				<a href="<?php echo Yii::app()->request->baseUrl; ?>/admin/users/delete/id/<?=$cs->id?>" class="delete_with_bg"></a>
			</td>
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