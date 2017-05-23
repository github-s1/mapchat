<script src="https://maps.googleapis.com/maps/api/js?v=3.exp"></script>
<?php if(!empty($DangerDriver->lat) and !empty($DangerDriver->lng)): ?>
 <script>
  var myLatlng = new google.maps.LatLng(<?=$DangerDriver->lat?>,<?=$DangerDriver->lng?>);	
  var map;
  var markers = new Array();
  var infos = new Array();
  var NewIconHref;
  var NewContentReply;
   var g=0;
 
   function initialize() {
     var mapOptions = {
      zoom: 14,
      center: new google.maps.LatLng(<?=$DangerDriver->lat?>,<?=$DangerDriver->lng?>)
     };
      map = new google.maps.Map(document.getElementById('map-canvas'),
      mapOptions);
       

      var contentString = "<div style='width:200px; height:140px'><p><strong>Имя: </strong><?=$DangerDriver->user->name?></p><br/><p><strong>Телефон: </strong><?=$DangerDriver->user->phone?></p><br/><p><strong>Машина: </strong><?=$DangerDriver->user->car->marka?>, <?=$DangerDriver->user->car->model?>, <?=$DangerDriver->user->car->number?></p><br/><br/><p><strong>Cообщение: </strong><?=$msg?></p></div>";
      var infowindow = new google.maps.InfoWindow({
       content: contentString,
       maxWidth: 300
      });

      var marker = new google.maps.Marker({
      position: myLatlng,
      icon: "<?php echo(Yii::app()->params['siteUrl']);?>/images/red.png" , //'http://mapicons.nicolasmollet.com/wp-content/uploads/mapicons/shape-default/color-a61e22/shapecolor-color/shadow-1/border-dark/symbolstyle-white/symbolshadowstyle-dark/gradient-no/bomb.png',
      map: map
      
    });

       google.maps.event.addListener(marker, 'click', function() {
          infowindow.open(map,marker);
      });

   }
    google.maps.event.addDomListener(window, 'load', initialize);

setInterval(function(){
	$.ajax({
		type: "POST",
		url: "<?=Yii::app()->params['siteUrl']?>/driver_application/Baikal/GetRepliedDrivers/?baikal=<?=$id?>",
		success: function(data){
			var jsonRequest = JSON.parse(data);
			updateBlocks(jsonRequest);
      checkUl("helpers2");
      checkUl("free_drivers2");
      checkUl("non_helpers2");
			for (j=0; j<jsonRequest.length; j++) {
				g=0;
// --------------------------------------------------------------------------------
				for (i=0; i<markers.length; i++) {
					if (markers[i].id != jsonRequest[j].driver_id) {
						g = g + 1;
					}
					else {
						if (jsonRequest[j].response == 0) {
							NewIconHref ="<?php echo(Yii::app()->params['siteUrl']);?>/images/green.png";
							NewContentReply = "<div style='width:200px; height:100px'><h3>" + jsonRequest[j].driver_name +"</h3><p> Еду на помощь </p></div>";
						}
						else if (jsonRequest[j].response == 1) {
							NewIconHref ="<?php echo(Yii::app()->params['siteUrl']);?>/images/yellow.png";
							NewContentReply = '<div style="width:200px; height:100px"><h3>' + jsonRequest[j].driver_name +'</h3><p> Отказался помочь </p><br/><a href="javascript:void(0)" onclick="makeRequest('+ jsonRequest[j].driver_id +')">Отправить запрос</a> </div>';
						}
						else if (jsonRequest[j].response == 2) {
							NewIconHref ="<?php echo(Yii::app()->params['siteUrl']);?>/images/blue.png";
							NewContentReply = '<div style="width:200px; height:100px"><h3>' + jsonRequest[j].driver_name +'</h3><p> Свободен </p><br/><a href="javascript:void(0)" onclick="makeRequest('+ jsonRequest[j].driver_id +')">Отправить запрос</a> </div>';
						}
						var newPosition = new google.maps.LatLng(jsonRequest[j].lat,jsonRequest[j].lng);
						markers[i].setPosition(newPosition);
						markers[i].setIcon(NewIconHref);
						infos[i].setContent(NewContentReply);
						//  makeInfoWin(markers[i], NewContentReply, map);
					}
				}
// --------------------------------------------------------------------------------
				if (g == markers.length){
					if (jsonRequest[j].response == 0) {
						var contentReply = "<div style='width:200px; height:100px'><h3>" + jsonRequest[j].driver_name +"</h3><p> Еду на помощь </p></div>";
						var iconHref ="<?php echo(Yii::app()->params['siteUrl']);?>/images/green.png";
					}
					else if (jsonRequest[j].response == 1) {
						var contentReply = '<div style="width:200px; height:100px"><h3>' + jsonRequest[j].driver_name +'</h3><p> Отказался помочь </p><br/><a href="javascript:void(0)" onclick="makeRequest('+ jsonRequest[j].driver_id +')">Отправить запрос</a> </div>';
						var iconHref ="<?php echo(Yii::app()->params['siteUrl']);?>/images/yellow.png";
					}
					else if (jsonRequest[j].response == 2) {
						var contentReply = '<div style="width:200px; height:100px"><h3>' + jsonRequest[j].driver_name +'</h3><p> Свободен </p><br/><a href="javascript:void(0)" onclick="makeRequest('+ jsonRequest[j].driver_id +')">Отправить запрос</a> </div>';
						var iconHref ="<?php echo(Yii::app()->params['siteUrl']);?>/images/blue.png";
					}
					markers[j]= new google.maps.Marker({
						position: new google.maps.LatLng(jsonRequest[j].lat,jsonRequest[j].lng),
						map: map,
						clickable: true,
						title: jsonRequest[j].driver_name,
						icon: iconHref
					});
					markers[j].set('id', jsonRequest[j].driver_id);
					makeInfoWin(markers[j], contentReply, map,j);
					function makeInfoWin(marker, data, map,j) {
						infos[j] = new google.maps.InfoWindow({ content: data });
						google.maps.event.addListener(marker, 'click', function() {
							infos[j].open(map,marker);  
						});
					}
				}
// --------------------------------------------------------------------------------
			}
		}       
	});
}, 5000);

       function makeRequest(driver_id)
       {
   
            params = {
                    baikal : <?=$id?>,
                    driver : driver_id,
                };
          $.ajax({
            type: "POST",
            data: params,
            url: "<?=Yii::app()->params['siteUrl']?>/driver_application/Baikal/AskForHelp/",
            success: function(data){
               if(data == 0){
                alert("Произошла ошибка");
               }
               if (data == 1){
                alert("Запрос отправлен");
               }
             }
            }); 
       }
        function updateBlocks(request)
           {
             var helpers = document.getElementById('helpers2');
             var nonhelpers = document.getElementById('non_helpers2');
             var free = document.getElementById('free_drivers2');
             var helpers_list = ""
             var nonhelpers_list = ""
             var free_list = ""
             for (j=0; j<request.length; j++)
             {
              if (request[j].response == 0)
              {
                helpers_list = helpers_list + "<li> <ul> <li><b> Имя: </b>" + request[j].driver_name + "</li> <li><b>Телефон: </b>" + request[j].phone + "</li></ul></li>";
              }else if (request[j].response == 1)
              {
                nonhelpers_list = nonhelpers_list + "<li> <ul> <li><b> Имя:</b> " + request[j].driver_name + "</li> <li><b>Телефон: </b>" + request[j].phone + "</li></ul></li>";
              }else if (request[j].response == 2)
              {
                free_list = free_list + "<li> <ul> <b><li> Имя:</b> " + request[j].driver_name + "</li> <li><b>Телефон: </b>" + request[j].phone + "</li></ul></li>";
              }
             }
              
              helpers.innerHTML = helpers_list;
              nonhelpers.innerHTML = nonhelpers_list;
              free.innerHTML = free_list;
          } 
           

      
      </script>
<?php endif;?>
<div id="tabs">
  <ul class="tabs">
    <li><a href="#tabs-1">Активные</a></li>
    <li><a href="#tabs-2">Завершённые</a></li> 
  </ul>
  <div id="tabs-1" class="baykal">
  <div class="settings">
    <div class="menu_settings">
      <h2>Опасность:</h2>
        <?php $this->widget('SettingsMenu',array(
          'items'=>$items
        )); ?> 
      <h2>Техпомощь:</h2>
        <?php $this->widget('SettingsMenu',array(
          'items'=>$seconditems
        )); ?>
    </div>
        
    <div class="settings_container">
      <table> 
        <tr>
          <td>
            <div id="map-canvas" style="width:900px; height:600px;margin-top: 20px;"></div>
          </td>
          <td>
          <?php if(!empty($id)): ?>
            <div id="drivers_activity">
              <div id="author">
                <h3><?=$DangerDriver->user->name?>(требуется помощь)</h3>
                <ul>
                  <li><b>Имя:</b> <?=$DangerDriver->user->name?> </li>
                  <li><b>Телефон:</b> <?=$DangerDriver->user->phone?> </li>
                  <li><b>Машина:</b> <?=$DangerDriver->user->car->marka?>, <?=$DangerDriver->user->car->model?>, <?=$DangerDriver->user->car->number?> </li>
                  <li><b>Cообщение:</b> <?php if (!empty($msg)){echo $msg;}else{echo "Отсутствует";} ?> </li>
                </ul>
              </div>
              <div id="helpers"> 
                <h3> Помогают </h3>
                  <ul id="helpers2">
                  </ul>
                  <a href='' class='openUl' onclick="return false;">Показать больше >></a>
              </div>
              <div id="free_drivers">
                <h3> Свободны </h3>
                <ul id="free_drivers2">
                </ul>
                <a href='' class='openUl' onclick="return false;">Показать больше >></a>
              </div>
              <div id="non_helpers">
                <h3>Не помогают </h3>
                <ul id="non_helpers2">
                </ul>
                <a href='' class='openUl' onclick="return false;">Показать больше >></a>
              </div>
            </div> 
          <?php endif; ?>
          </td>
        </tr>
      </table>
    </div>
    
    <div class="clear"></div>
  </div>  
  </div>

  <div id="tabs-2" class="baykal">
    <table> 
      <thead>
        <tr>
          <th>Тип</th>
          <th>ID</th>
          <th>Вызвал</th>
          <th>Подробнее</th>
        </tr>
      </thead>
      <tbody>
      <?php if (!empty($baikalsNonActual)): ?>
        <?php foreach ($baikalsNonActual as $Baikal): ?>
        <tr>
          <td> <?php if($Baikal->status == 0){echo "Опасность";}else{echo "Техпомощь";} ?> </td>
          <td> <?=$Baikal->id?> </td>
          <td> <?=$Baikal->driver->phone?> </td>
          <td><a href="" onclick="window.open('<?=Yii::app()->params['siteUrl']?>/dispatcher/showBaikal/BaikalDetails/?id=<?=$Baikal->id?>', 'Baikal', params)">Подробнее</a></td>
        </tr>
        <?php endforeach;?>
      <?php endif; ?>
      </tbody>
    </table>
        <?php $this->widget('MyLinkPager', array(
            'pages' => $pages,
          )) ?>
  </div>
</div>
</section>
<script> 
   function allBaikals()
   {
         var infos = new Array();      
          $.ajax({
           type: "POST",
           url: "<?=Yii::app()->params['siteUrl']?>/driver_application/Baikal/actualBaikals",
           success: function(data){
             var start_lat, start_lng;
             if (data != "0")
             {
               data = JSON.parse(data);
               data = data.response;
               start_lat = data[0].lat;
               start_lng = data[0].lng;
             }else{
               start_lat = 49.984110;
               start_lng = 36.230850;
             }
              var mapAll;

                 function initialize() 
                 {
                    var mapOptions = {
                     zoom: 8,
                     center: new google.maps.LatLng(start_lat, start_lng)
                    };
                    mapAll = new google.maps.Map(document.getElementById('map-canvas'),mapOptions);
                 }
                 initialize();
                  var markers = new Array();
                  if (data != 0){
                  for (i=0; i<data.length; i++) {
                    var myLatlng = new google.maps.LatLng(data[i].lat, data[i].lng); 
                    markers[i] = new google.maps.Marker({
                    position: myLatlng,
                    icon: "<?php echo(Yii::app()->params['siteUrl']);?>/images/red.png" , 
                    map: mapAll
                   });
                   if (data[i].type == 0) {
                      cont = "<div style='width:200px; height:140px'><p><strong>Имя: </strong>"+data[i].driver_name+"</p><br/><p><strong>Тип байкала: </strong>Угроза жизни</p>";
                   }else if (data[i].type == 1) {
                      cont = "<div style='width:200px; height:140px'><p><strong>Имя: </strong>"+data[i].driver_name+"</p><br/><p><strong>Тип байкала: </strong>Техпомощь</p>";
                   }
                   makeInfoWin(markers[i], cont, mapAll,i);
                 }
               }
             // alert(data.response[0].id_driver);
           }
           }); 


      function makeInfoWin(marker, data, map,j) {
            infos[j] = new google.maps.InfoWindow({ content: data });
            google.maps.event.addListener(marker, 'click', function() {
              infos[j].open(map,marker);  
            });
          } 
   }
</script>
 <?php if(empty($id)): ?>
   <script>
    $( document ).ready(function() {
      allBaikals();
    });
   </script> 
<?php endif; ?>