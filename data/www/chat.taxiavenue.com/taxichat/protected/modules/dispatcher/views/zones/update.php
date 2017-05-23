<script src="//api-maps.yandex.ru/2.0/?load=package.full&lang=ru-RU" type="text/javascript"></script>

<script type="text/javascript">
ymaps.ready(init);
var myPolygon;
function init() {
    var myMap = new ymaps.Map("map", {
        center: [49.9935, 36.230383000000074],
        zoom: 11,
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
	<?php if($id != 0 && !empty($tariff_zone->points)) { 
		$points = explode(";", $tariff_zone->points);
		unset($points[count($points) - 1]);
		foreach($points as $p) { ?>
			[<?=$p?>],
		<?php } 
	} ?>	
	]], {}, {
        // Курсор в режиме добавления новых вершин.
        editorDrawingCursor: "crosshair",
        // Максимально допустимое количество вершин.
        //editorMaxPoints: 500,
        // Цвет заливки.
		//fillColor: '#FF0000', 
        // Цвет обводки.
        //strokeColor: '#00FF00',
        // Ширина обводки.
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


function get_coord(){
	coords = '';
	for (var i = 0; i < myPolygon.geometry.getCoordinates()[0].length; i++) {
		coords += myPolygon.geometry.getCoordinates()[0][i] + ";"
	}
	$("#TariffZones_points").val(coords);
	return true;
}
</script>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'tariff_zones-form',
	'enableAjaxValidation'=>false,
	'htmlOptions'=>array('enctype'=>'multipart/form-data', 'onsubmit'=>'return  get_coord()'),
)); ?>


	<?php echo $form->errorSummary($tariff_zone); ?>
	<div class="row">
		<?php echo $form->labelEx($tariff_zone,'name'); ?>
		<?php echo $form->textField($tariff_zone,'name',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($tariff_zone,'name'); ?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($tariff_zone,'is_percent'); ?>
		<?php $list_types = array('+','%');
		echo $form->dropDownList($tariff_zone, 'is_percent',$list_types);?>
		<?php echo $form->error($tariff_zone,'is_percent'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($tariff_zone,'value'); ?>
		<?php echo $form->textField($tariff_zone,'value',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($tariff_zone,'value'); ?>
	</div>
	<?php echo $form->hiddenField($tariff_zone,'points'); ?>
	<?php echo $form->error($tariff_zone,'points'); ?>
	<div id="map" class="map_canvas" style="width: 600px; height: 400px;"></div>
	<div class="row buttons">
		<?php echo CHtml::submitButton('Сохранить'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->