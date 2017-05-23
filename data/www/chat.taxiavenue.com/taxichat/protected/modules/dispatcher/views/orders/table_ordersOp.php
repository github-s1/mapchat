		<div id="tabsFourOp">
		<table class="orders">
			<thead>
				<tr>
					<th>Статус</th>
					<th>На когда</th>
					<th>Позывной</th>
					<th>Класс</th>
					<th>Телефон клиента</th>
					<th>Откуда</th>
					<th>Куда</th>
					<th>Цена</th>
					<th>ID заказа</th>
					<th>Дата создания/изменения</th>
					<th></th>
					<th></th>
					<th></th>
				</tr>
			</thead>

			<tbody id="hotTable">
			<tr class="emuHeader">
				<td colspan="13">Заказы операторов</td>
			</tr>
			<?php if(!empty($orders)){ ?>
			<?php foreach($orders as $i => $or) { ?>
					<tr id="trline_<?=$or->id?>">
						<td><?=$or->status->name?></td>
						<td><?=$or->order_date?></td>
						<td><?php echo(!empty($or->id_driver)?$or->driver->phone:'-');?></td>
						<td><?=$or->price_class->name?></td>
						<td><?=$or->customer->phone?></td>
						<td><?php echo(!empty($or->from)?$or->from_adress->adress->name:'-');?></td>
						<td><?php echo(!empty($or->where)?$or->where_adress->adress->name:'-');?></td>
						<td><?=$or->price?></td>
						<td><?=$or->id?></td>
						<td><?=$or->change_date?></td>
						<td>
							<a class="edit_with_bg"  onclick="popup('<?php echo Yii::app()->request->baseUrl; ?>/dispatcher/orders/update/id/<?=$or->id?>', 'Редактироовать заказ', 'pop_chat pop_zone pop_rating'); return false;" title="Редактировать"></a>
							<?php /* $tpr = "if(confirm('Вы уверены?')){
								$.get('".Yii::app()->request->baseUrl."/admin/orders/delete/id/".$or->id."', function(data){
									if(data==1){
										$('#trline_".$or->id."').fadeOut();
									} else {
										alert('Не удается удалить заказ.');
									} 
								});
							 }
						return false;"; */ ?>
						<td>
							<a class="delete_with_bg" onclick="popup('<?php echo Yii::app()->request->baseUrl; ?>/dispatcher/orders/closeorder/id/<?=$or->id?>', 'Завершить заказ', 'pop_chat pop_zone pop_rating'); return false;" title="Завершить" ></a>
						</td>
						<td>
							<a class="map_icon mapWidget" href="javascript:void(0);" onclick="getPoints(<?=$or->id?>)" data-point="<?=$or->id?>" title="Карта"></a>
						</td>					
					</tr>
				<?php } ?>
				</tbody>
		       </table>
		       <?php $this->widget('MyLinkPager', array(
				'pages' => $pages,
			   )) ?>
				<?php }else{ ?>
					<tr>
						<td style="text-align:left;">Нет результатов</td>
	                    <td></td>
				     	<td></td>
				     	<td></td>
				     	<td></td>
				     	<td></td>
				     	<td></td>
				     	<td></td>
				     	<td></td>
				     	<td></td>
				     	<td></td>
				     	<td></td>
				     	<td></td>
					</tr>
					</tbody></table>
				<?php  } ?>
				</div>
