<script src="http://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.autocomplete.js"></script> 
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/getPoints.js"></script> 
<script type="text/javascript">   
    $(document).ready(function() {
      getTables('finished_orders', '#tabsOne');
      setInterval(refresh_table, 60000);
    });
    
    function refresh_table() {  
      var active_li = $('#tabsNew li.ui-state-active');
      var url = active_li.attr('data-url');
      
      var elem = active_li.find('a').attr('href');
      
      getTables(url, elem);
    }
    
    function getTables(url, elem)
    {
      $.ajax({
      type: "POST",
      url: "<?=Yii::app()->params['siteUrl']?>/dispatcher/orders/"+url+"/ajax/1<?php echo(isset($_GET['date_from']) && $_GET['date_from'] != ''?'/date_from/'.$_GET['date_from']:'');?><?php echo(isset($_GET['date_to']) && $_GET['date_to'] != ''?'/date_to/'.$_GET['date_to']:'');?><?php echo(isset($_GET['driver']) && $_GET['driver'] != ''?'/driver/'.$_GET['driver']:'');?><?php echo(isset($_GET['client']) && $_GET['client'] != ''?'/client/'.$_GET['client']:'');?><?php echo(isset($_GET['order']) && $_GET['order'] != ''?'/order/'.$_GET['order']:'');?>",
       success: function(data)
       {
        $(elem).html(data); 
        return false;
       }
     });
    }
    
  </script>
<h1>Архив заказов</h1>
<div class="search_block"><!--search_block-->
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'filter-form',
		'enableAjaxValidation'=>false,
		'htmlOptions'=>array('enctype'=>'multipart/form-data'),
	)); ?>
	  <label for="">Заказ:<input type="text" name="filter[order]" value="<?php echo(!empty($_GET['order'])?$_GET['order']:''); ?>"/></label>
	  <label for="">Клиент:<input id="autocompleteCustomer" autocomplete="off" type="text" name="filter[client]" value="<?php echo(!empty($_GET['client'])?$_GET['client']:''); ?>"/></label>
		<label for="">Водитель:<input id="autocompleteDriver" autocomplete="off" type="text" name="filter[driver]" value="<?php echo(!empty($_GET['driver'])?$_GET['driver']:''); ?>"/></label>
		<label for="">Создан от:<input type="text" name="filter[date_from]" class="datepicker" value="<?php echo(!empty($_GET['date_from'])?$_GET['date_from']:''); ?>"/></label>
		<label for="">До:<input type="text" name="filter[date_to]" class="datepicker" value="<?php echo(!empty($_GET['date_to'])?$_GET['date_to']:''); ?>"//></label>
		<input class="search_button" type="submit" value="Искать"/>
	<?php $this->endWidget(); ?>
	
	<div class="clear"></div>
	<div class="clear"></div>
</div><!--search_block_end-->
<div id="tabsNew">
  <ul class="tabs">
    <li data-url="finished_orders"><a href="#tabsOne" onclick="getTables('finished_orders', '#tabsOne'); return false;">Выполненые</a></li>
    <li data-url="finished_orders"><a href="#tabsTwo" onclick="getTables('customerCanceled', '#tabsTwo'); return false;">Отменённые клиентом</a></li>
    <li data-url="DriverCancel"><a href="#tabsThree"  onclick="getTables('driverCancel', '#tabsThree'); return false;">Отменённые водителем</a></li>
  </ul> 
<div id="tabsOne"></div>
<div id="tabsTwo"></div>
<div id="tabsThree"></div>
</div>
<script>
$(document).ready(function() {
	$('.info').click(function () {
		var divToggle = $(this).next();
		divToggle.slideToggle(0);
		$(this).toggleClass('active');
	});
});

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
      $("#map-canvasInner").addClass("open");
      return false;
     }
   });
  }
  /*
    $(document).ready(function(){
      $('#autocompleteDriver').autocomplete("<?php echo Yii::app()->request->baseUrl; ?>/dispatcher/orders/get_driver", {  
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
    $('#autocompleteCustomer').autocomplete("<?php echo Yii::app()->request->baseUrl; ?>/dispatcher/orders/get_customer", {  
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
*/
     
</script>


