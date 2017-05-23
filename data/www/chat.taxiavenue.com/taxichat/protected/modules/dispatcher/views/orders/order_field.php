<script>
$(document).ready(function() {
    $('.info').click(function () {
        var divToggle = $(this).next();
        divToggle.slideToggle(0);
        $(this).toggleClass('active');
    });
});
</script>
<tr class="info">
	<td><?=$or->status->name?></td>
	<td><?=$or->order_date?></td>
	<td><?php echo(!empty($or->id_driver)?$or->driver->phone:'-');?></td>
	<td><?=$or->price_class->name?></td>
	<td><?php echo(!empty($or->customer->phone)?$or->customer->phone:'-'); ?> </td>
	<td><?=$or->customer->phone?></td>
	<td><?=$or->from_adress->adress->name?></td>
	<td><?php if (empty($or->where_adress->adress->name)){echo '-';}else{echo $or->where_adress->adress->name;}?></td>
	<td><?=$or->price?></td>
	<td><?=$or->id?></td>
	<td><?=$or->change_date?></td>
</tr>

<tr class="detail_info">
	<td colspan="11">
		<table>
			<tbody>
				<tr>
					<td>
						<ul>
						<?php if(!empty($or->id_driver)) { ?>
							<li><span>ФИО водителя:</span> <?=($or->driver->name.' '.$or->driver->surname)?></li>
							<?php if(!empty($or->driver->id_car)) { ?>
								<li><span>Марка машины:</span> <?=$or->driver->car->marka?></li>
								<li><span>Модель машины:</span> <?=$or->driver->car->model?></li>
								<li><span>Госномер:</span> <?=$or->driver->car->number?></li>
							<?php } else { ?>
								<li><span>Марка машины:</span> нет</li>
								<li><span>Марка машины:</span> нет</li>
								<li><span>Госномер:</span> нет</li>
							<?php } ?>
						<?php } else { ?>
							<li><span>ФИО водителя:</span> нет</li>
							<li><span>Марка машины:</span> нет</li>
							<li><span>Марка машины:</span> нет</li>
							<li><span>Госномер:</span> нет</li>
						<?php } ?>
						</ul>
					</td>
					<td>
						<ul>
							<li>
								<span>Доп. Услуги:</span>
								<?php if(!empty($or->services)) { 
									foreach($or->services as $i => $s) {
										$serv = $s->service->name;
										if($i < count($or->services) - 1) {
											$serv .= ', ';
										}
										echo($serv);
									}
								} else {
									echo(' нет');
								}?>
							</li>
						</ul>
					</td>
					<td>
						<ul>
							<li><span>Примечание:</span><?php echo(!empty($or->additional_info)?' '.$or->additional_info:' нет'); ?></li>
						</ul>
					</td>
					<td>
		                <ul>
							<?php if(!empty($or->review)) { ?>
								<li><span>Оценка клиента:</span> <?=$or['review']->id_evaluation?>/5</li>
								<li><span>Отзыв:</span> <?=$or['review']->text?></li>
							<?php } else { ?>
								<li><span>Оценка клиента: нет</li>
								<li><span>Отзыв: нет</li>
							<?php } ?>	
							<br/>
						</ul>
					</td>
					<td>
						<a href="javascript: void(0);" class="edit_with_bg popup" style="display:inline-block; vertical-align:middle;" onclick="popup('<?php echo Yii::app()->request->baseUrl; ?>/admin/orders/archive_update/id/<?=$or->id?>', 'Редактировать заказ', 'pop_chat pop_zone pop_rating pop_arch'); return false;"></a> 
						<a href="javascript: void(0);" class="add_button popup" style="display:inline-block; vertical-align:middle;" onclick="popup('<?php echo Yii::app()->request->baseUrl; ?>/admin/orders/orderlog/id/<?=$or->id?>', 'История заказа', 'pop_chat pop_zone pop_rating'); return false;">История закзаза</a>
					</td>
				</tr>
			</tbody>
		</table>
	</td>
</tr>