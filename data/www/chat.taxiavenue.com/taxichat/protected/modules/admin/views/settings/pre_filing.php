<div class="settings_container">
  <div class="fines flow clearfix">
	<?php $form=$this->beginWidget('CActiveForm', array(
		'enableAjaxValidation'=>false,
		'htmlOptions'=>array('enctype'=>'multipart/form-data'),
	));  ?>
	  <fieldset>
		<legend>Настройка предварительных заказов</legend>
		<?php foreach ($pre_filing_settings as $i=>$item) { ?>
			<label for="setting<?=$item->id?>"><?=$item->descr?>
				<input id="setting<?=$item->id?>" type="<?=$item->type?>" value="<?=$item->value?>" name="Settings[<?=$item->id?>][value]">
			</label>
		<?php } ?>
		
		 <?php echo CHtml::submitButton('Сохранить', array(
		   'class'=>'save_set',
		)); ?>
	  </fieldset>
	<?php $this->endWidget(); ?>
  </div>
</div>