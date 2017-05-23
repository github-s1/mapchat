<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.form.js"></script>
<script>
$(document).ready(function() { 
    var options = { 
        target:        '#popup_content',   // target element(s) to be updated with server response 
    }; 
	$('#users-form').ajaxForm(options);
}); 
	
</script>

<div class="pop_users_inner">
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
	<label><span>Логин:</span>
       <?php echo $form->textField($customer,'nickname'); ?>
    </label>
	
	 <label for=""><span>Пароль:</span>
		<?php echo $form->passwordField($customer,'password'); ?>
	</label>
	<label><span>Роль:</span>
		  <?php echo $form->dropDownList($customer, 'id_type',$list_types);?>
	</label>
	 <label><span>E-m@il:</span>
		<?php echo $form->textField($customer,'email'); ?>
	  </label>
	  <label><span>Телефон:</span>
		<?php echo $form->textField($customer,'phone'); ?>
	  </label>
   <div class="s_c">
		<a href="javascript: void(0);" class="pop_cancel" onclick="closePopup()">Отмена</a>
		<?php echo CHtml::submitButton('Отправить', array(
		   'class'=>'pop_push',
		)); ?>
	</div>
	<?php $this->endWidget();  ?>

</div><!-- form -->