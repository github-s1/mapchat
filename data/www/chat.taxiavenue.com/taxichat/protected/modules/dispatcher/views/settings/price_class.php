<script type="text/javascript" language="javascript">
	function addPriceClass() {
		var id = new Date().getTime();
		$('#filter').prepend('<div class="row">Название <input type="text" maxlength="255" name="PriceClass_new['+id+'][name]">Тип <select name="PriceClass_new['+id+'][is_percent]"><option value="0">+</option><option value="1">%</option></select>Значение <input type="text" maxlength="8" name="PriceClass_new['+id+'][value]"><a class="delete" onclick="$(this).parent().remove();" href="javascript: void(0);">удалить</a></div>');	
	}
</script>
<h1>Ценовые классы авто</h1>
<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'enableAjaxValidation'=>false,
	'htmlOptions'=>array('enctype'=>'multipart/form-data'),
));  ?>
		<a href="javascript:void(0);" onclick="addPriceClass();">Добавить</a>
		<div id="filter">
			<?php foreach ($price_class as $i=>$item) { ?>
			<div class="row">
				Название <input type="text" maxlength="255" name="PriceClass[<?=$item->id?>][name]" value="<?=$item->name?>">
				Тип <select name="PriceClass[<?=$item->id?>][is_percent]">
						<option <?php echo(!$item->is_percent?' selected':''); ?> value="0">+</option>
						<option <?php echo($item->is_percent?' selected':''); ?> value="1">%</option>
					</select>
				Значение <input type="text" maxlength="8" name="PriceClass[<?=$item->id?>][value]" value="<?=$item->value?>">
			</div>
		<?php } ?>
		</div>
	
	<div class="row buttons">
		<?php echo CHtml::submitButton('Сохранить'); ?>
	</div>

<?php $this->endWidget(); ?>
</div>