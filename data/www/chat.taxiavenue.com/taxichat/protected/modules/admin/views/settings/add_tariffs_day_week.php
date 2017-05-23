<?php if(!empty($tariffs_day_week)) {  
	$list_types = array('+','%'); 
	$days = array('воскресенье', 'понедельник', 'вторник', 'среда', 'четверг', 'пятница', 'суббота'); ?>
	<?php foreach($tariffs_day_week as $s) { ?>
		<li>
			<span><?=$s->name?></span>
			<span><?=$days[$s->day_week]?></span>
			<span><?=$list_types[$s->is_percent]?></span>
			<span><?=$s->value?></span>
			<span>
				<a href="javascript: void(0);" onclick="del_tariffs_day_week('<?=$s->id?>',$(this));" class="delete"></a>
			</span>
			</li>

	<?php } ?>
<?php } ?> 