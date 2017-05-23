
<script type="text/javascript">
	ymaps.ready(init);
	function init() {
		var myMap = new ymaps.Map("map", {
			center: [<?php if(!empty($points[0]['latitude']) && !empty($points[0]['longitude'])) { echo($points[0]['latitude'].', '.$points[0]['longitude']); } else { echo('49.9935, 36.230383000000074');} ?>],
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
		<?php if(!empty($points)) { ?>
		<?php foreach($tariff_zones as $i => $zone){
			if(!empty($zone->points)) {
				$points_1 = explode(";", $zone->points);
				unset($points_1[count($points_1) - 1]); ?>

			var myPolygon<?=$i+1?> = new ymaps.Polygon([[
			<?php foreach($points_1 as $p) { ?>
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
	ymaps.route([
		<?php foreach($points as $point){  ?>
			[<?=$point['latitude']?>, <?=$point['longitude']?>],
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
			$prom_points_count = count($points) - 2;
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
<div id="map" class="map_canvas" style="width: 600px; height: 400px;"></div>

<div class="row">
	<label for="Orders_price">Цена</label>
	<input id="Orders_price" type="text" maxlength="8" value="<?=$settings['min_order_price']['value']?>" readonly name="Orders[price]">
	<input id="Orders_price_distance" type="hidden" value="<?=$settings['min_order_price']['value']?>" name="Orders[price_distance]">
</div>
<div class="row">
	<label for="Orders_distance">Расстояние</label>
	<input id="Orders_distance" type="text" maxlength="9" value="0" readonly name="Orders[distance]">
</div>
