  <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.autocomplete.js"></script>    
  <script src="http://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places"></script>
   <?php if(Dispatcher::$contentUploader == "socket"): ?>
      <script src="<?php echo Yii::app()->request->baseUrl; ?>/js/socket.js"></script>
   <?php elseif(Dispatcher::$contentUploader=="ajax"):?>
   <script type="text/javascript">
   
    $(document).ready(function() {
        getTables('new_orders', '#tabsOne');
        refresh_markers();
        setInterval(refresh_table, 60000);
        setInterval(refresh_markers, 60000);
    });
    
    function refresh_table() {  
        var active_li = $('#tabsNew li.ui-state-active');
        var url = active_li.attr('data-url');
        var elem = active_li.find('a').attr('href');
        getTables(url, elem);
    }
    
    function getTables(url, elem)
    {
        var newElem = elem + "App";
        var OpElem = elem + "Operator";
        <?php if(Yii::app()->user->checkAccess('4')):?>
        $.ajax({
            type: "POST",
            url: "<?=Yii::app()->params['siteUrl']?>/dispatcher/orders/"+url+"/ajax/1<?php echo(isset($_GET['date_from']) && $_GET['date_from'] != ''?'/date_from/'.$_GET['date_from']:'');?><?php echo(isset($_GET['date_to']) && $_GET['date_to'] != ''?'/date_to/'.$_GET['date_to']:'');?><?php echo(isset($_GET['driver']) && $_GET['driver'] != ''?'/driver/'.$_GET['driver']:'');?><?php echo(isset($_GET['client']) && $_GET['client'] != ''?'/client/'.$_GET['client']:'');?><?php echo(isset($_GET['order']) && $_GET['order'] != ''?'/order/'.$_GET['order']:'');?>",
            success: function(data)
            {
                $(newElem).html(data);
                return false;
            }
       });
       <?php endif; ?>
        url = url + "Op";
        $.ajax({
            type: "POST",
            url: "<?=Yii::app()->params['siteUrl']?>/dispatcher/orders/"+url+"/ajax/1<?php echo(isset($_GET['date_from']) && $_GET['date_from'] != ''?'/date_from/'.$_GET['date_from']:'');?><?php echo(isset($_GET['date_to']) && $_GET['date_to'] != ''?'/date_to/'.$_GET['date_to']:'');?><?php echo(isset($_GET['driver']) && $_GET['driver'] != ''?'/driver/'.$_GET['driver']:'');?><?php echo(isset($_GET['client']) && $_GET['client'] != ''?'/client/'.$_GET['client']:'');?><?php echo(isset($_GET['order']) && $_GET['order'] != ''?'/order/'.$_GET['order']:'');?>",
            success: function(newdata)
            {
                $(OpElem).html(newdata); 
                return false;
            }
        });
    }
    
    </script>
    <?php endif; ?>

<h1>Заказы</h1>
<div class="search_block"><!--search_block-->
    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'filter-form',
        'enableAjaxValidation'=>false,
        'htmlOptions'=>array('enctype'=>'multipart/form-data'),
    )); ?>
    <label for="">Заказ:<input type="text" name="filter[order]" value="<?php echo(!empty($_GET['order'])?$_GET['order']:''); ?>"/></label>
    <label for="">Клиент:<input id="autocompleteCustomer" autocomplete="off" type="text" name="filter[client]" value="<?php echo(!empty($_GET['client'])?$_GET['client']:''); ?>"/></label>
    <label for="">Водитель:<input id="autocompleteDriver" autocomplete="off" type="text"  name="filter[driver]" value="<?php echo(!empty($_GET['driver'])?$_GET['driver']:''); ?>"/></label>
    <label for="">Создан от:<input type="text" name="filter[date_from]" class="datepicker" value="<?php echo(!empty($_GET['date_from'])?$_GET['date_from']:''); ?>"/></label>
    <label for="">До:<input type="text" name="filter[date_to]" class="datepicker" value="<?php echo(!empty($_GET['date_to'])?$_GET['date_to']:''); ?>" /></label>
    <input class="search_button" type="submit" value="Искать"/>
    <?php $this->endWidget(); ?>

	
    <a href="javascript: void(0);" class="add_button popup" onclick="popup('<?php echo Yii::app()->request->baseUrl; ?>/dispatcher/orders/create', 'Новый заказ', 'pop_chat pop_zone pop_rating'); return false;">Создать новый</a>
    <div class="clear"></div>
</div><!--search_block_end-->

<div id="tabsNew" class="orders_tables">
  <ul class="tabs">
    <li data-url="new_orders"><a href="#tabsOne" onclick="getTables('new_orders', '#tabsOne'); return false;">Новые <span class="new_orders"> </span></a> </li>
    <li data-url="pre_orders"><a href="#tabsTwo" onclick="getTables('pre_orders', '#tabsTwo'); return false;">Предварительные <span class="pre_orders"> </span></a> </li>
    <li data-url="term_orders"><a href="#tabsThree" onclick="getTables('term_orders', '#tabsThree'); return false;">Срочные <span class="term_orders"> </span></a> </li>
    <li data-url="taken_orders"><a href="#tabsFour" onclick="getTables('taken_orders', '#tabsFour'); return false;">Подача <span class="taken_orders"> </span></a> </li>
    <li data-url="exp_orders"><a href="#tabsFive" onclick="getTables('exp_orders', '#tabsFive'); return false;">Ожидание <span class="exp_orders"> </span></a> </li>
    <li data-url="run_orders"><a href="#tabsSix" onclick="getTables('run_orders', '#tabsSix'); return false;">Выполнение <span class="run_orders"> </span></a> </li>
    </ul>
    <script>

    function getPoints(id_order)
    {
        $("#map-canvasInner").remove();
        $("[data-point = "+id_order+"]").after("<div id='map-canvasInner'><a href='javascript:void(0);' class='closeWidget'>x</a><div id='map-canvas'></div></div>");
        $(".closeWidget").click(function () {
            $("#map-canvasInner").toggleClass("close", "open");
            setTimeout(function () {
                $("#map-canvasInner").remove();
            }, 500);
        });
        $.ajax({
            type: "POST",
            url: "<?=Yii::app()->params['siteUrl']?>/dispatcher/orders/get_points/id/"+id_order,
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
                $.ajax({
                type: "POST",
                url: "<?=Yii::app()->params['siteUrl']?>/dispatcher/orders/NearestDrivers/lat/"+jsonRequest[0].lat+"/lng/"+jsonRequest[0].lng+"/id/"+id_order+"/",
                success: function(data)
                {
                    if (data != 0){
                        var infos = new Array();
                        var markers = new Array();
                        var drivers = JSON.parse(data);
                        for (var i=0; i<drivers.length; i++) {
                            var myLatlng = new google.maps.LatLng(drivers[i].lat, drivers[i].lng); 
                            markers[i] = new google.maps.Marker({
                            position: myLatlng,
                            icon: "<?php echo(Yii::app()->params['siteUrl']);?>/images/blue.png" , 
                            map: map
                            });
                            var content = "<p>Имя: " + drivers[i].driver_name + "</p><p><a href='javascript: void(0);' onclick='makeForcedRequest("+id_order+","+drivers[i].driver_id+")'> Отправить запрос </a></p>" ;
                            makeInfoWin(markers[i],content,map, i);
                        }
                    }
                    function makeInfoWin(marker, data, map,j)
                    {
                        infos[j] = new google.maps.InfoWindow({ content: data });
                        google.maps.event.addListener(marker, 'click', function() {
                            infos[j].open(map,marker);  
                        });
                    } 
                    $("#map-canvasInner").addClass("open");
                }
                });
           }
        });
    }
  </script> 
    <div id="tabsOne"><div id="tabsOneOperator"></div><div id="tabsOneApp"></div></div>
    <div id="tabsTwo"><div id="tabsTwoOperator"></div><div id="tabsTwoApp"></div></div>
    <div id="tabsThree"><div id="tabsThreeOperator"></div><div id="tabsThreeApp"></div></div>
    <div id="tabsFour"><div id="tabsFourOperator"></div><div id="tabsFourApp"></div></div>
    <div id="tabsFive"><div id="tabsFiveOperator"></div><div id="tabsFiveApp"></div></div>
    <div id="tabsSix"><div id="tabsSixOperator"></div><div id="tabsSixApp"></div></div>
</div>

 
<script>

    $(document).ready(function(){
  

      $('#autocompleteDriver').autocomplete("<?php //echo Yii::app()->request->baseUrl; ?>/dispatcher/orders/get_driver", {  
      matchContains: false,
      scroll: true,
      highlight: false,
      width: 255,
      cache: false,
      autoFill:false,
      selectFirst:false,
      formatItem: function(row) {
        return "<a href='javascript: void(0);'>" + row[0] + ' '+ row[1] + "</a>";
      },
      formatResult: function(row) {
        return row[0].replace(/(<.+?>)/gi, '');
        
        //return true;
      },
    });
    $('#autocompleteCustomer').autocomplete("<?php //echo Yii::app()->request->baseUrl; ?>/dispatcher/orders/get_customer", {  
      matchContains: false,
      scroll: true,
      highlight: false,
      width: 255,
      cache: false,
      autoFill:false,
      selectFirst:false,
      formatItem: function(row) {
        return "<a href='javascript: void(0);'>" + row[0] + ' '+ row[1] + "</a>";
      },
      formatResult: function(row) {
        return row[0].replace(/(<.+?>)/gi, '');
        
        //return true;
      },
    });

  });
    

	function makeForcedRequest(order,driver)
		{
			
			function makeCall()
			{ 
				
				params = {
                    order : order,
                    driver : driver
                };
                $.ajax({
                  type: "POST",
                  data: params,
                  url: "<?=Yii::app()->params['siteUrl']?>/dispatcher/orders/makeForcedRequest/",
                    success: function(data){
                    data = JSON.parse(data);
                       if (data.result != 'failure'){
                         alert("Запрос отправлен");
                         setTimeout(getAnswer, 180000);
                       }else{
                         alert(data.errorName);
                       }
                    }
			          });

            }

			function getAnswer()
			{
                params = {
                    order : order,
                    driver : driver
                };
                $.ajax({
                  type: "POST",
                  data: params,
                  url: "<?=Yii::app()->params['siteUrl']?>/dispatcher/orders/GetAnswerForced/",
                    success: function(data){
                      data = JSON.parse(data);
                       if (data.result != 'failure'){
                         alert("Водитель принял ваш запрос на заказ " + order);
                       }else{
                         alert(data.errorName);
                       }
                   }
			  });
			}
			makeCall();
		}

    function refresh_markers()
    {
      $.ajax({
      type: "POST",
      url: "<?=Yii::app()->params['siteUrl']?>/dispatcher/orders/getMarkers",
       success: function(data)
       {
        data = JSON.parse(data);
        if (data.pre != 0){
          $('.pre_orders').html(data.pre);
        }else{
          $('.pre_orders').html("");
        }
         if (data.exp != 0){
          $('.exp_orders').html(data.exp);
        }else{
          $('.exp_orders').html("");
        }
        if (data.taken != 0){
          $('.taken_orders').html(data.taken);
        }else{
          $('.taken_orders').html("");
        }
        if (data.term != 0){
          $('.term_orders').html(data.term);
        }else{
           $('.term_orders').html("");
        }
        if (data.run != 0){
          $('.run_orders').html(data.run);
        }else{
          $('.run_orders').html("");
        }
        return false;
       }
     });
    }

    $(document).ready(function() {
     
      setInterval(refresh_markers, 15000);
    });
    
</script>

 
<!--<div id="map-canvas"></div>-->
