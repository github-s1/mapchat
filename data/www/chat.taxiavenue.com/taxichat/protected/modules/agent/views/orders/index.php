<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.autocomplete.js"></script> 
<?php if(!$ajax) { ?>
	<script src="https://maps.googleapis.com/maps/api/js?v=3.exp"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			refresh_table();
			refresh_markers();
			setInterval(refresh_table, 60000);
			setInterval(refresh_markers, 6000);
		});
		
		function refresh_table() {	
			var active_li = $('#tabsNew li.ui-tabs-active');
			var url = active_li.attr('data-url');
			
			var elem = active_li.find('a').attr('href');
			
			getTables(url, elem);
		}
		
		function getTables(url, elem)
		{
			$.ajax({
				type: "POST",
				url: "<?=Yii::app()->params['siteUrl']?>/agent/orders/"+url+"/ajax/1<?php echo(isset($_GET['date_from']) && $_GET['date_from'] != ''?'/date_from/'.$_GET['date_from']:'');?><?php echo(isset($_GET['date_to']) && $_GET['date_to'] != ''?'/date_to/'.$_GET['date_to']:'');?><?php echo(isset($_GET['driver']) && $_GET['driver'] != ''?'/driver/'.$_GET['driver']:'');?><?php echo(isset($_GET['client']) && $_GET['client'] != ''?'/client/'.$_GET['client']:'');?><?php echo(isset($_GET['order']) && $_GET['order'] != ''?'/order/'.$_GET['order']:'');?>",
				success: function(data)
				{
					$(elem).html(data);	
					return false;
				}
			});
		}
		
	function refresh_markers()
    {
		$.ajax({
		type: "POST",
		url: "<?=Yii::app()->params['siteUrl']?>/agent/orders/getMarkers",
		success: function(data)
		{
			data = JSON.parse(data);
			if (data.new != 0){
			  $('.new_orders').html(data.new);
			}else{
			  $('.new_orders').html("");
			}
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
		
	</script>

	<h1>Заказы</h1>
	<div class="search_block"><!--search_block-->
		<?php $form=$this->beginWidget('CActiveForm', array(
			'id'=>'filter-form',
			'enableAjaxValidation'=>false,
			'htmlOptions'=>array('enctype'=>'multipart/form-data', 
			'action'=>'drivers'),
		)); ?>
			<label for="">Заказ:<input type="text" name="filter[order]" value="<?php echo(!empty($_GET['order'])?$_GET['order']:''); ?>"/></label>
			<label for="">Создан от:<input type="text" name="filter[date_from]" class="datepicker" value="<?php echo(!empty($_GET['date_from'])?$_GET['date_from']:''); ?>"/></label>
			<label for="">До:<input type="text" name="filter[date_to]" class="datepicker" value="<?php echo(!empty($_GET['date_to'])?$_GET['date_to']:''); ?>"//></label>
			<input class="search_button" type="submit" value="Искать"/>
		<?php $this->endWidget(); ?>
		<?php if(!Yii::app()->user->checkAccess('8')): ?>
		   <a href="javascript: void(0);" class="add_button popup" onclick="popup('<?php echo Yii::app()->request->baseUrl; ?>/agent/orders/create', 'Новый заказ', 'pop_chat pop_zone pop_rating'); return false;">Создать новый</a>
		<?php endif; ?>
		<div class="clear"></div>
	</div><!--search_block_end-->
	<div id="tabsNew" class="orders_tables">
	  <ul>
		<li data-url="index" class="ui-tabs-active"><a href="#tabsOne" onclick="getTables('index', '#tabsOne'); return false;">Новые <span class="new_orders"> </span></a></li>
		<li data-url="pre_orders"><a href="#tabsTwo" onclick="getTables('pre_orders', '#tabsTwo'); return false;">Предварительные <span class="pre_orders"> </span></a></li>
		<li data-url="term_orders"><a href="#tabsThree" onclick="getTables('term_orders', '#tabsThree'); return false;">Срочные <span class="term_orders"> </span></a></li>
		<li data-url="taken_orders"><a href="#tabsFour" onclick="getTables('taken_orders', '#tabsFour'); return false;">Подача <span class="taken_orders"> </span></a></li>
		<li data-url="exp_orders"><a href="#tabsFive" onclick="getTables('exp_orders', '#tabsFive'); return false;">Ожидание <span class="exp_orders"> </span></a></li>
		<li data-url="run_orders"><a href="#tabsSix" onclick="getTables('run_orders', '#tabsSix'); return false;">Выполнение <span class="run_orders"> </span></a></li>
	  </ul>
	  <div id="tabsOne">
  <?php } ?>
<script>
	$(document).ready(function() { 
		ClickPaginate();
	}); 
	
	function ClickPaginate() {
		$('#tabsOne .pagination a').click(function () {
			var url = $(this).attr('href')+"/ajax/1";
			$.ajax({
				url: url,
				type: 'get',
				success: function(data){	
					$('#tabsOne').html(data);	
					return false;
				},
				failure:function(){
				}
			});
			return false;
		});
		return false;
	}

	function getPoints(id_order)
	{
	  $.ajax({
	  type: "POST",
	  url: "<?=Yii::app()->params['siteUrl']?>/agent/orders/get_points/id/"+id_order,
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
			return false;
	   }
	 });
	}	
</script> 

	<?php include('table_orders.php'); ?>
	
	<?php if(!$ajax) { ?>
		</div>
		<div id="tabsTwo"></div>
		<div id="tabsThree"></div>
		<div id="tabsFour"></div>
		<div id="tabsFive"></div>
		<div id="tabsSix"></div>
	</div>
	<div id="map-canvas" style="width:600px; height:500px;margin-top: 20px;"></div>
<?php } ?>