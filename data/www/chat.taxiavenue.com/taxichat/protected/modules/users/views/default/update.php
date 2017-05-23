<script type="text/javascript">
$(function(){
	var type = $("#Users_type").val();
		loadResult(type);
		
	$('#Users_type').change(function(){
		var id_type = $("#Users_type").val();
		loadResult(id_type);
	});
});


function loadResult(id_type){
	var params = new Object();
	params['id_type'] = id_type;
	$.ajax({
		url: '<?=Yii::app()->params['siteUrl']?>/users/type_atributes/<?=$id?>',
		type: 'post',
		data: params,
		success: function(data){
			$('#features_type').html(data);
			return false;
		}
	});
}
</script>
<?php
/* @var $this UsersController */
/* @var $model Users */
$this->breadcrumbs=array(
	'Users'=>array('index'),
	$model->name=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Users', 'url'=>array('index')),
	array('label'=>'Create Users', 'url'=>array('create')),
	array('label'=>'View Users', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Users', 'url'=>array('admin')),
);
?>

<h1>Update Users <?php echo $model->id; ?></h1>

<?php
/* @var $this UsersController */
/* @var $model Users */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'users-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
	'htmlOptions'=>array('enctype'=>'multipart/form-data'),
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'type'); ?>
		<?php echo $form->dropDownList($model, 'type',$list_types);?>
		<?php echo $form->error($model,'type'); ?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($model,'phone'); ?>
		<?php echo $form->textField($model,'phone'); ?>
		<?php echo $form->error($model,'phone'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'surname'); ?>
		<?php echo $form->textField($model,'surname',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'surname'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'patronymic'); ?>
		<?php echo $form->textField($model,'patronymic',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'patronymic'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'email'); ?>
		<?php echo $form->textField($model,'email',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'email'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'nickname'); ?>
		<?php echo $form->textField($model,'nickname',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'nickname'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'balance'); ?>
		<?php echo $form->textField($model,'balance',array('size'=>8,'maxlength'=>8)); ?>
		<?php echo $form->error($model,'balance'); ?>
	</div>

	<div class="row">
		
		<?php 
		if(!empty($model->id) && !empty($model->photo)) {
			echo CHtml::image(Yii::app()->params['siteUrl'].'/images/users/'.$model->id.'.'.$model->photo, $model->name,
				array(
				'width'=>'200',
				'class'=>'image',
				));
		}	
		echo CHtml::activeFileField($model, 'photo'); ?>
	</div>
	
	<div id="features_type"></div>
	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->