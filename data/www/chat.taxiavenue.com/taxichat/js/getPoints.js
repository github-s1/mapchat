 function getPoints(id_order)
	{

		$.ajax({
			type: "POST",
			url: "/dispatcher/orders/get_points/id/"+id_order,
			success: function(data)
			{
				//$(elem).html(data);
				if(data != 0) {
					var jsonRequest = JSON.parse(data);
					var map;
					var directionsDisplay;
					var directionsService = new google.maps.DirectionsService();

					var start = new google.maps.LatLng(jsonRequest[0].lat,jsonRequest[0].lng);
					var finish = [];
					var count = jsonRequest.length;
					if(count > 1) {
						finish = new google.maps.LatLng(jsonRequest[count - 1].lat,jsonRequest[count - 1].lng);
					} else {
						finish = new google.maps.LatLng(jsonRequest[0].lat,jsonRequest[0].lng);
					} 

					var waypoints = [];
					for(j = 1; j < jsonRequest.length - 1; j++) {
						waypoints.push({
							location: new google.maps.LatLng(jsonRequest[j].lat,jsonRequest[j].lng),
							stopover: true
						});
					}

					directionsDisplay = new google.maps.DirectionsRenderer();
					var mapOptions = {
						zoom: 8,
						center: start
					};
					map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);

					var request = {
						origin: start, //точка старта
						destination: finish,
						waypoints:  waypoints,
						optimizeWaypoints: false,
						travelMode: google.maps.DirectionsTravelMode.DRIVING
					//режим прокладки маршрута
					};

					directionsService.route(request, function(response, status) {
						if (status == google.maps.DirectionsStatus.OK) {
							directionsDisplay.setDirections(response);
						}
					});
					directionsDisplay.setMap(map);
				}
   
		   }
		});
	}