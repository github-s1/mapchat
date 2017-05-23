<script src="//api-maps.yandex.ru/2.0/?load=package.full&lang=ru-RU" type="text/javascript"></script>
<script type="text/javascript">
var myPolygon;
	
function get_coord(){
	coords = '';
	for (var i = 0; i < myPolygon.geometry.getCoordinates()[0].length; i++) {
		coords += myPolygon.geometry.getCoordinates()[0][i] + ";"
	}
	$("#polygons_coord").val(coords);
	return true;
}

function new_service() {
	var params = {};
	$('#new_service').find('input, select').each(function(){
		if($(this).attr("type") == "checkbox")
			params[$(this).attr("name")] = Number($(this).is(':checked'));
		else	
			params[$(this).attr("name")] = $(this).val();
	});
	$.ajax({
		url: "<?=Yii::app()->params['siteUrl']?>/admin/settings/add_service",
		type: 'post',
		data: params,
		success: function(data){
			$('#filter').html(data);
			return false;
		}
	});
	
}

function del_service(id, obj){
	$.get("<?=Yii::app()->params['siteUrl']?>/admin/settings/del_service/id/"+id, function(data){
		if(data == 1){
			$(obj).parent().parent().remove();
		} else {
			alert("Ошибка! Не удалось удалить услугу");
		}
	});
	return false;
}
</script>
<?php
$list_types = array('+','%');
?>

<h1>Базовые</h1>

<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'enableAjaxValidation'=>false,
	'htmlOptions'=>array('enctype'=>'multipart/form-data', 'onsubmit'=>'return  get_coord()'),
));  ?>
	<?php foreach ($base_settings as $i=>$item) { 
		//print_r($item); exit;
		?>
			<div class="row">
				<label for="setting<?=$item->id?>"><?=$item->descr?></label>
				<?php if($item->type != 'map') { ?>
					<input id="setting<?=$item->id?>" type="<?=$item->type?>" value="<?=$item->value?>" name="Settings[<?=$item->id?>][value]">
				<?php } else { ?>
				<script type="text/javascript">
					ymaps.ready(init);
					function init() {
						var myMap = new ymaps.Map("map", {
							center: [49.9935, 36.230383000000074],
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
						myPolygon = new ymaps.Polygon([[
						<?php if(!empty($item->value)) { 
							$points = explode(";", $item->value);
							unset($points[count($points) - 1]);
							foreach($points as $p) { ?>
								[<?=$p?>],
							<?php } 
						} ?>	
						]], {}, {
							// Курсор в режиме добавления новых вершин.
							editorDrawingCursor: "crosshair",
							strokeWidth: 3
						});
						//console.log(myPolygon.geometry.getCoordinates()[0]);
						// Добавляем многоугольник на карту.
						myMap.geoObjects.add(myPolygon);

						// В режиме добавления новых вершин меняем цвет обводки многоугольника.
						var stateMonitor = new ymaps.Monitor(myPolygon.editor.state);
						stateMonitor.add("drawing", function (newValue) {
							myPolygon.options.set("strokeColor", newValue ? '#FF0000' : '#0000FF');
						});

						// Включаем режим редактирования с возможностью добавления новых вершин.
						myPolygon.editor.startDrawing();
					}	
				</script>	
				<div id="map" class="map_canvas" style="width: 600px; height: 400px;"></div>
				<input id="polygons_coord" type="hidden" value="<?=$item->value?>" name="Settings[<?=$item->id?>][value]">
				<?php } ?>
			</div>
		<?php } ?>
	
	<div class="row buttons">
		<?php echo CHtml::submitButton('Сохранить'); ?>
	</div>

<?php $this->endWidget(); ?>
</div>

<h1>Услуги</h1>

<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'enableAjaxValidation'=>false,
	'htmlOptions'=>array('enctype'=>'multipart/form-data'),
));  ?>
	<?php foreach ($preliminary_settings as $i=>$item) { ?>
			<div class="row">
				<label for="setting<?=$item->id?>"><?=$item->descr?></label>
				Тип<select name="Settings[<?=$item->id?>][type]">
					<option <?php echo($item->type == 0?' selected':''); ?> value="0">+</option>
					<option value="1" <?php echo($item->type == 1?' selected':''); ?>>%</option>
				</select>
				Значение<input id="setting<?=$item->id?>" type="text" value="<?=$item->value?>" name="Settings[<?=$item->id?>][value]">
			</div>
		<?php } ?>
	
	<div class="row buttons">
		<?php echo CHtml::submitButton('Сохранить'); ?>
	</div>

<?php $this->endWidget(); ?>
</div>

<h1>Доп. услуги</h1>

<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'enableAjaxValidation'=>false,
	'htmlOptions'=>array('enctype'=>'multipart/form-data'),
)); ?>
<div class="row" id="new_service">
	<label>Новая доп. услуга</label>
	Название
	<?php echo $form->textField($new_service,'name'); ?>
	Тип
	<?php echo $form->dropDownList($new_service, 'is_percent',$list_types);?>
	Значение
	<?php echo $form->textField($new_service,'value'); ?>
	Услуга водителя
	<?php echo $form->checkBox($new_service,'is_driver'); ?>
	<a href="javascript: void(0);" onclick="new_service();" class="new_service" >Добавить</a>
</div>
<div id="filter">
	<?php if(!empty($services)){  ?>
		<table cellspacing="0" cellpadding="0">
			<tbody>
				<?php foreach($services as $s){  ?>
					<tr>
						<td><?=$s->name?></td>
						<td><?=$list_types[$s->is_percent]?></td>
						<td><?=$s->value?></td>
						<td>Услуга водителя
						<input type="checkbox" <?php echo($s->is_driver == 1?' checked':''); ?> value="<?=$s->is_driver?>" name="Services_all[<?=$s->id?>][is_driver]">
						<input type="hidden" value="<?=$s->id?>" name="Services_all[<?=$s->id?>][id]">
						</td>
						<td><a href="javascript: void(0);" onclick="del_service('<?=$s->id?>',$(this));" class="delete" >удалить</a></td>
					</tr>
				<?php } ?>
			</tbody>	
		</table>
	<?php } ?>
</div>
<div class="row buttons">
		<?php echo CHtml::submitButton('Сохранить'); ?>
	</div>
<?php $this->endWidget(); ?>
</div>