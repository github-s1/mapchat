<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.form.js"></script>
<script>
$(document).ready(function() { 
    var options = { 
        target:        '#popup_content',   // target element(s) to be updated with server response 
    }; 
	$('#addresses-form').ajaxForm(options);
}); 
</script>



<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'addresses-form',
	'enableAjaxValidation'=>false,
	'htmlOptions'=>array('enctype'=>'multipart/form-data'),
)); ?>

<div class="chat_inner pop_zone">
	<?php if(Yii::app()->user->hasFlash('success')){ ?>
			<div class="flash_success">
				<p><?=Yii::app()->user->getFlash('success')?></p>
			</div>	
		<?php } ?>
	<?php echo $form->errorSummary($model); ?>
	  <label>Название:
		<?php echo $form->textField($model,'popular_name'); ?>
	  </label>
	  <label>Адресс:
		<?php echo $form->textField($model,'name',array('id'=>'geocomplete', 'class'=>'geocomplete', 'placeholder'=>'Введите адресс', 'autocomplete'=>'off')); ?>
	  </label>
	  <label>Долгота:
		 <input class="latitude" name="lat" type="text" value="">
	  </label>
	   <label>Широта:
		 <input class="longitude" name="lng" type="text" value="">
	  </label>
	  

	<div id="map_canvas" style="width: 603px; height: 404px;"></div>
</div>
<div class="s_c">
	<a href="javascript: void(0);" class="pop_cancel" onclick="closePopup()">Отмена</a>
	<?php echo CHtml::submitButton('Сохранить', array(
	   'class'=>'pop_push',
	)); ?>
</div>
<?php $this->endWidget();  ?>
