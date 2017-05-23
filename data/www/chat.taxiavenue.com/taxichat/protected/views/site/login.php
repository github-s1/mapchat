<?php
/* @var $this SiteController */
/* @var $model LoginForm */
/* @var $form CActiveForm  */

$this->pageTitle=Yii::app()->name . ' - Login'; ?>

<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'login-form',
	'enableClientValidation'=>true,
	'clientOptions'=>array(
		'validateOnSubmit'=>true,
	),
)); ?>


	<div class="row">
		<?php echo $form->labelEx($model,'username'); ?>
		<?php echo $form->textField($model,'username'); ?>
		
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'password'); ?>
		<?php echo $form->passwordField($model,'password'); ?>
		
	</div>

	<div class="row rememberMe">
		<?php echo $form->checkBox($model,'rememberMe'); ?>
		<?php echo $form->label($model,'rememberMe'); ?>
		
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Войти', array('class'=>'pop_push')); ?>
	</div>

<?php $this->endWidget(); ?>
</div><!-- form -->
<div class="errorsLogin">
	<?php echo $form->error($model,'username'); ?>
	<?php echo $form->error($model,'password'); ?>
	<?php echo $form->error($model,'rememberMe'); ?>
</div>