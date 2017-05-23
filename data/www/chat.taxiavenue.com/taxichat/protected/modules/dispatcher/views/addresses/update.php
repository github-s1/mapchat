<script src="http://maps.googleapis.com/maps/api/js?sensor=false&amp;libraries=places"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.geocomplete.min.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/geocomplete.js"></script>

<?php
if($id != 0)
	$this->breadcrumbs=array('Популярные места'=>array('index'), 'Редактирование');
else
	$this->breadcrumbs=array('Популярные места'=>array('index'), 'Новый адрес');
$this->menu=array(
	array('label'=>'Популярные места', 'url'=>array('index')),
);

if($id != 0) 
	echo('<h1>Редактирование адреса</h1>');
else
	echo('<h1>Новый адрес</h1>');
?>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'addresses-form',
	'enableAjaxValidation'=>false,
)); ?>

    <div class="map_canvas" style="width: 600px; height: 400px;"></div>
    <a id="reset" href="#" style="display:none;">Reset Marker</a>

	<?php echo $form->errorSummary($model); ?>
	<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>60,'maxlength'=>255, 'id'=>'geocomplete', 'class'=>'geocomplete', 'placeholder'=>'Введите адресс', 'autocomplete'=>'off')); ?>
        <input id="find" type="button" value="find" />
		<?php echo $form->error($model,'name'); ?>
	</div>

    <div class="row">
        <?php echo $form->labelEx($model,'latitude'); ?>
       <input class="latitude" name="lat" type="text" value="">
        <?//php echo $form->textField($model,'latitude',array('size'=>60,'maxlength'=>255, 'class'=>'latitude')); ?>
        <?php echo $form->error($model,'latitude'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'longitude'); ?>
        <input class="longitude" name="lng" type="text" value="">
        <?//php echo $form->textField($model,'longitude',array('size'=>60,'maxlength'=>255, 'class'=>'longitude')); ?>
        <?php echo $form->error($model,'longitude'); ?>
    </div>

	<div class="row">
		<?php echo $form->labelEx($model,'popular_name'); ?>
		<?php echo $form->textField($model,'popular_name',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'popular_name'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->