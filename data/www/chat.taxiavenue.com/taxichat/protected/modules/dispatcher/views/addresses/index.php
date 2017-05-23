<h1>Популярные места</h1>
<div class="search_block">
	<a href="create" class="add_button">Добавить</a>
	<div class="clear"></div>
</div>
<?php if(!empty($addresses)) { ?>
<table>
	<thead>
	<tr>
		<th>Адрес</th>
		<th>Популярное место</th>
		<th>Широта</th>
		<th>Долгота</th>
		<th></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach($addresses as $i => $adr) { ?>
		<tr class="<?php echo($i%2!=0?'active':'');?>">
			<td><?=$adr->name?></td>
			<td><?=$adr->popular_name?></td>
			<td><?=$adr->latitude?></td>
			<td><?=$adr->longitude?></td>
			<td><a href="update/id/<?=$adr->id?>" class="edit_with_bg"></a><a href="delete/id/<?=$adr->id?>" class="delete_with_bg"></a></td>
		</tr>
	<?php } ?>
	</tbody>
</table>
<?php $this->widget('CLinkPager', array(
    'pages' => $pages,
)) ?>
<?php } else { 
	echo('<p>Нет популярных мест</p>'); 
} ?>