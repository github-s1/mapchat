<script type="text/javascript" language="javascript">
	function addPriceClass() {
		var id = new Date().getTime();
		$('#filter').prepend('<div class="row">Название <input type="text" name="PriceClass_new['+id+'][name]"> Тип <select name="PriceClass_new['+id+'][is_percent]"><option value="0">+</option><option value="1">%</option></select> Значение <input type="text" maxlength="8" name="PriceClass_new['+id+'][value]"><a class="delete" onclick="$(this).parent().remove();" href="javascript: void(0);"></a></div>');	
	}
</script>


<div class="settings_container">
    <div class="fines">
        <?php $form=$this->beginWidget('CActiveForm', array(
			'enableAjaxValidation'=>false,
			'htmlOptions'=>array('enctype'=>'multipart/form-data'),
		));  ?>
		<a class="add_button" href="javascript:void(0);" onclick="addPriceClass();">Добавить</a>
		<div class="clearfix"></div>
              <fieldset id="filter">
                <legend>Ценовые классы</legend>
				
				<?php foreach ($price_class as $i=>$item) { ?>
					<div class="row">
						Название <input type="text" name="PriceClass[<?=$item->id?>][name]" value="<?=$item->name?>">
						Тип <select name="PriceClass[<?=$item->id?>][is_percent]">
								<option <?php echo(!$item->is_percent?' selected':''); ?> value="0">+</option>
								<option <?php echo($item->is_percent?' selected':''); ?> value="1">%</option>
							</select>
						Значение <input type="text" maxlength="8" name="PriceClass[<?=$item->id?>][value]" value="<?=$item->value?>">
					</div>
				<?php } ?>
				 <?php echo CHtml::submitButton('Сохранить', array(
				   'class'=>'save_set',
				)); ?>
               
              </fieldset>
            <?php $this->endWidget(); ?>
    </div>
</div>
