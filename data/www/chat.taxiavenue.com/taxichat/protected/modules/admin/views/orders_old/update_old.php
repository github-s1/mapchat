<script src="http://maps.googleapis.com/maps/api/js?sensor=false&amp;libraries=places"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.geocomplete.min.js"></script>
<script src="//api-maps.yandex.ru/2.0/?load=package.full&lang=ru-RU" type="text/javascript"></script>
<script type="text/javascript">
	$(document).ready(function() {
		geolocate();
	});
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
	
	function addPoint() {
		var id = new Date().getTime();
		$('#filter').append('<div class="row"><strong>Новая точка</strong><input type="text" class="geocomplete" name="point_add['+id+'][name]" autocomplete="off" value="" placeholder="Введите адресс" maxlength="255" size="60"><input class="latitude" type="hidden" value="" name="point_add['+id+'][latitude]"><input class="longitude" type="hidden" value="" name="point_add['+id+'][longitude]"><a class="delete" onclick="$(this).parent().remove(); update_route(); return false;" href="javascript: void(0);">удалить</a></div>');	
		geolocate();
		return false;
	}
	
	function geolocate() {
		$(".geocomplete").geocomplete({
			map: "",
			details: "form ",
			markerOptions: {
				draggable: true
			}
		}).bind("geocode:result", function(event, result) {
			$(this).parent().find(".latitude").val(result.geometry.location.k);
			$(this).parent().find(".longitude").val(result.geometry.location.A);
			update_route();
		});
		return false;
	}
	
	function update_route() {
		var params = {};
		$('#from').find('input').each(function(){
			params[$(this).attr("name")] = $(this).val();
		});
		$('#filter').find('input').each(function(){
			params[$(this).attr("name")] = $(this).val();
		});
		$.ajax({
			url: "<?=Yii::app()->params['siteUrl']?>/admin/orders/new_route",
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
	
	ymaps.ready(init);
	function init() {
		var myMap = new ymaps.Map("map", {
			center: [<?php if(!empty($address->latitude) && !empty($address->longitude)) { echo($address->latitude.', '.$address->longitude); } else { echo('49.9935, 36.230383000000074');} ?>],
			zoom: 11,
			behaviors: ["default", "scrollZoom"]
		});
		myMap.controls
			// Кнопка изменения масштаба.
			.add('zoomControl', { left: 5, top: 5 })
			// Список типов карты
			.add('typeSelector')
			// Стандартный набор кнопок
			.add('mapTools', { left: 35, top: 5 });
		
		// Создаем многоугольник без вершин.
		
		<?php foreach($tariff_zones as $i => $zone){  
			if(!empty($zone->points)) { 
				$points = explode(";", $zone->points);
				unset($points[count($points) - 1]); ?>
				
			var myPolygon<?=$i+1?> = new ymaps.Polygon([[
			<?php foreach($points as $p) { ?>
					[<?=$p?>],
				<?php } ?>
			]], {}, {
				editorDrawingCursor: "crosshair",
				strokeWidth: 3
			});
			
			myPolygon<?=$i+1?>.options.set('visible', false);	
			myMap.geoObjects.add(myPolygon<?=$i+1?>);
			<?php } 			
		} ?>
	<?php if(!empty($where_points) && !empty($address->name)) { ?> 
	ymaps.route([
		[<?=$address->latitude?>, <?=$address->longitude?>],
		<?php foreach($where_points as $point){  ?>
			[<?=$point->adress->latitude?>, <?=$point->adress->longitude?>],
		<?php } ?>
    ]).then(function (res) {
		myMap.geoObjects.add(res);
		var distance = Math.round(res.getLength())/1000;
		var price_route = Math.round(<?=$settings['price_kilometer']['value']?> * distance * 100)/100;
		var price = price_route;
       // myMap.geoObjects.add(route);
	   
	     var pathsObjects = ymaps.geoQuery(res.getPaths()),
		edges = [];
			
		// Переберем все сегменты и разобьем их на отрезки.
		pathsObjects.each(function (path) {
			var coordinates = path.geometry.getCoordinates();
			
			for (var i = 1, l = coordinates.length; i < l; i++) {
				edges.push({
					type: 'LineString',
					coordinates: [coordinates[i], coordinates[i - 1]]
				});
			}
		});

		var routeObjects = ymaps.geoQuery(edges)
				.add(res.getWayPoints())
				.add(res.getViaPoints())
				.setOptions('strokeWidth', 3)
				.addToMap(myMap),
			// Найдем все объекты, попадающие внутрь МКАД.
			<?php foreach($tariff_zones as $i => $zone){ ?>
			objectsInPolygon<?=$i+1?> = routeObjects.searchInside(myPolygon<?=$i+1?>);
			// Найдем объекты, пересекающие МКАД.
			boundaryPolygon<?=$i+1?> = routeObjects.searchIntersect(myPolygon<?=$i+1?>);
			if(!$.isEmptyObject(objectsInPolygon<?=$i+1?>._sl) || !$.isEmptyObject(boundaryPolygon<?=$i+1?>._sl)) {
					price += <?php echo($zone->is_percent?'price_route * ':'');?><?=$zone->value?>;
				//console.log(price); 
			}
			<?php } 
			$prom_points_count = count($where_points) - 1;
			if(($prom_points_count) > 0) { 
				if($settings['intermediate_point']['type'] == '1') { ?>
					price += price_route * <?=$prom_points_count * $settings['intermediate_point']['value'] / 100?>;
				<?php }	else { ?>
					price += <?=$prom_points_count * $settings['intermediate_point']['value']?>;
				<?php } 		
			}	?>
			$('#Orders_price').val(Math.round(price*100)/100);
			$('#Orders_distance').val(distance);
			$('#Orders_price_distance').val(price_route);
		 });		
	<?php } ?>
}	
</script>
<?php
if($id != 0)
	$this->breadcrumbs=array('Заказы'=>array('index'), 'Редактирование');
else
	$this->breadcrumbs=array('Заказы'=>array('index'), 'Новый заказ');
$this->menu=array(
	array('label'=>'Заказы', 'url'=>array('index')),
);

if($id != 0) 
	echo('<h1>Редактирование данных заказа</h1>');
else
	echo('<h1>Новый заказ</h1>');
?>

<div class="form">

	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'users-form',
		'enableAjaxValidation'=>false,
		'htmlOptions'=>array('enctype'=>'multipart/form-data'),
	)); 
	echo $form->errorSummary($order); ?>
	
	<div class="row">
		<?php echo $form->labelEx($order,'order_date'); ?>
		<?php echo $form->textField($order,'order_date', array('class'=>'date_picker')); ?>
		<?php echo $form->error($order,'order_date'); ?>
	</div>
	<div id="from">
		<div class="row">
			<label for="Addresses_name">Откуда</label>
			<?php echo $form->textField($address,'name', array('size'=>60,'maxlength'=>255, 'class'=>'geocomplete', 'placeholder'=>'Введите адресс', 'autocomplete'=>'off')); ?>
			<?php echo $form->error($address,'name'); ?>
			<input class="latitude" name="Addresses[latitude]" type="hidden" value="<?=$address->latitude?>">
			<input class="longitude" name="Addresses[longitude]" type="hidden" value="<?=$address->longitude?>">
		</div>
	</div>
	<div class="row">
		<?php echo $form->labelEx($order,'additional_info'); ?>
		<?php echo $form->textArea($order,'additional_info',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($order,'additional_info'); ?>
	</div>
	<label>Промежуточные точки</label>
	<a href="javascript: void(0);" onclick="addPoint();">Добавить точку</a>
	<div id="filter">
		<?php if(!empty($where_points)) { 
			foreach($where_points as $point){  ?>
			<div class="row">
				<input type="text" class="geocomplete" name="where_points[<?=$point->id?>][name]" autocomplete="off" value="<?=$point->adress->name?>" placeholder="Введите адресс" maxlength="255" size="60">
				<input class="latitude" type="hidden" value="<?=$point->adress->latitude?>" name="where_points[<?=$point->id?>][latitude]">
				<input class="longitude" type="hidden" value="<?=$point->adress->longitude?>" name="where_points[<?=$point->id?>][longitude]">
				<a href="javascript: void(0);" onclick="del_point('<?=$point->id?>',$(this));" class="delete" >удалить</a>
			</div>
			<?php } 
		} else { ?>
			<div class="row">
				<input type="text" class="geocomplete" name="point_add[0][name]" autocomplete="off" value="" placeholder="Введите адресс" maxlength="255" size="60">
				<input class="latitude" type="hidden" value="" name="point_add[0][latitude]">
				<input class="longitude" type="hidden" value="" name="point_add[0][longitude]">
			</div> 
		<?php } ?>
	</div>
	<div id="map_container">
		<div id="map" class="map_canvas" style="width: 600px; height: 400px;"></div>
		<div class="row">
			<?php echo $form->labelEx($order,'price'); ?>
			<?php echo $form->textField($order,'price', array('readonly' => 'readonly')); ?>
			<?php echo $form->error($order,'price'); ?>
			<?php echo $form->hiddenField($order, 'price_distance');?>
		</div>
		<div class="row">
			<?php echo $form->labelEx($order,'distance'); ?>
			<?php echo $form->textField($order,'distance', array('readonly' => 'readonly')); ?>
			<?php echo $form->error($order,'distance'); ?>
		</div>
	</div>
	<div class="row">
		<?php echo $form->labelEx($order,'id_price_class'); ?>
		<?php echo $form->radioButtonList($order, 'id_price_class',$price_class_all);?>
		<?php echo $form->error($order,'id_price_class'); ?>
	</div>
	
	<?php if(!empty($services_all)) { ?>
		<div class="row">
			<label class="required" for="OrderService">Доп. услуги</label>
			<select id="OrderService" name="OrderService[id][]" multiple>
				<?php foreach($services_all as $i => $s) { ?>
					<option value="<?=$i?>"<?php echo(in_array($i, $services_order)?' selected':'');?>><?=$s?></option>
				<?php } ?>	
			</select>
		</div>
	<?php } ?>
	
	<div class="row">
		<?php echo $form->labelEx($order,'id_customer'); ?>
		<?php echo $form->dropDownList($order, 'id_customer',$customers_all);?>
		<?php echo $form->error($order,'id_customer'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($order,'id_driver'); ?>
		<?php echo $form->dropDownList($order, 'id_driver',$drivers_all);?>
		<?php echo $form->error($order,'id_driver'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($order,'customer_note'); ?>
		<?php echo $form->textArea($order,'customer_note',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($order,'customer_note'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($order,'driver_note'); ?>
		<?php echo $form->textArea($order,'driver_note',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($order,'driver_note'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($order,'id_status'); ?>
		<?php echo $form->dropDownList($order, 'id_status',$statuses_all);?>
		<?php echo $form->error($order,'id_status'); ?>
	</div>

	<div class="row buttons">
			<?php echo CHtml::submitButton($order->isNewRecord ? 'Create' : 'Save'); ?>
	</div>
	<?php $this->endWidget(); ?>
</div><!-- form -->