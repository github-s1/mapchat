
	<?php if(!empty($orders)) { ?>
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
					<th>Комиссия</th>
					<th></th>
					<th></th>
					<th></th>
				</tr>
			</thead>

			<tbody id="hotTable">
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
					        <select id="select_<?=$or->id?>" onchange="setCommission(<?=$or->id?>)">
					        <?php for($i=10;$i<=20;$i++){ ?>
					        <?php if ($i != $or->commission): ?>
                              <option><?=$i?></option>
                            <?php else: ?>
                              <option selected><?=$i?></option>
                            <?php endif; ?>
                              <?php } ?>
                             </select>
					    </td> 
						<td>
							<a class="edit_with_bg" href="<?php echo Yii::app()->request->baseUrl; ?>/admin/orders/update/id/<?=$or->id?>"></a>
						</td>
						<td>
							<?php $tpr = "if(confirm('Вы уверены?')){
								$.get('".Yii::app()->request->baseUrl."/admin/orders/delete/id/".$or->id."', function(data){
									if(data==1){
										$('#trline_".$or->id."').fadeOut();
									} else {
										alert('Не удается удалить заказ.');
									} 
								});
							 }  
						return false;"; ?>
							<a class="delete_with_bg" href="<?php echo Yii::app()->request->baseUrl; ?>/admin/orders/delete/id/<?=$or->id?>" onclick="<?=$tpr?>"></a>
						</td>
						
						<td>
							<a href="javascript:void(0);" onclick="getPoints(<?=$or->id?>)">Карта</a>
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

	<script> 
       function setCommission(id)
       {
        var select = document.getElementById("select_" + id);
        
        params = {
                   order_id : id,
                   commission : select.value
                 }
                 $.ajax({
                  type: "POST",
                  data: params,
                  url: "<?=Yii::app()->params['siteUrl']?>/agent/settings/setCommission/",
                    success: function(data){
                    	if (data!=0)
                    	{
                           alert("Комиссия изменена");
                    	}
                    }
                });
       }
	</script>