<script src="http://maps.googleapis.com/maps/api/js?sensor=false&amp;libraries=places"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.geocomplete.min.js"></script>
<!--<script src="//api-maps.yandex.ru/2.0/?load=package.full&lang=ru-RU" type="text/javascript"></script>-->
<script type="text/javascript">
	$(document).ready(function() {
		$('#filter').find('select option').click(function(){
		  update_route();
		});
		geolocate();
	});
	function del_point(id, obj){
		$.get("<?=Yii::app()->params['siteUrl']?>/admin/orders/delete_point/id/"+id, function(data){
			if(data == 1){
				$(obj).parent().remove();
				update_route();
			} else {
				alert("Ошибка! Не удалось удалить платеж");
			}
		});
		return false;
	}
	
	function addPoint() {
		var id = new Date().getTime();
		$('#filter').append('<div class="row"><strong>Новая точка</strong><input type="text" class="geocomplete" name="point_add['+id+'][name]" autocomplete="off" value="" placeholder="Введите адресс" maxlength="255" size="60"><input class="latitude" type="hidden" value="" name="point_add['+id+'][latitude]"><input class="longitude" type="hidden" value="" name="point_add['+id+'][longitude]"><input type="text" name="point_add['+id+'][entrance]" placeholder="Подъезд"><a class="delete" onclick="$(this).parent().remove(); update_route(); return false;" href="javascript: void(0);">удалить</a></div>');	
		geolocate();
		return false;
	}
	
	function geolocate() {
		$(".geocomplete").geocomplete({
			map: "",
			details: "form ",
			markerOptions: {
				draggable: true
			}
		}).bind("geocode:result", function(event, result) {
			//console.log(result.address_components[0].long_name);
			$(this).parent().find(".latitude").val(result.geometry.location.k);
			$(this).parent().find(".longitude").val(result.geometry.location.B);
			update_route();
		});
		return false;
	}
	
	function update_route() {
		var params = {};
		$('#filter').find('input').each(function(){
			params[$(this).attr("name")] = $(this).val();
		});
		$('#filter').find('select').each(function(){
			params[$(this).attr("name")] = $(this).val();
		});
		$.ajax({
			url: "<?=Yii::app()->params['siteUrl']?>/admin/orders/new_route",
			type: 'post',
			data: params,
			success: function(data){
				if(data != 0)
					$('#map_container').html(data);
				return false;
			}
		});
		return false;
	}	
</script> 
<?php
if($id != 0)
	$this->breadcrumbs=array('Заказы'=>array('index'), 'Редактирование');
else
	$this->breadcrumbs=array('Заказы'=>array('index'), 'Новый заказ');
$this->menu=array(
	array('label'=>'Заказы', 'url'=>array('index')),
);

if($id != 0) 
	echo('<h1>Редактирование данных заказа</h1>');
else
	echo('<h1>Новый заказ</h1>');
?>

<div class="form">

	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'users-form',
		'enableAjaxValidation'=>false,
		'htmlOptions'=>array('enctype'=>'multipart/form-data'),
	)); 
	echo $form->errorSummary($order); ?>
	<div id="filter">
		
		<div class="row">
			<?php echo $form->labelEx($order,'order_date'); ?>
			<?php echo $form->textField($order,'order_date', array('class'=>'date_picker')); ?>
			<?php echo $form->error($order,'order_date'); ?>
		</div>
		
		<div class="row">
			<?php echo $form->labelEx($order,'is_preliminary'); ?>
			<?php echo $form->dropDownList($order, 'is_preliminary',array('нет', 'да'));?>
			<?php echo $form->error($order,'is_preliminary'); ?>
		</div>
		
		<div class="row">
			<?php echo $form->labelEx($order,'id_price_class'); ?>
			<?php echo $form->dropDownList($order, 'id_price_class',$price_class_all);?>
			<?php echo $form->error($order,'id_price_class'); ?>
		</div>
		<div class="row">
			<?php echo $form->labelEx($order,'id_customer'); ?>
			<?php echo $form->dropDownList($order, 'id_customer',$customers_all);?>
			<?php echo $form->error($order,'id_customer'); ?>
		</div>
		<?php if(!empty($services_all)) { ?>
			<div class="row">
				<label class="required" for="OrderService">Доп. услуги</label>
				<select id="OrderService" name="OrderService[id][]" multiple>
					<?php foreach($services_all as $i => $s) { ?>
						<option value="<?=$i?>"<?php echo(in_array($i, $services_order)?' selected':'');?>><?=$s?></option>
					<?php } ?>	
				</select>
			</div>
		<?php } ?>
		
		<div class="row">
			<label>Откуда</label>
			<input type="text" class="geocomplete" name="<?php echo(isset($order_points[0])?'order_points['.$order_points[0]->id.'][name]':'point_add[0][name]');?>" autocomplete="off" value="<?php echo(isset($order_points[0])?$order_points[0]->adress->name:'');?>" placeholder="Введите адресс" maxlength="255" size="60">
			<input class="latitude" name="<?php echo(isset($order_points[0])?'order_points['.$order_points[0]->id.'][latitude]':'point_add[0][latitude]');?>" type="hidden" value="<?php echo(isset($order_points[0])?$order_points[0]->adress->latitude:'');?>">
			<input class="longitude" name="<?php echo(isset($order_points[0])?'order_points['.$order_points[0]->id.'][longitude]':'point_add[0][longitude]');?>" type="hidden" value="<?php echo(isset($order_points[0])?$order_points[0]->adress->longitude:'');?>">
			<input type="text" name="<?php echo(isset($order_points[0])?'order_points['.$order_points[0]->id.'][entrance]':'point_add[0][entrance]');?>" value="<?php echo(isset($order_points[0])?$order_points[0]->entrance:'');?>" placeholder="Подъезд">
		</div>
	
		<div class="row">
			<?php echo $form->labelEx($order,'additional_info'); ?>
			<?php echo $form->textArea($order,'additional_info',array('rows'=>6, 'cols'=>50)); ?>
			<?php echo $form->error($order,'additional_info'); ?>
		</div>
	
		<label>Промежуточные точки</label>
		<a href="javascript: void(0);" onclick="addPoint();">Добавить точку</a>
		<?php if(isset($order_points[1])) { 
			foreach($order_points as $i => $point){
				if($i != 0) { ?>
			<div class="row">
				<input type="text" class="geocomplete" name="order_points[<?=$point->id?>][name]" autocomplete="off" value="<?=$point->adress->name?>" placeholder="Введите адресс" maxlength="255" size="60">
				<input class="latitude" type="hidden" value="<?=$point->adress->latitude?>" name="order_points[<?=$point->id?>][latitude]">
				<input class="longitude" type="hidden" value="<?=$point->adress->longitude?>" name="order_points[<?=$point->id?>][longitude]">
				<input type="text" name="order_points[<?=$point->id?>][entrance]" value="<?=$point->entrance?>" placeholder="Подъезд">
				<a href="javascript: void(0);" onclick="del_point('<?=$point->id?>',$(this));" class="delete" >удалить</a>
			</div>
			<?php } 
			}
		} else { ?>
			<div class="row">
				<input type="text" class="geocomplete" name="point_add[1][name]" autocomplete="off" value="" placeholder="Введите адресс" maxlength="255" size="60">
				<input class="latitude" type="hidden" value="" name="point_add[1][latitude]">
				<input class="longitude" type="hidden" value="" name="point_add[1][longitude]">
				<input type="text" name="point_add[1][entrance]" placeholder="Подъезд"> 
			</div> 
		<?php } ?>
		
		
	</div>
	<div id="map_container">
		<div class="row">
			<?php echo $form->labelEx($order,'price'); ?>
			<?php echo $form->textField($order,'price', array('readonly' => 'readonly')); ?>
			<?php echo $form->error($order,'price'); ?>
			<?php echo $form->hiddenField($order, 'price_distance');?>
			<?php echo $form->hiddenField($order, 'price_without_class');?>
		</div>
		<div class="row">
			<?php echo $form->labelEx($order,'distance'); ?>
			<?php echo $form->textField($order,'distance', array('readonly' => 'readonly')); ?>
			<?php echo $form->error($order,'distance'); ?>
		</div>
		
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($order,'driver_note'); ?>
		<?php echo $form->textArea($order,'driver_note',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($order,'driver_note'); ?>
	</div>

	

	<div class="row buttons">
			<?php echo CHtml::submitButton($order->isNewRecord ? 'Create' : 'Save'); ?>
	</div>
	<?php $this->endWidget(); ?>
</div><!-- form -->