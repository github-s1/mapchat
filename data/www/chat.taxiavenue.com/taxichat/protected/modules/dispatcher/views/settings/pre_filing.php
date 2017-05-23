<h1>Настройки предварительных заказов</h1>

<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'enableAjaxValidation'=>false,
	'htmlOptions'=>array('enctype'=>'multipart/form-data'),
));  ?>
	<?php foreach ($pre_filing_settings as $i=>$item) { ?>
			<div class="row">
				<label for="setting<?=$item->id?>"><?=$item->descr?></label>
				<input id="setting<?=$item->id?>" type="<?=$item->type?>" value="<?=$item->value?>" name="Settings[<?=$item->id?>][value]">
			</div>
		<?php } ?>
	
	<div class="row buttons">
		<?php echo CHtml::submitButton('Сохранить'); ?>
	</div>

<?php $this->endWidget(); ?>
</div>