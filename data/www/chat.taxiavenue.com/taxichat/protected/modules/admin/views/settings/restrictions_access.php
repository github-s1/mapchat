<div class="settings_container">
  <div class="users clearfix">
	<?php $form=$this->beginWidget('CActiveForm', array(
			'enableAjaxValidation'=>false,
			'htmlOptions'=>array('enctype'=>'multipart/form-data'),
		));  ?>
	  <fieldset>
		<legend>Ограничения доступа</legend>
		<?php foreach ($access_settings as $i=>$item) { ?>
			<label for="setting<?=$item->id?>"><?=$item->descr?>
				<textarea id="setting<?=$item->id?>" name="Settings[<?=$item->id?>][value]"><?=$item->value?></textarea>
			</label>
		<?php } ?>
		
		<?php echo CHtml::submitButton('Сохранить', array(
		   'class'=>'save_set clearfix',
		)); ?>
	  </fieldset>
	<?php $this->endWidget(); ?>
  </div>
</div>