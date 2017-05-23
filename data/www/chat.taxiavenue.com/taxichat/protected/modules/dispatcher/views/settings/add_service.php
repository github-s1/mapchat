<script type="text/javascript">
</script>
<?php if(!empty($services)){  
	$list_types = array('+','%'); ?>
	<table cellspacing="0" cellpadding="0">
		<tbody>
			<?php foreach($services as $s){  ?>
				<tr>
					<td><?=$s->name?></td>
					<td><?=$list_types[$s->is_percent]?></td>
					<td><?=$s->value?></td>
					<td>Услуга водителя
						<input type="checkbox" <?php echo($s->is_driver == 1?' checked':''); ?> value="<?=$s->is_driver?>" name="Services_all[<?=$s->id?>][is_driver]">
						<input type="hidden" value="<?=$s->id?>" name="Services_all[<?=$s->id?>][id]">
					</td>
					<td><a href="javascript: void(0);" onclick="del_service('<?=$s->id?>',$(this));" class="delete" >удалить</a></td>
				</tr>
			<?php } ?>
		</tbody>	
	</table>
<?php } ?>
