<script src="http://maps.googleapis.com/maps/api/js?sensor=false&amp;libraries=places"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.geocomplete.min.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/geocomplete.js"></script>

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
	<a id="reset" href="#" style="display:none;">Reset Marker</a>
</div>
<div class="s_c">
	<?php echo CHtml::submitButton('Сохранить', array(
	   'class'=>'pop_push',
	)); ?>
</div>
<?php $this->endWidget();  ?>
