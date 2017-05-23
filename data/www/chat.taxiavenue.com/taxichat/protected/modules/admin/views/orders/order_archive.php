 <script src="http://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places"></script>
 <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/getPoints.js"></script> 
<h1>Архив заказов</h1>
<div class="search_block"><!--search_block-->
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'filter-form',
		'enableAjaxValidation'=>false,
		'htmlOptions'=>array('enctype'=>'multipart/form-data'),
	)); ?>
		<label for="">Водитель:<input type="text" name="filter[driver]" value="<?php echo(!empty($_GET['driver'])?$_GET['driver']:''); ?>"/></label>
		<label for="">Создан от:<input type="text" name="filter[date_from]" class="datepicker" value="<?php echo(!empty($_GET['date_from'])?$_GET['date_from']:''); ?>"/></label>
		<label for="">До:<input type="text" name="filter[date_to]" class="datepicker" value="<?php echo(!empty($_GET['date_to'])?$_GET['date_to']:''); ?>"//></label>
		<input class="search_button" type="submit" value="Искать"/>
	<?php $this->endWidget(); ?>
	<div class="clear"></div>
</div><!--search_block_end-->
<?php if(!empty($orders)) { ?>
<table class="arch_orders">
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
	</tr>
	</thead>
	<tbody>
	<?php foreach($orders as $i => $or) { ?>
		<tr class="info">
			<td><?=$or->execut_status->name?></td>
			<td><?php echo(!empty($or->id_driver)?$or->driver->phone:'-');?></td>
			<td><?=$or->price_class->name?></td>
			<td><?=$or->customer->phone?></td>
			<td><?php echo(!empty($or->from)?$or->from_adress->adress->name:'-');?></td>
		    <?php if (!empty($or->where_adress->adress->name)): ?>
			  <td><?=$or->where_adress->adress->name?></td>
			<?php else: ?> 
			  <td> - </td>
			<?php endif; ?>
			<td><?=$or->price?></td>
			<td><?=$or->id?></td>
			<td><?=$or->order_date?></td>
		</tr>
		<tr class="detail_info">

			<td colspan="9">
				<table>
					<tbody>
						<tr>
							<td style="width: 20%;">
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
							<td style="width: 20%;">
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
							<td style="width: 20%;">
								<ul>
									<li><span>Примечание:</span><?php echo(!empty($or->additional_info)?' '.$or->additional_info:' нет'); ?></li>
								</ul>
							</td>
							<td style="width: 20%;">
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
							<td style="width: 20%;">
								<a href="javascript: void(0);" class="edit_with_bg popup" style="display:inline-block; vertical-align:middle;" onclick="popup('<?php echo Yii::app()->request->baseUrl; ?>/admin/orders/archive_update/id/<?=$or->id?>', 'Редактировать заказ', 'pop_chat pop_zone pop_rating pop_arch'); return false;"></a> 
								<a href="javascript: void(0);" class="add_button popup" style="display:inline-block; vertical-align:middle;" onclick="popup('<?php echo Yii::app()->request->baseUrl; ?>/admin/orders/orderlog/id/<?=$or->id?>', 'История заказа', 'pop_chat pop_zone pop_rating'); return false;">История заказа</a>
							</td>
						</tr>
					</tbody>
				</table>
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