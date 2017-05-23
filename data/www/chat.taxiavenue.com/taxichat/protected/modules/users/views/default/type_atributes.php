
<?php if($type == 1) { ?>
<script type="text/javascript">
$(function(){
	var type = $("#Models_marka").val();
		loadModel(type);
		
	$('#Models_marka').change(function(){
		var id_marka = $("#Models_marka").val();
		loadModel(id_marka);
		return false;
	});
});


function loadModel(id_marka){
	var params = new Object();
	params['id_marka'] = id_marka;
	$.ajax({
		url: '<?=Yii::app()->params['siteUrl']?>/users/model_atributes/<?=$id?>',
		type: 'post',
		data: params,
		success: function(data){
			$('#features_model').html(data);
			return false;
		}
	});
}
</script>
	<input type="hidden" value="<?php echo(isset($model->user_car)?$model->user_car->id:'');?>" name="Cars[id]">
	<div class="row">
		<label class="required" for="Models_marka">Марка
			<span class="required">*</span>
		</label>
		<select id="Models_marka" name="Models[marka]">
			<?php foreach($marks_all as $i => $marka) { ?>
				<option <?php echo(isset($model->user_car->model_car->marka_car)&&$model->user_car->model_car->marka_car->id == $i?'selected':''); ?> value="<?=$i?>"><?=$marka?></option>
			<?php } ?>
		</select>
	</div>
	<div id="features_model"></div>
	<div class="row">
		<label class="required" for="Cars_color">Цвет
			<span class="required">*</span>
		</label>
		<input id="Cars_color" type="text" value="<?php echo(isset($model->user_car)?$model->user_car->color:'');?>" name="Cars[color]" maxlength="255" size="60">
	</div>

	<?php if(isset($model->user_car)) { 
		$img_fields = array('1' =>$model->user_car->photo1, '2' => $model->user_car->photo2, '3' => $model->user_car->photo3, '4' => $model->user_car->photo4, '5' => $model->user_car->photo5, '6' => $model->user_car->photo6, '7' => $model->user_car->photo7);
		foreach($img_fields as $i => $field) { ?> 
			<div class="row">
				<label class="required" for="Cars_photo<?=$i?>">photo<?=$i?></label>
				<?php 
				if(!empty($field)) {
					echo CHtml::image(Yii::app()->params['siteUrl'].'/images/cars/'.$model->user_car->id.'/photo'.$i.'.'.$field, '',
						array(
						'width'=>'200',
						'class'=>'image',
						));
				}	
				//echo CHtml::activeFileField($model->user_car, 'photo1'); ?>
				<input id="Cars_photo<?=$i?>" type="file" name="Cars[photo<?=$i?>]">
			</div>
		<?php }
	} else { 
		for($i=1;$i<8;$i++) { ?>
			<div class="row">
				<label class="required" for="Cars_photo<?=$i?>">photo<?=$i?></label>
				<input id="Cars_photo<?=$i?>" type="file" name="Cars[photo<?=$i?>]">
			</div>
		<?php }
	} ?>

	<?php if(!empty($services_all)) { ?>
		<div class="row">
			<label class="required" for="DriverService">Доп. услуги
				<span class="required">*</span>
			</label>
			<select id="DriverService" name="DriverService[id][]" multiple>
				<?php foreach($services_all as $i => $s) { ?>
					<option value="<?=$i?>"<?php echo(in_array($i, $services_driver)?' selected':'');?>><?=$s?></option>
				<?php } ?>	
			</select>
		</div>
	<?php } ?>
	<?php if(!empty($price_class_all)) { ?>
		<div class="row">
			<label class="required" for="DriverClass">Ценовой класс
				<span class="required">*</span>
			</label>
			<select id="DriverClass" name="DriverClass[id][]" multiple>
				<?php foreach($price_class_all as $i => $s) { ?>
					<option value="<?=$i?>"<?php echo(in_array($i, $price_class_driver)?' selected':'');?>><?=$s?></option>
				<?php } ?>	
			</select>
		</div>
	<?php } ?>
<?php } elseif($type == 3) { ?>
	<div class="row">
		<label class="required" for="Users_password">Пароль
			<span class="required">*</span>
		</label>
		<input type="password" id="Users_password" type="text" value="<?php echo(!empty($model->password)?$model->password:'');?>" name="Users[password]" maxlength="255" size="60">
	</div>
<?php } ?>
<?php if(!empty($statuses)) { ?>
	<div class="row">
		<label class="required" for="Users_type">Статус
			<span class="required">*</span>
		</label>
		<select id="Users_status" name="Users[status]">
			<?php foreach($statuses as $i => $s) { ?>
				<option value="<?=$i?>"<?php echo($model->status == $i?' selected':'');?>><?=$s?></option>
			<?php } ?>	
		</select>
	</div>
<?php } ?>