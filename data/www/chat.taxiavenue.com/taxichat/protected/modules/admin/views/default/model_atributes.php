<script type="text/javascript">
$(function(){
	$('#new_model').click(function(){
		$('#form_new_model').show();
		$('#select_model').hide();
		$('#is_new_model').val(1);
		return false;
	});
	
	$('#cancel').click(function(){
		$('#form_new_model').hide();
		$('#select_model').show();
		$('#is_new_model').val(0);
		return false;
	});
});
</script>
	<input type="hidden" value="<?php echo(isset($model->car)?$model->car->id:'');?>" name="Cars[id]">
	<div class="row" id="select_model">
		<label class="required" for="Cars_marka">Модель
			<span class="required">*</span>
		</label>
		<select id="Models_marka" name="Cars[id_model]">
			<?php foreach($all_models_mark as $i => $m) { ?>
				<option <?php echo(isset($model->car->model)&&$model->car->model->id == $i?'selected':''); ?> value="<?=$i?>"><?=$m?></option>
			<?php } ?>
		</select>
		<a href="javascript: void(0);" id="new_model">Задать свою</a>
	</div>
	<div id="form_new_model" style="display:none;">
		<input type="hidden" value="0" name="Models[is_new_model]" id="is_new_model">
		<div class="row">
			<label class="required" for="Models_name">Модель
			<span class="required">*</span>
		</label>
		<input id="Models_name" type="text" name="Models[name]" maxlength="255" size="60">
		</div>
		<div class="row">
			<label class="required" for="Models_bodytype">Тип кузова
				<span class="required">*</span>
			</label>
			<select id="Models_bodytype" name="Models[id_bodytype]">
				<?php foreach($bodytypes_all as $i => $b) { ?>
					<option value="<?=$i?>"><?=$b?></option>
				<?php } ?>
			</select>
		</div>
		<a href="javascript: void(0);" id="cancel">Отмена</a>
	</div>