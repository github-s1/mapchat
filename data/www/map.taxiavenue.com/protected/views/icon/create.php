<?php echo CHtml::form('','post',array('enctype'=>'multipart/form-data')); ?>

<?php echo CHtml::activeFileField($model, 'name'); ?>
<?php echo CHtml::submitButton('Загрузить'); ?>
<?php echo CHtml::endForm(); ?>