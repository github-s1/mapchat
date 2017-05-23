<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.geocomplete.min.js"></script>
<script src="http://api-maps.yandex.ru/2.0/?load=package.full&lang=ru-RU" type="text/javascript"></script>
<!--<div class="settings_container tarifs" id="tabs"> -->

<div class="settings_container tarifs" id="tabs">
<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'users-form',
		'enableAjaxValidation'=>false,
		'htmlOptions'=>array('enctype'=>'multipart/form-data'),
	));
	echo $form->errorSummary($order); ?>

<div class="clearfix ed_driver inv_tab active" id="filter">
	<div class="left_ed_driver">
	 <div id="map-canvas" style="width:418px; height: 340px; "></div>

		  <fieldset class="ed_dr_2nd">
			  <legend>Маршрут</legend>
			   <div id="points">
					<label>Откуда:
						<input readonly type="text"  name="<?php echo(isset($order_points[0])?'order_points['.$order_points[0]->id.'][entrance]':'point_add[0][entrance]');?>" style="width:60px;margin-right:35px;" value="<?php echo(isset($order_points[0])?$order_points[0]->entrance:'');?>" placeholder="Подъезд">
						<input readonly type="text" class="geocomplete" name="<?php echo(isset($order_points[0])?'order_points['.$order_points[0]->id.'][name]':'point_add[0][name]');?>" autocomplete="off" value="<?php echo(isset($order_points[0])?$order_points[0]->adress->name:'');?>" placeholder="Введите адресс" style="width:120px;" maxlength="255" size="60">
						<input readonly class="latitude" name="<?php echo(isset($order_points[0])?'order_points['.$order_points[0]->id.'][latitude]':'point_add[0][latitude]');?>" type="hidden" value="<?php echo(isset($order_points[0])?$order_points[0]->adress->latitude:'');?>">
						<input readonly class="longitude" name="<?php echo(isset($order_points[0])?'order_points['.$order_points[0]->id.'][longitude]':'point_add[0][longitude]');?>" type="hidden" value="<?php echo(isset($order_points[0])?$order_points[0]->adress->longitude:'');?>">
					</label>
					<a href="#" class="findAdress">Уточнение адреса:</a>

					<?php if(isset($order_points[1])) {
						foreach($order_points as $i => $point){
							if($i != 0) { ?>
							<label>Куда:
								<input type="text" name="order_points[<?=$point->id?>][entrance]" value="<?=$point->entrance?>" style="width:60px;" placeholder="Подъезд">
								<input readonly type="text" class="geocomplete" name="order_points[<?=$point->id?>][name]" autocomplete="off" value="<?=$point->adress->name?>" placeholder="Введите адресс" style="width:120px;" maxlength="255" size="60">
								<input readonly class="latitude" type="hidden" value="<?=$point->adress->latitude?>" name="order_points[<?=$point->id?>][latitude]">
								<input readonly class="longitude" type="hidden" value="<?=$point->adress->longitude?>" name="order_points[<?=$point->id?>][longitude]">
							</label>
							<?php }
						}
					} else { ?>

						<label>Куда:
							<input readonly type="text" name="point_add[1][entrance]" style="width:60px;" placeholder="Подъезд">
							<input readonly type="text" class="geocomplete" name="point_add[1][name]" autocomplete="off" value="" placeholder="Введите адресс" style="width:120px;" maxlength="255" size="60">
							<input readonly class="latitude" type="hidden" value="" name="point_add[1][latitude]">
							<input readonly class="longitude" type="hidden" value="" name="point_add[1][longitude]">
						</label>

					<?php } ?>
				</div>
			</fieldset>
			<fieldset class="ed_dr_options">


					<?php echo $form->checkBox($order, 'is_preliminary', array('disabled' => 'disabled'));?>
					<?php echo $form->error($order,'is_preliminary'); ?>
					<label for="Orders_is_preliminary" style="width:80%">Предварительный заказ</label>


			</fieldset>
			<fieldset>
			<legend>Информация о клиенте</legend>
			 <!-- ЗАПИЛИ ПРОВЕРОЧКИ -->
			 <label><span>Имя:</span> <?=$customer->name?> </label>
			 <label><span>Телефон:</span> <?=$customer->phone?></label>
			 <label><span>Скидочная карта:</span>  </label>
			 <label><span>Доп карта:</span> </label>

			</fieldset>

			<fieldset class="disTextarea">
			<label><span>Заметки<br>водителя:</span>
				<?php echo $form->textArea($order,'driver_note',array('rows'=>6, 'cols'=>50 , 'readonly' => 'readonly')); ?>
				<?php echo $form->error($order,'driver_note'); ?>
			</label>
			</fieldset>
	</div>

	<div class="right_ed_driver">
	<fieldset class="rightArchPop">
	<label><span>Номер заказа:</span> </label>
	<!-- ЗАПИЛИ ПРОВЕРОЧКИ -->
		<?php if(!empty($driver)): ?>
		  <label><span>Позывной:</span> <span class="archIput"><?=$driver->phone?></span> </label>
		<?php else: ?> 	  
	      <label><span>Позывной:</span> <span class="archIput">Отсутствует</span></label>
		<?php endif; ?>
	

		<label>Статус:		
				<span class="archIput"><?=$status->name?></span> 
			</label>

		<div id="map_container">
					<label>Цена:
						<?php echo $form->textField($order,'price', array('readonly' => 'readonly')); ?>
						<?php echo $form->error($order,'price'); ?>
						<?php echo $form->hiddenField($order, 'price_distance', array('readonly' => 'readonly'));?>
						<?php echo $form->hiddenField($order, 'price_without_class', array('readonly' => 'readonly'));?>
					</label>
					<label>Расстояние:
						<?php echo $form->textField($order,'distance', array('readonly' => 'readonly')); ?>
						<?php echo $form->error($order,'distance'); ?>
					</label>
					</div>
					<label>Время создания:
						<!--<input type="text" id="" name="" style="width:90px;margin-right:100px;">-->
						<?php echo $form->textField($order,'order_date', array('class'=>'date_picker1','readonly' => 'readonly')); ?>
						<?php echo $form->error($order,'order_date'); ?>
					</label>

			<label>Ценовой класс:
	          <span class="archIput"><?=$price_class->name?></span> 
			</label>		 

		<label><span>Время простоев:</span> <input readonly type="text" name="Orders[down_time]" value="<?=$order->down_time?>" /> </label>
		<label><span>Стоимость простоев:</span></label>

		<label><span>Оплата бонусами:</span> <span class="archIput"><?=$order->bonuses?></span> </label>
		<label><span>Платёж:</span> </label>

		<!-- ЗАПИЛИ ПРОВЕРОЧКИ -->
		<?php if(!empty($driver)): ?>
		 <label><span>Фамилия водителя:</span> <span class="archIput"><?=$driver->surname?></span></label>
		 <label><span>Информация о машине:</span><span class="archIput"><?=$car->marka?></span></label>
		<?php else: ?> 
		  <label><span>Фамилия водителя:</span> <span class="archIput">Отсутствует</span></label>
		  <label><span>Информация о машине:</span><span class="archIput">Отсутствует</span></label>
		<?php endif; ?>
		<label><span>Номер создателя закаказа:</span> <input readonly type="text" name="Orders[id_creator]" value="<?=$order->id_creator?>" /> </label>
		<label><span>Номер закрывшего закаказ:</span>  </label>
	</fieldset>


		<fieldset class="disTextarea">
			<label><span>Заметка<br>к телефону:</span>
				<?php echo $form->textArea($order,'phone_note',array('rows'=>6, 'cols'=>50, 'readonly' => 'readonly')); ?>
				<?php echo $form->error($order,'phone_note'); ?>
			</label>
		</fieldset>

	</div>




  </div>

<?php $this->endWidget(); ?>  

</div>

<script type="text/javascript">
	function geolocate() {
		var $geocomplete = $(".geocomplete");		
		$geocomplete.each(function(){
			$(this).geocomplete().bind("geocode:result", function(event, result) {
				$(this).parent().find(".latitude").val(result.geometry.location.k);
				$(this).parent().find(".longitude").val(result.geometry.location.B);
				update_route();
			});
		});
	};

$(document).ready(function() {
	$('#filter').find('select option').click(function(){
		update_route();
	});
	geolocate();
	getPoints(<?=$order->id?>);
});

/*-------------------------------------
		Добавление точки "Куда"
*/
function addPoint() {
	var id = new Date().getTime();
	$('#points').append('<label>Куда: <a class="delete_with_bg" onclick="$(this).parent().remove(); update_route(); return false;" href="javascript: void(0);"></a><input type="text" name="point_add['+id+'][entrance]" style="width:60px;" placeholder="Подъезд"><input type="text" class="geocomplete" name="point_add['+id+'][name]" autocomplete="off" value="" placeholder="Введите адресс" style="width:120px;" maxlength="255" size="60"><input class="latitude" type="hidden" value="" name="point_add['+id+'][latitude]"><input class="longitude" type="hidden" value="" name="point_add['+id+'][longitude]"></label>');
	geolocate();
	return false;
}


/*-------------------------------------
		Обновление точек
*/
function update_route() {
	console.log("update_route");
	var params = {};
	$('#filter').find('input').each(function(){
		params[$(this).attr("name")] = $(this).val();
	});
	$('#filter').find('select').each(function(){
		params[$(this).attr("name")] = $(this).val();
	});
	$.ajax({
		url: "<?=Yii::app()->params['siteUrl']?>/admin/orders/new_route",
		type: 'post',
		data: params,
		success: function(data){
			if(data != 0){
				$('#map_container').html(data);
			}
		}
	});
}

/*-------------------------------------
		Удаление точки "Куда"
*/
function del_point(id, obj){
	$.get("<?=Yii::app()->params['siteUrl']?>/admin/orders/delete_point/id/"+id, function(data){
		if(data == 1){
			$(obj).parent().remove();
			update_route();
		} else {
			alert("Ошибка! Не удалось удалить платеж");
		}
	});
	return false;
}


 
</script>