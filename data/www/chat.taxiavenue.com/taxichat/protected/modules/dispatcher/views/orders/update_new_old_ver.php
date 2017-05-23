<script src="http://maps.googleapis.com/maps/api/js?sensor=false&amp;libraries=places"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.geocomplete.min.js"></script>
<script src="http://api-maps.yandex.ru/2.0/?load=package.full&lang=ru-RU" type="text/javascript"></script>
<div class="settings_container tarifs" id="tabs">
<script type="text/javascript">
/*
	$(document).ready(function() {
		$('#filter').find('select option').click(function(){
		  update_route();
		});
		geolocate();
	});
	function del_point(id, obj){
		$.get("<?=Yii::app()->params['siteUrl']?>/dispatcher/orders/delete_point/id/"+id, function(data){
			if(data == 1){
				$(obj).parent().remove();
				update_route();
			} else {
				alert("Ошибка! Не удалось удалить платеж");
			}
		});
		return false;
	}

	function addPoint() {
		var id = new Date().getTime();
		$('#points').append('<label>Куда: <a class="delete_with_bg" onclick="$(this).parent().remove(); update_route(); return false;" href="javascript: void(0);"></a><input type="text" name="point_add['+id+'][entrance]" style="width:60px;" placeholder="Подъезд"><input type="text" class="geocomplete" name="point_add['+id+'][name]" autocomplete="off" value="" placeholder="Введите адресс" style="width:120px;" maxlength="255" size="60"><input class="latitude" type="hidden" value="" name="point_add['+id+'][latitude]"><input class="longitude" type="hidden" value="" name="point_add['+id+'][longitude]"></label>');
		geolocate();
		return false;
	}

	function geolocate() {
		var $geocomplete = $(".geocomplete");
		console.log($geocomplete);

		$geocomplete.each(function(){
			$(this).geocomplete().bind("geocode:result", function(event, result) {
				$(this).parent().find(".latitude").val(result.geometry.location.k);
				$(this).parent().find(".longitude").val(result.geometry.location.B);
				update_route();
			});
		});

		$geocomplete.geocomplete({
			map: "",
			details: "#users-form",
			markerOptions: {
				draggable: true
			}
		}).bind("geocode:result", function(event, result) {
			$(this).parent().find(".latitude").val(result.geometry.location.k);
			$(this).parent().find(".longitude").val(result.geometry.location.B);
			update_route();
		});

		return false;
	}

	function update_route() {
		var params = {};
		$('#filter').find('input').each(function(){
			params[$(this).attr("name")] = $(this).val();
		});
		$('#filter').find('select').each(function(){
			params[$(this).attr("name")] = $(this).val();
		});
		$.ajax({
			url: "<?php //echo Yii::app()->params['siteUrl']?>/dispatcher/orders/new_route",
			type: 'post',
			data: params,
			success: function(data){
				if(data != 0)
					$('#map_container').html(data);
				return false;
			}
		});
		return false;
	}
*/
</script>
 <?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'users-form',
		'enableAjaxValidation'=>false,
		'htmlOptions'=>array('enctype'=>'multipart/form-data'),
	));
	echo $form->errorSummary($order); ?>
	<div class="clearfix ed_driver inv_tab active" id="filter">		
		<div class="left_ed_driver">
			<fieldset>
				<label>Заказчик*:
					<?php echo $form->dropDownList($order, 'id_customer',$customers_all);?>
					<?php echo $form->error($order,'id_customer'); ?>
				  
				</label>
			</fieldset>
		

			<fieldset class="ed_dr_2nd">
			  <legend>Маршрут</legend>
			  <div id="points">
			  <label>Откуда:
					<!--<input type="text" value="" maxlength="8" id="" name="" placeholder="подъезд" style="width:60px;margin-right:35px;">
					<input type="text" value="" maxlength="8" id="" name="" placeholder="Введите адресс" style="width:120px;">-->
			<input type="text" name="<?php echo(isset($order_points[0])?'order_points['.$order_points[0]->id.'][entrance]':'point_add[0][entrance]');?>" style="width:60px;margin-right:35px;" value="<?php echo(isset($order_points[0])?$order_points[0]->entrance:'');?>" placeholder="Подъезд">
			<input type="text" class="geocomplete" name="<?php echo(isset($order_points[0])?'order_points['.$order_points[0]->id.'][name]':'point_add[0][name]');?>" autocomplete="off" value="<?php echo(isset($order_points[0])?$order_points[0]->adress->name:'');?>" placeholder="Введите адресс" style="width:120px;" maxlength="255" size="60">
			<input class="latitude" name="<?php echo(isset($order_points[0])?'order_points['.$order_points[0]->id.'][latitude]':'point_add[0][latitude]');?>" type="hidden" value="<?php echo(isset($order_points[0])?$order_points[0]->adress->latitude:'');?>">
			<input class="longitude" name="<?php echo(isset($order_points[0])?'order_points['.$order_points[0]->id.'][longitude]':'point_add[0][longitude]');?>" type="hidden" value="<?php echo(isset($order_points[0])?$order_points[0]->adress->longitude:'');?>">
			</label>
			<a href="#" class="findAdress">Уточнение адресса:</a>
			
			<?php if(isset($order_points[1])) {
			foreach($order_points as $i => $point){
					 if($i != 0) { ?>
							 <label>Куда:
							 <a href="javascript: void(0);" onclick="del_point('<?=$point->id?>',$(this));" class="delete_with_bg" ></a>
							 <input type="text" name="order_points[<?=$point->id?>][entrance]" value="<?=$point->entrance?>" style="width:60px;" placeholder="Подъезд">
							 <input type="text" class="geocomplete" name="order_points[<?=$point->id?>][name]" autocomplete="off" value="<?=$point->adress->name?>" placeholder="Введите адресс" style="width:120px;" maxlength="255" size="60">
							 <input class="latitude" type="hidden" value="<?=$point->adress->latitude?>" name="order_points[<?=$point->id?>][latitude]">
							 <input class="longitude" type="hidden" value="<?=$point->adress->longitude?>" name="order_points[<?=$point->id?>][longitude]">
						   </label>
						 <?php }
						 }
					 } else { ?>
						 
						 <label>Куда:
						 <a href="javascript: void(0);" onclick="del_point($(this));" class="delete_with_bg" ></a>
							 <input type="text" name="point_add[1][entrance]" style="width:60px;" placeholder="Подъезд">
							 <input type="text" class="geocomplete" name="point_add[1][name]" autocomplete="off" value="" placeholder="Введите адресс" style="width:120px;" maxlength="255" size="60">
							 <input class="latitude" type="hidden" value="" name="point_add[1][latitude]">
							 <input class="longitude" type="hidden" value="" name="point_add[1][longitude]">
							 </label>
						
					 <?php } ?>
			</div>
				<a href="javascript: void(0);" class="addPoint" onclick="addPoint();">Добавить точку</a>
			</fieldset>
			
			<fieldset class="ed_dr_options">

		  
					<?php echo $form->checkBox($order, 'is_preliminary');?>
					<?php echo $form->error($order,'is_preliminary'); ?>
					<label for="Orders_is_preliminary" style="width:80%">Предварительный заказ</label>


			</fieldset>
			<fieldset>
			<div id="map_container">
				<legend>Расчет стоимости:</legend>
					<label>Цена:
						<?php echo $form->textField($order,'price', array('readonly' => 'readonly', 'style'=> 'width:90px;margin-right:100px;')); ?>
						<?php echo $form->error($order,'price'); ?>
						<?php echo $form->hiddenField($order, 'price_distance');?>
						<?php echo $form->hiddenField($order, 'price_without_class');?>
					</label>
					<label>Расстояние:
						<?php echo $form->textField($order,'distance', array('readonly' => 'readonly', 'style' => 'width:90px;margin-right:100px;')); ?>
						<?php echo $form->error($order,'distance'); ?>
					</label>
					</div>
					<label>Время создания:
						<!--<input type="text" id="" name="" style="width:90px;margin-right:100px;">-->
						<?php echo $form->textField($order,'order_date', array('class'=>'date_picker1', 'style' => 'width:90px;margin-right:100px;')); ?>
						<?php echo $form->error($order,'order_date'); ?>
					</label>
			
			</fieldset>
	  </div>
	  <div class="right_ed_driver">
	  <fieldset>
			<label>Ценовой класс:
				<?php echo $form->dropDownList($order, 'id_price_class',$price_class_all);?>
				<?php echo $form->error($order,'id_price_class'); ?>
			</label>		 
		</fieldset>
		
		<fieldset class="ed_dr_options" class="required" for="OrderService">
			<legend>Дополнительные услуги</legend>
		   <?php if(!empty($services_all)) { ?>
					<?php foreach($services_all as $i => $s) { ?>
						<input type="checkbox"  id="service<?=$i?>" name="OrderService[id][<?=$i?>]" <?php echo(in_array($i, $services_order)?' checked=""':'');?>></input>
					 	<label for="service<?=$i?>"><?=$s?></label>
					<?php } ?>
		   <?php } ?>

				<!--
				
				<input type="checkbox" id="service2" name="">
				<label for="service2">Животные</label>
				
				<input type="checkbox" id="service3" name="">
				<label for="service3">Курящий пасажир</label>
				
				<input type="checkbox" id="service4" name="">
				<label for="service4">Трезвый водитель</label>
				
				<input type="checkbox" id="service5" name="">
				<label for="service5">Буксировка</label>
				
				<input type="checkbox" id="service6" name="">
				<label for="service6">Некурящий водитель</label>
				-->

		</fieldset>

		<fieldset class="disTextarea">
			<label><span>Заметки<br>водителя:</span>
				<?php echo $form->textArea($order,'driver_note',array('rows'=>6, 'cols'=>50)); ?>
				<?php echo $form->error($order,'driver_note'); ?>
			</label>
			<label><span>Заметка<br>оператору:</span>
				<?php echo $form->textArea($order,'operator_note',array('rows'=>6, 'cols'=>50)); ?>
				<?php echo $form->error($order,'operator_note'); ?>
			</label>
			<label><span>Заметка<br>к телефону:</span>
				<?php echo $form->textArea($order,'phone_note',array('rows'=>6, 'cols'=>50)); ?>
				<?php echo $form->error($order,'phone_note'); ?>
			</label>
		</fieldset>

	  </div>
			</div>
	<div class="s_c">
		<a onclick="closePopup()" class="pop_cancel" href="javascript: void(0);">Отмена</a>
		<!--<a href="javascript: void(0);" class="pop_push" id="submit_button">Сохранить</a> -->
			  
			<?php echo CHtml::submitButton("Сохранить", array('class' => 'pop_push', 'id' => 'submit_button')); ?>

				</div>
	<?php $this->endWidget(); ?>  
</div>
<script type="text/javascript">

function loadScript() {
	var script = document.createElement('script');
	script.type = 'text/javascript';
	script.src = 'https://maps.googleapis.com/maps/api/js?v=3.exp&' + 'callback=initialize';
	document.body.appendChild(script);
}

window.onload = loadScript;

 $(document).ready(function() {

	 $( ".date_picker1" ).datetimepicker({ 	
		dateFormat: 'yy-mm-dd',
		timeOnlyTitle: 'Выберите время',
		timeText: 'Время',
		hourText: 'Часы',
		minuteText: 'Минуты',
		secondText: 'Секунды',
		currentText: 'Сейчас',
		showOn: "button",
		buttonImage: "/img/calendar.png",
		buttonImageOnly: true,
		closeText: 'Закрыть'
	});

	$( ".datepicker" ).datepicker({
		showOn: "button",
		buttonImage: "/img/calendar.png",
		buttonImageOnly: true
	});
});
</script>
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

		$geocomplete.geocomplete({
			map: "",
			details: "#users-form",
			markerOptions: {
				draggable: true
			}
		}).bind("geocode:result", function(event, result) {
			$(this).parent().find(".latitude").val(result.geometry.location.k);
			$(this).parent().find(".longitude").val(result.geometry.location.B);
			update_route();
		});

		return false;
	};

$(document).ready(function() {
    $('#filter').find('select option').click(function(){
        update_route();
    });
//    geolocate();
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
    var params = {};
    $('#filter').find('input').each(function(){
        params[$(this).attr("name")] = $(this).val();
    });
    $('#filter').find('select').each(function(){
        params[$(this).attr("name")] = $(this).val();
    });
    $.ajax({
        url: "<?=Yii::app()->params['siteUrl']?>/dispatcher/orders/new_route",
        type: 'post',
        data: params,
        success: function(data){
                if(data != 0)
                        $('#map_container').html(data);
                return false;
        }
    });
    return false;
}

/*-------------------------------------
		Удаление точки "Куда"
*/
function del_point(id, obj){
    $.get("<?=Yii::app()->params['siteUrl']?>/dispatcher/orders/delete_point/id/"+id, function(data){
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