<h1>Оценки водителей</h1>
<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'enableAjaxValidation'=>false,
	'htmlOptions'=>array('enctype'=>'multipart/form-data'),
));  ?>
    <div id="filter">
    <?php foreach ($evaluations as $i=>$item) { ?>
    <div class="row">
        Название <input type="text" maxlength="255" name="Evaluations[<?=$item->id?>][name]" value="<?=$item->name?>">
        Значение <input type="text" maxlength="8" name="Evaluations[<?=$item->id?>][value]" value="<?=$item->value?>">
    </div>
    <?php } ?>
    </div>
	
    <div class="row buttons">
            <?php echo CHtml::submitButton('Сохранить'); ?>
    </div>

<?php $this->endWidget(); ?>
</div>