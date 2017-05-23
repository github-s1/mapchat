<script src="http://maps.googleapis.com/maps/api/js?sensor=false&amp;libraries=places"></script>
<script type="text/javascript">
	var markers = [];
	<?php if($ajax == 0) { ?>
		var map = 0;
		$(document).ready(function() {
			setInterval(refresh_map, 60000);
		});
		function refresh_map() {
			var params = {};
			params['ajax'] = 1;
			$.ajax({
				url: "<?=Yii::app()->params['siteUrl']?>/admin/drivers/drivers_map",
				type: 'post',
				data: params,
				success: function(data){
					if(data != 0) {
						for (i in markers) {
							markers[i].setMap(null);
						}
						$('#map_container').html(data);
						initialize();
					}	
					return false;
				}
			});
			return false;
		}
		google.maps.event.addDomListener(window, 'load', initialize);
		
		function initialize() {
			var mapOptions = {
			  center: new google.maps.LatLng(49.9935, 36.230383000000074),
			  zoom: 14,
			  mapTypeId: google.maps.MapTypeId.ROADMAP,
			  draggableCursor: "crosshair"
			};
			map = new google.maps.Map(document.getElementById("map_canvas"),
				mapOptions);
			
			show_markers();
		}
	<?php } ?>	
	function show_markers() {
		var image_red = new google.maps.MarkerImage('<?php echo(Yii::app()->params['siteUrl']);?>/images/red.png', 
		new google.maps.Size(20, 32),      
		new google.maps.Point(0,0),      
		new google.maps.Point(0, 32));
		
		var image_blue = new google.maps.MarkerImage('<?php echo(Yii::app()->params['siteUrl']);?>/images/blue.png', 
		new google.maps.Size(20, 32),      
		new google.maps.Point(0,0),      
		new google.maps.Point(0, 32));
		
		var image_yellow = new google.maps.MarkerImage('<?php echo(Yii::app()->params['siteUrl']);?>/images/yellow.png', 
		new google.maps.Size(20, 32),      
		new google.maps.Point(0,0),      
		new google.maps.Point(0, 32));
		<?php if(!empty($drivers)) { 
				foreach($drivers as $i => $dr) { ?>
					var myLatlng<?=$i?> = new google.maps.LatLng(<?=$dr->lat?>, <?=$dr->lng?>);

					var contentString<?=$i?> = '<div id="content"><?=$dr->user->phone?></br><?=$dr->user->car->marka.' '.$dr->user->car->model?></br><?=$dr->user->car->number?></br></div>';

					var infowindow<?=$i?> = new google.maps.InfoWindow({
						content: contentString<?=$i?>
					 });

					markers[<?=$i?>] = new google.maps.Marker({
						 position: myLatlng<?=$i?>,
						 map: map,
						<?php echo($dr->id_status == 1?'icon: image_blue':($dr->id_status == 2?'icon: image_yellow':($dr->id_status == 4?'icon: image_red':'')));?>
					});
					google.maps.event.addListener(markers[<?=$i?>], 'click', function() {
						infowindow<?=$i?>.open(map,markers[<?=$i?>]);
					});
			<?php } 
		} ?>
		return false;	
	}
</script>
<?php if($ajax == 0) { ?>
<div id="map_container">
<?php } ?>
	<div id="map_canvas" class="map_canvas" style="width: 1200px; height: 800px;"></div>
<?php if($ajax == 0) { ?>
</div>
<?php } ?>