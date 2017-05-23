<script src="http://maps.googleapis.com/maps/api/js?sensor=false&amp;libraries=places"></script>
<script type="text/javascript">
	var markers = [];
	
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
			
	
	var map = 0;
	$(document).ready(function() {
		setInterval(refresh_map, 6000);
	});
	
	function clear_markers() {
		for (i in markers) {
			markers[i].setMap(null);
		}
	}
	
	function refresh_map() {		
		
		var params = {};
		params['ajax'] = 1;
		$.ajax({
			url: "<?=Yii::app()->params['siteUrl']?>/admin/drivers/drivers_map",
			type: 'post',
			data: params,
			success: function(data){
				clear_markers();
				markers = [];
				var contentString = [];
				var infowindow = [];
				var jsonRequest = JSON.parse(data);
				var info = [];
				//console.log(jsonRequest.length);
				
				for (j=0; j<jsonRequest.length; j++) {
					contentString[j] = '<div id="content">'+jsonRequest[j].phone+'</br>'+jsonRequest[j].car.marka+' '+jsonRequest[j].car.model+'</br>'+jsonRequest[j].car.number+'</br></div>';
					
					infowindow[j] = new google.maps.InfoWindow({
						content: contentString[j],
					 });
					
					var marker_icon;
					
					switch (jsonRequest[j].id_status) {
					  case 1:
						marker_icon = image_blue;
						break
					  case 2:
						marker_icon = image_yellow;
						break
					  case 4:
						marker_icon = image_red;
						break
					  default:
						marker_icon = image_blue;
					}
					
					markers[j]= new google.maps.Marker({
						position: new google.maps.LatLng(jsonRequest[j].lat,jsonRequest[j].lng),
						map: map,
						//clickable: true,			
						icon: marker_icon
					});
					// markers[j].set('id', jsonRequest[j].id);
					var mark = markers[j];
					var info = infowindow[j];					
									
					google.maps.event.addListener(mark, 'click', function() {
						
						info.open(map, mark);
						
					});
				}

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

	function show_markers() {
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
					//markers[<?=$i?>].set('id', <?=$dr->id?>);
					google.maps.event.addListener(markers[<?=$i?>], 'click', function() {
						infowindow<?=$i?>.open(map,markers[<?=$i?>]);
					});
			<?php } 
		} ?>
		return false;	
	}
</script>
  <h1>Карта водителей</h1>
      
	<div id="map_canvas" class="mapBlock" style="min-width: 927px; height: 800px; width: 100%;"></div>