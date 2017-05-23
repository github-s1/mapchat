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
<h1>Тарифные зоны</h1>
<a href="create">Добавить</a>
<div id="filter">
	<?php if(!empty($tariff_zones)){  ?>
		<table cellspacing="0" cellpadding="0">
			<tbody>
				<?php foreach($tariff_zones as $zone){  ?>
					<tr>
						<td><?=$zone->name?></td>
						<td><a href="<?=Yii::app()->params['siteUrl']?>/admin/zones/update/id/<?=$zone->id?>" class="update" >редактировать</a><a href="<?=Yii::app()->params['siteUrl']?>/admin/zones/delete/id/<?=$zone->id?>" class="delete" >удалить</a></td>
					</tr>
				<?php } ?>
			</tbody>	
		</table>
	<?php } else {
		echo('Тарифных зон нет'); 
	}	
	$this->widget('CLinkPager', array(
    'pages' => $pages,
)) ?>	
</div>
<div id="map" class="map_canvas" style="width: 600px; height: 400px;"></div>