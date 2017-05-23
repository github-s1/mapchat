<script src="//api-maps.yandex.ru/2.0/?load=package.full&lang=ru-RU" type="text/javascript"></script>

<script type="text/javascript">
ymaps.ready(init);
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
	
	<?php foreach($tariff_zones as $i => $zone){  
	if(!empty($zone->points)) { 
		$points = explode(";", $zone->points);
		unset($points[count($points) - 1]); ?>
		
	var myPolygon = new ymaps.Polygon([[
	<?php foreach($points as $p) { ?>
			[<?=$p?>],
		<?php } ?>
	]], {}, {
        editorDrawingCursor: "crosshair",
        strokeWidth: 3
    });
    myMap.geoObjects.add(myPolygon);
	<?php } 			
} ?>
}
</script>
<div class="settings_container">
	<div class="tarif_zones_inner clearfix">
		<fieldset>
			<legend>Новая тарифная зона</legend>
			<a href="javascript: void(0);" class="add_button popup" onclick="popup('<?php echo Yii::app()->request->baseUrl; ?>/admin/zones/create', 'Новая тарифная зона', 'pop_chat pop_zone'); return false;">Добавить</a>
			<div class="clear"></div>
		</fieldset>	
		<div class="list">
		<?php if(!empty($tariff_zones)){  ?>
		  <ul>
			<?php foreach($tariff_zones as $zone){  ?>
				<li>
					<?=$zone->name?>
					<a href="<?=Yii::app()->params['siteUrl']?>/admin/zones/delete/id/<?=$zone->id?>" class="delete" title="Удалить"></a>
					<a href="javascript: void(0);" class="edit popup" title="Редактировать"  onclick="popup('<?=Yii::app()->params['siteUrl']?>/admin/zones/update/id/<?=$zone->id?>', 'Тарифная зона', 'pop_chat pop_zone'); return false;"></a>
			<?php } ?>
		  </ul>
		<?php } else {
			echo('Тарифных зон нет'); 
		}	
		$this->widget('MyLinkPager', array(
			'pages' => $pages,
		)); ?>	  
		</div>
	<div id="map" class="zones_map" style="width: 639px; height: 455px;"></div>
	</div>
</div>

