<?php if(!empty($services)){  
	$list_types = array('+','%'); ?>
	<?php foreach($services as $s) { ?>
		<li>
			<span><?=$s->name?></span>
			<span><?=$list_types[$s->is_percent]?></span>
			<span><?=$s->value?></span>
			<span>
				<input type="checkbox" <?php echo($s->is_driver == 1?' checked':''); ?> value="<?=$s->is_driver?>" name="Services_all[<?=$s->id?>][is_driver]" id="service<?=$s->id?>">
				<label for="service<?=$s->id?>">Услуга водителя</label>
			</span>
			<span>
				<a href="javascript: void(0);" onclick="del_service('<?=$s->id?>',$(this));" class="delete"></a>
			</span>
			</li>

	<?php } ?>
<?php } ?>
