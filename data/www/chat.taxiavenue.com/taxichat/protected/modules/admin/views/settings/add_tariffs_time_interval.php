<?php if(!empty($tariffs_time_interval)) {  
	$list_types = array('+','%'); ?>
	<?php foreach($tariffs_time_interval as $s) { ?>
		<li>
			<span><?=$s->name?></span>
			<span><?=$s->from?></span>
			<span><?=$s->before?></span>
			<span><?=$list_types[$s->is_percent]?></span>
			<span><?=$s->value?></span>
			<span>
				<a href="javascript: void(0);" onclick="del_tariffs_time_interval('<?=$s->id?>',$(this));" class="delete"></a>
			</span>
			</li>

	<?php } ?>
<?php } ?> 