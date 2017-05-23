<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.form.js"></script>
<script>
$(document).ready(function() { 
    var options = { 
        target:        '#popup_content',   // target element(s) to be updated with server response 
    }; 
	$('#users-form').ajaxForm(options);
}); 
</script>

<div class="form red_user">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'users-form',
	'enableAjaxValidation'=>false,
	'htmlOptions'=>array('enctype'=>'multipart/form-data'),
));  ?>

	<?php  echo $form->errorSummary($customer); ?>
	<?php if(Yii::app()->user->hasFlash('success')){ ?>
		<div class="flash_success">
			<p><?=Yii::app()->user->getFlash('success')?></p>
		</div>	
	<?php } ?>
	<div class="row">
		<?php echo $form->labelEx($customer,'surname'); ?>
		<?php echo $form->textField($customer,'surname',array('maxlength'=>255)); ?>
		<?php echo $form->error($customer,'surname'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($customer,'name'); ?>
		<?php echo $form->textField($customer,'name',array('maxlength'=>255)); ?>
		<?php echo $form->error($customer,'name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($customer,'phone'); ?>
		<?php echo $form->textField($customer,'phone'); ?>
		<?php echo $form->error($customer,'phone'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($customer,'nickname'); ?>
		<?php echo $form->textField($customer,'nickname',array('maxlength'=>255)); ?>
		<?php echo $form->error($customer,'nickname'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($customer,'password'); ?>
		<?php echo $form->passwordField($customer,'password',array('maxlength'=>255)); ?>
		<?php echo $form->error($customer,'password'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($customer,'email'); ?>
		<?php echo $form->textField($customer,'email',array('maxlength'=>255)); ?>
		<?php echo $form->error($customer,'email'); ?>
	</div>

	<div class="row clearfix">
	<?php echo $form->labelEx($customer,'photo');?>
		<div class="img_file" style="background-image: url(<?php echo(!empty($customer->photo)?Yii::app()->params['siteUrl'].'/images/users/small/'.$customer->photo:Yii::app()->params['siteUrl'].'/img/ed_photo.png');?>);">
			<div class="img_file_inner">
				<?php if(!empty($customer->photo)) { ?><a class="img_eye" href="<?php echo(!empty($customer->photo)?Yii::app()->params['siteUrl'].'/images/users/'.$customer->photo:Yii::app()->params['siteUrl'].'/img/ed_photo.png');?>"></a><?php } ?>
				<?php echo CHtml::activeFileField($customer, 'photo'); ?>
			</div>
		</div>
	</div>
	<?php /*
	$src = ImageHelper::thumb(100, 100, Yii::getPathOfAlias('webroot.images').'/users/32.png');
	$src = CHtml::image(Yii::app()->request->hostInfo . '/' . $src, $this->name, array('title'=>$this->name));
	*/ ?>
	<div class="row dop_info">
		<?php echo $form->labelEx($customer,'dop_info'); ?>
		<?php echo $form->textArea($customer,'dop_info'); ?>
		<?php echo $form->error($customer,'dop_info'); ?>
	</div>
	
	<div class="row buttons">
		<a href="javascript: void(0);" class="pop_cancel" onclick="closePopup()">Отмена</a>
		<?php echo CHtml::submitButton('Сохранить', array(
		   'class'=>'pop_push',
		)); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->