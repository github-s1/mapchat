<h1>Оповещение клиента</h1>

<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'enableAjaxValidation'=>false,
	'htmlOptions'=>array('enctype'=>'multipart/form-data'),
));  ?>
	<?php foreach ($messages_settings as $i=>$item) { ?>
			<h2><?=$item->descr?></h2>
			<div class="row">
				<label>Включено</label>
				<input type="checkbox" <?php echo($item->type == 1?' checked':''); ?> value="<?=$item->type?>" name="Settings[<?=$item->id?>][type]">
				<label>Шаблон оповещения</label>
				<textarea name="Settings[<?=$item->id?>][value]"><?=$item->value?></textarea>
			</div>
		<?php } ?>
	
	<div class="row buttons">
		<?php echo CHtml::submitButton('Сохранить'); ?>
	</div>

<?php $this->endWidget(); ?>
</div>