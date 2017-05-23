<h1>Новый отзыв</h1>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'review-form',
	'enableAjaxValidation'=>false,
	'htmlOptions'=>array('enctype'=>'multipart/form-data'),
));  ?>

	<?php  echo $form->errorSummary($review); ?>
	
	<div class="row">
		<?php echo $form->labelEx($review,'id_driver'); ?>
		<?php echo $form->dropDownList($review, 'id_driver',$drivers);?>
		<?php echo $form->error($review,'id_driver'); ?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($review,'id_customer'); ?>
		<?php echo $form->dropDownList($review, 'id_customer',$customers);?>
		<?php echo $form->error($review,'id_customer'); ?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($review,'id_evaluation'); ?>
		<?php echo $form->dropDownList($review, 'id_evaluation',$evaluations);?>
		<?php echo $form->error($review,'id_evaluation'); ?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($review,'text'); ?>
		<?php echo $form->textArea($review,'text'); ?>
		<?php echo $form->error($review,'text'); ?>
	</div>
	
	<div class="row buttons">
		<?php echo CHtml::submitButton('Сохранить'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->