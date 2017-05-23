<script src="//api-maps.yandex.ru/2.0/?load=package.full&lang=ru-RU" type="text/javascript"></script>
<script type="text/javascript">
	var myPolygon;
	
	$(function() {
       $( "#tabs" ).tabs();
    });
	  
	function get_coord(){
		coords = '';
		for (var i = 0; i < myPolygon.geometry.getCoordinates()[0].length; i++) {
			coords += myPolygon.geometry.getCoordinates()[0][i] + ";"
		}
		$("#polygons_coord").val(coords);
		return true;
	}

	function new_service() {
		var params = {};
		$('#new_service').find('input, select').each(function(){
			if($(this).attr("type") == "checkbox")
				params[$(this).attr("name")] = Number($(this).is(':checked'));
			else	
				params[$(this).attr("name")] = $(this).val();
		});
		$.ajax({
			url: "<?=Yii::app()->params['siteUrl']?>/admin/settings/add_service",
			type: 'post',
			data: params,
			success: function(data){
				$('#filter').html(data);
				return false;
			}
		});
		
	}

	function del_service(id, obj){
		$.get("<?=Yii::app()->params['siteUrl']?>/admin/settings/del_service/id/"+id, function(data){
			if(data == 1){
				$(obj).parent().parent().remove();
			} else {
				alert("Ошибка! Не удалось удалить услугу");
			}
		});
		return false;
	}
	
	function del_tariffs_time_day(id, obj){
		$.get("<?=Yii::app()->params['siteUrl']?>/admin/settings/del_tariffs_time_day/id/"+id, function(data){
			if(data == 1){
				$(obj).parent().parent().remove();
			} else {
				alert("Ошибка! Не удалось удалить тариф.");
			}
		});
		return false;
	}
	
	function new_tariffs_time_day() {
		var params = {};
		$('#new_tariffs_time_day').find('input, select').each(function(){
			params[$(this).attr("name")] = $(this).val();	
		});
		$.ajax({
			url: "<?=Yii::app()->params['siteUrl']?>/admin/settings/add_tariffs_time_day",
			type: 'post',
			data: params,
			success: function(data){
				$('#filter_tariffs_time_day').html(data);
				return false;
			}
		});
		
	}
	
	function del_tariffs_day_week(id, obj){
		$.get("<?=Yii::app()->params['siteUrl']?>/admin/settings/del_tariffs_day_week/id/"+id, function(data){
			if(data == 1){
				$(obj).parent().parent().remove();
			} else {
				alert("Ошибка! Не удалось удалить тариф.");
			}
		});
		return false;
	}
	
	function new_tariffs_day_week() {
		var params = {};
		$('#tariffs_day_week').find('input, select').each(function(){
			params[$(this).attr("name")] = $(this).val();	
		});
		$.ajax({
			url: "<?=Yii::app()->params['siteUrl']?>/admin/settings/add_tariffs_day_week",
			type: 'post',
			data: params,
			success: function(data){
				$('#filter_tariffs_day_week').html(data);
				return false;
			}
		});
		
	}
	
	function del_tariffs_time_interval(id, obj){
		$.get("<?=Yii::app()->params['siteUrl']?>/admin/settings/del_tariffs_time_interval/id/"+id, function(data){
			if(data == 1){
				$(obj).parent().parent().remove();
			} else {
				alert("Ошибка! Не удалось удалить тариф.");
			}
		});
		return false;
	}
	
	function new_tariffs_time_interval() {
		var params = {};
		$('#tariffs_time_interval').find('input, select').each(function(){
			params[$(this).attr("name")] = $(this).val();	
		});
		$.ajax({
			url: "<?=Yii::app()->params['siteUrl']?>/admin/settings/add_tariffs_time_interval",
			type: 'post',
			data: params,
			success: function(data){
				$('#filter_tariffs_time_interval').html(data);
				return false;
			}
		});
		
	}

</script>
<?php
$list_types = array('+','%');
?>	
<div id="tabs" class="settings_container tarifs">
          <ul>
            <li><a href="#tabs-1">Базовые</a></li>
            <li><a href="#tabs-2">Услуги</a></li>
            <li><a href="#tabs-3">Доп.услуги</a></li>
			 <li><a href="#tabs-4">По времени</a></li>
          </ul>
          <div id="tabs-1" class="tarifs_inner clearfix">
			<div>	
				<?php $form=$this->beginWidget('CActiveForm', array(
					'enableAjaxValidation'=>false,
					'htmlOptions'=>array('enctype'=>'multipart/form-data', 'onsubmit'=>'return  get_coord()'),
				));  ?>
				<?php foreach ($base_settings as $i=>$item) { ?>
					<label for="setting<?=$item->id?>">
						<span>
							
							<?php if($item->type != 'map') { ?>
								<?=$item->descr?>
								<input id="setting<?=$item->id?>" type="<?=$item->type?>" value="<?=$item->value?>" name="Settings[<?=$item->id?>][value]">
							<?php } else { ?>
								<script type="text/javascript">
									ymaps.ready(init);
									function init() {
										var myMap = new ymaps.Map("map", {
											center: [49.9935, 36.230383000000074],
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
										// Создаем многоугольник без вершин.
										myPolygon = new ymaps.Polygon([[
										<?php if(!empty($item->value)) { 
											$points = explode(";", $item->value);
											unset($points[count($points) - 1]);
											foreach($points as $p) { ?>
												[<?=$p?>],
											<?php } 
										} ?>	
										]], {}, {
											// Курсор в режиме добавления новых вершин.
											editorDrawingCursor: "crosshair",
											strokeWidth: 3
										});
										//console.log(myPolygon.geometry.getCoordinates()[0]);
										// Добавляем многоугольник на карту.
										myMap.geoObjects.add(myPolygon);

										// В режиме добавления новых вершин меняем цвет обводки многоугольника.
										var stateMonitor = new ymaps.Monitor(myPolygon.editor.state);
										stateMonitor.add("drawing", function (newValue) {
											myPolygon.options.set("strokeColor", newValue ? '#FF0000' : '#0000FF');
										});

										// Включаем режим редактирования с возможностью добавления новых вершин.
										myPolygon.editor.startDrawing();
									}	
								</script>	
								<input id="polygons_coord" type="hidden" value="<?=$item->value?>" name="Settings[<?=$item->id?>][value]">
							<?php } ?>	
						</span>
					</label>
				<?php } ?>	
    
             <!-- <p>Километров в минимальном заказе: 5.33 км = (25 - 9)/3</p> -->
			  <?php echo CHtml::submitButton('Сохранить', array(
			   'class'=>'save_set',
			)); ?>
			<?php $this->endWidget(); ?>
            </div>
            <div>
              <p>Границы города</p>
              <div class="tarifs_mini">
					<div id="map" class="map_canvas" style="width: 512px; height: 378px;"></div>
			  </div>
            </div>
            
			
			
          </div>
			<div id="tabs-2" class="services_inner clearfix">
				<div>
					<?php $form=$this->beginWidget('CActiveForm', array(
						'enableAjaxValidation'=>false,
						'htmlOptions'=>array('enctype'=>'multipart/form-data'),
					));  ?>
					<?php foreach ($preliminary_settings as $i=>$item) { ?>
						<p><?=$item->descr?>
							<label>Значение:
								<input id="setting<?=$item->id?>" type="text" value="<?=$item->value?>" name="Settings[<?=$item->id?>][value]">
							</label>
							<label>Тип:
								<select name="Settings[<?=$item->id?>][type]">
									<option <?php echo($item->type == 0?' selected':''); ?> value="0">+</option>
									<option value="1" <?php echo($item->type == 1?' selected':''); ?>>%</option>
								</select>
							</label>
						</p>
					<?php } ?>
					<?php echo CHtml::submitButton('Сохранить', array(
					   'class'=>'save_set',
					)); ?>

					<?php $this->endWidget(); ?>
				</div>
          </div>
          <div id="tabs-3" class="dop_services_inner clearfix">
            <div>
				<?php $form=$this->beginWidget('CActiveForm', array(
					'enableAjaxValidation'=>false,
					'htmlOptions'=>array('enctype'=>'multipart/form-data'),
				)); ?>
                <fieldset id="new_service">
                  <legend>Новая доп. услуга</legend>
                  <label>Имя:
                    <?php echo $form->textField($new_service,'name'); ?>
                  </label>
                  <label>Тип:
                    <?php echo $form->dropDownList($new_service, 'is_percent',$list_types);?>
                  </label>
                  <label>Значение:
                    <?php echo $form->textField($new_service,'value'); ?>
				  </label>
				  <?php echo $form->checkBox($new_service,'is_driver'); ?>
				  <label for="Services_is_driver">Услуга водителя</label>
				  <a href="javascript: void(0);" onclick="new_service();" class="add_button" >Добавить</a>
                </fieldset>
				<ul id="filter">
				<?php if(!empty($services)) {  ?>
					<?php foreach($services as $s) { ?>
						<li>
							<span>
								<?=$s->name?>
							</span>
							<span>
								<select  name="Services_all[<?=$s->id?>][is_percent]">
									<option value="0" <?php echo(!$s->is_percent?'selected':''); ?>>+</option>
									<option value="1" <?php echo($s->is_percent?'selected':''); ?>>%</option>
								</select>
							</span>
							<span>
								<input type="text" value="<?=$s->value?>" name="Services_all[<?=$s->id?>][value]" size="10px">
							</span>
							<span>
								<input type="hidden" value="<?=$s->id?>" name="Services_all[<?=$s->id?>][id]">
								<input type="checkbox" <?php echo($s->is_driver == 1?' checked':''); ?> value="<?=$s->is_driver?>" name="Services_all[<?=$s->id?>][is_driver]" id="service<?=$s->id?>">
								<label for="service<?=$s->id?>">Услуга водителя</label>
							</span>
							<span>
								<a href="javascript: void(0);" onclick="del_service('<?=$s->id?>',$(this));" class="delete" title="Удалить"></a>
							</span>
							</li>
				
					<?php } ?>
				<?php } ?> 
				</ul>
              <button type="submit" class="save_set">Сохранить</button>
			  <?php $this->endWidget(); ?>
            </div>
          </div>
		  
		  <div id="tabs-4" class="dop_services_inner clearfix">
            <div>
				<?php $form=$this->beginWidget('CActiveForm', array(
					'enableAjaxValidation'=>false,
					'htmlOptions'=>array('enctype'=>'multipart/form-data'),
				)); ?>
                <fieldset id="new_tariffs_time_day">
                  <legend>По времени дня</legend>
                  <label>Имя:
                    <?php echo $form->textField($tariff_time_day,'name',array('size'=>20,'maxlength'=>255)); ?>
                  </label>
                  <label>От:
                    <?php $times = array('00:00'=>'00:00', '01:00' => '01:00', '02:00' => '02:00', '03:00' => '03:00', '04:00' => '04:00', '05:00' => '05:00', '06:00' => '06:00', '07:00' => '07:00', '08:00' => '08:00', '09:00' => '09:00', '10:00' => '10:00', '11:00' => '11:00', '12:00' => '12:00', '13:00' => '13:00', '14:00' => '14:00', '15:00' => '15:00', '16:00' => '16:00', '17:00' => '17:00', '18:00' => '18:00', '19:00' => '19:00', '20:00' => '20:00', '21:00' => '21:00', '22:00' => '22:00', '23:00' => '23:00', '24:00' => '24:00');
					echo $form->dropDownList($tariff_time_day, 'from',$times);?>
                  </label>
				   <label>До:
                    <?php echo $form->dropDownList($tariff_time_day, 'before',$times);?>
                  </label>
				  <label>Тип:
                    <?php echo $form->dropDownList($tariff_time_day, 'is_percent',$list_types);?>
                  </label>
                  <label>Значение:
                    <?php echo $form->textField($tariff_time_day,'value', array('maxlength'=>255)); ?>
                 </label>
					
				  <a href="javascript: void(0);" onclick="new_tariffs_time_day();" class="add_button" >Добавить</a>
				  
				  <ul id="filter_tariffs_time_day">
					<?php if(!empty($tariffs_time_day)) {  ?>
						<?php foreach($tariffs_time_day as $s) { ?>
							<li>
								<span><?=$s->name?></span>
								<span><?=$s->from?></span>
								<span><?=$s->before?></span>
								<span><?=$list_types[$s->is_percent]?></span>
								<span><?=$s->value?></span>
								<span>
									<a href="javascript: void(0);" onclick="del_tariffs_time_day('<?=$s->id?>',$(this));" class="delete" title="Удалить"></a>
								</span>
								</li>
					
						<?php } ?>
					<?php } ?> 
					</ul>
                </fieldset>
				  </br>
				<fieldset id="tariffs_day_week">
                  <legend>По дням недели</legend>
                  <label>Имя:
                    <?php echo $form->textField($tariff_day_week,'name',array('size'=>20,'maxlength'=>255)); ?>
                  </label>
                  <label>От:
                    <?php $days = array('воскресенье', 'понедельник', 'вторник', 'среда', 'четверг', 'пятница', 'суббота');
					echo $form->dropDownList($tariff_day_week, 'day_week', $days);?>
                  </label>
				  <label>Тип:
                    <?php echo $form->dropDownList($tariff_day_week, 'is_percent',$list_types);?>
                  </label>
                  <label>Значение:
                    <?php echo $form->textField($tariff_day_week,'value', array('size'=>10,'maxlength'=>255)); ?>
                 </label>
					
				  <a href="javascript: void(0);" onclick="new_tariffs_day_week();" class="add_button" >Добавить</a>
				  <ul id="filter_tariffs_day_week">
					<?php if(!empty($tariffs_day_week)) {  ?>
						<?php foreach($tariffs_day_week as $s) { ?>
							<li>
								<span><?=$s->name?></span>
								<span><?=$days[$s->day_week]?></span>
								<span><?=$list_types[$s->is_percent]?></span>
								<span><?=$s->value?></span>
								<span>
									<a href="javascript: void(0);" onclick="del_tariffs_day_week('<?=$s->id?>',$(this));" class="delete" title="Удалить"></a>
								</span>
								</li>
					
						<?php } ?>
					<?php } ?> 
					</ul>
                </fieldset>
				<br>
				<fieldset id="tariffs_time_interval">
                  <legend>По времени дня</legend>
                  <label>Имя:
                    <?php echo $form->textField($tariff_time_interval,'name',array('size'=>20,'maxlength'=>255)); ?>
                  </label>
                  <label>От:
                   <?php echo $form->textField($tariff_time_interval,'from', array('class'=>'date_picker')); ?>
                  </label>
				   <label>До:
                    <?php echo $form->textField($tariff_time_interval,'before', array('class'=>'date_picker')); ?>
                  </label>
				  <label>Тип:
                    <?php echo $form->dropDownList($tariff_time_interval, 'is_percent',$list_types);?>
                  </label>
                  <label>Значение:
                    <?php echo $form->textField($tariff_time_interval,'value', array('size'=>10,'maxlength'=>255)); ?>
                 </label>
					
				  <a href="javascript: void(0);" onclick="new_tariffs_time_interval();" class="add_button" >Добавить</a>
				  
				  <ul id="filter_tariffs_time_interval">
					<?php if(!empty($tariffs_time_interval)) {  ?>
						<?php foreach($tariffs_time_interval as $s) { ?>
							<li>
								<span><?=$s->name?></span>
								<span><?=$s->from?></span>
								<span><?=$s->before?></span>
								<span><?=$list_types[$s->is_percent]?></span>
								<span><?=$s->value?></span>
								<span>
									<a href="javascript: void(0);" onclick="del_tariffs_time_interval('<?=$s->id?>',$(this));" class="delete" title="Удалить"></a>
								</span>
								</li>
					
						<?php } ?>
					<?php } ?> 
					</ul>
                </fieldset>
				
			  <?php $this->endWidget(); ?>
            </div>
          </div>
        </div>