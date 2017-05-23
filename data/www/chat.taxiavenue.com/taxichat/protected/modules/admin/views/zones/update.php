<script src="//api-maps.yandex.ru/2.0/?load=package.full&lang=ru-RU" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.form.js"></script>
<script type="text/javascript">
$(document).ready(function() { 
    var options = { 
        target:        '#popup_content',   // target element(s) to be updated with server response 
    }; 
	$('#ajax-form').ajaxForm(options);
}); 

ymaps.ready(init);
var myPolygon;
function init() {
    var myMap = new ymaps.Map("map1", {
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
	//	unset($points[count($points) - 1]);
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
	var coords = '';
	var length = myPolygon.geometry.getCoordinates()[0].length;
	for (var i = 0; i < length; i++) {
		coords += myPolygon.geometry.getCoordinates()[0][i];
		if(i < (length - 1)) {
			coords += ";";
		}
	}
	$("#TariffZones_points").val(coords);
	return true;
}


</script>
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'ajax-form',
	'enableAjaxValidation'=>false,
	'htmlOptions'=>array('enctype'=>'multipart/form-data', 'onsubmit'=>'return  get_coord()'),
)); ?>
<div class="chat_inner pop_zone">
	<?php if(Yii::app()->user->hasFlash('success')){ ?>
			<div class="flash_success">
				<p><?=Yii::app()->user->getFlash('success')?></p>
			</div>	
		<?php } ?>
		<?php echo $form->errorSummary($tariff_zone); ?>
	  <label>Имя:
		<?php echo $form->textField($tariff_zone,'name'); ?>
	  </label>
	  <label>Тип:
		<?php $list_types = array('+','%');
		echo $form->dropDownList($tariff_zone, 'is_percent',$list_types);?>
	  </label>
	  <label>Значение:
		<?php echo $form->textField($tariff_zone,'value'); ?>
	  </label>
	  <?php echo $form->hiddenField($tariff_zone,'points'); ?>

	<div id="map1" class="pop_zone_map" style="width: 603px; height: 404px;"></div>
</div>
<div class="s_c">
	<a href="javascript: void(0);" class="pop_cancel" onclick="closePopup()">Отмена</a>
	<?php echo CHtml::submitButton('Сохранить', array(
	   'class'=>'pop_push',
	)); ?>
</div>
<?php $this->endWidget();  ?>
