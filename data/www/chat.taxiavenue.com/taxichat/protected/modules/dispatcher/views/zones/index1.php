<script src="http://maps.googleapis.com/maps/api/js?sensor=false&amp;libraries=places"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/editor_polygons.js"></script>
<script type="text/javascript">
	// When the document is ready, create the map and handle clicks on it
jQuery(document).ready(function() {

<?php foreach($tariff_zones as $i => $zone){  
	if(!empty($zone->points)) { 
		$points = explode(";", $zone->points);
		unset($points[count($points) - 1]);
		foreach($points as $p) { ?>
			measureAdd(new google.maps.LatLng(<?=$p?>));
		<?php } ?>
	<?php } 			
} ?>
				
	
});

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