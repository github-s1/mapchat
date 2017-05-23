<div class="settings_container">
    <div class="fines">
        <?php $form=$this->beginWidget('CActiveForm', array(
			'enableAjaxValidation'=>false,
			'htmlOptions'=>array('enctype'=>'multipart/form-data'),
		));  ?>
              <fieldset class="driver_evaluations">
                <legend>Оценки водителей</legend>
				<?php foreach ($evaluations as $i=>$item) { ?>
					
					<label for="setting_name<?=$item->id?>">Название
						<input id="setting_name<?=$item->id?>" type="text" value="<?=$item->name?>" name="Evaluations[<?=$item->id?>][name]">
					</label>
					<label for="setting_val<?=$item->id?>">Значение
						<input id="setting_val<?=$item->id?>" type="text" value="<?=$item->value?>" name="Evaluations[<?=$item->id?>][value]">
					</label>
				<?php } ?>
				 <?php echo CHtml::submitButton('Сохранить', array(
				   'class'=>'save_set',
				)); ?>
               
              </fieldset>
            <?php $this->endWidget(); ?>
    </div>
</div>
