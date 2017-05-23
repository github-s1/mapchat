
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
	<input type="hidden" value="<?php echo(isset($model->car)?$model->car->id:'');?>" name="Cars[id]">
	<div class="row">
		<label class="required" for="Models_marka">Марка
			<span class="required">*</span>
		</label>
		<select id="Models_marka" name="Models[id_marka]">
			<?php foreach($marks_all as $i => $marka) { ?>
				<option <?php echo(isset($model->car->model->marka)&&$model->car->model->marka->id == $i?'selected':''); ?> value="<?=$i?>"><?=$marka?></option>
			<?php } ?>
		</select>
	</div>
	<div id="features_model"></div>
	<div class="row">
		<label class="required" for="Cars_color">Цвет
			<span class="required">*</span>
		</label>
		<input id="Cars_color" type="text" value="<?php echo(isset($model->car)?$model->car->color:'');?>" name="Cars[color]" maxlength="255" size="60">
	</div>

	<?php if(isset($model->car)) { 
		$img_fields = array('1' =>$model->car->photo1, '2' => $model->car->photo2, '3' => $model->car->photo3, '4' => $model->car->photo4, '5' => $model->car->photo5, '6' => $model->car->photo6, '7' => $model->car->photo7);
		foreach($img_fields as $i => $field) { ?> 
			<div class="row">
				<label class="required" for="Cars_photo<?=$i?>">photo<?=$i?></label>
				<?php 
				if(!empty($field)) {
					echo CHtml::image(Yii::app()->params['siteUrl'].'/images/cars/'.$model->car->id.'/photo'.$i.'.'.$field, '',
						array(
						'width'=>'200',
						'class'=>'image',
						));
				}	
				//echo CHtml::activeFileField($model->car, 'photo1'); ?>
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
<?php } elseif($type >= 3) { ?>
	<div class="row">
		<label class="required" for="Users_password">Пароль
			<span class="required">*</span>
		</label>
		<input type="password" id="Users_password" type="text" value="<?php echo(!empty($model->password)?$model->password:'');?>" name="Users[password]" maxlength="255" size="60">
	</div>
<?php } ?>