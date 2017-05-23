
<?php
if($id != 0)
	$this->breadcrumbs=array('Пользователи'=>array('index'), $customer->name=>array('view','id'=>$customer->id),'Редактирование');
else
	$this->breadcrumbs=array('Пользователи'=>array('index'), 'Новый пользователь');
$this->menu=array(
	array('label'=>'Пользователи', 'url'=>array('index')),
);

if($id != 0) 
	echo('<h1>Редактирование данных пользователя</h1>');
else
	echo('<h1>Новый пользователь</h1>');
?>
<?php
/* @var $this UsersController */
/* @var $model Users */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'users-form',
	'enableAjaxValidation'=>false,
	'htmlOptions'=>array('enctype'=>'multipart/form-data'),
));  ?>

	<?php  echo $form->errorSummary($customer); ?>
	
    <div class="row">
        <?php echo $form->labelEx($customer,'id_type'); ?>
        <?php $list_types = array(3=>'Администратор',4=>'Диспетчер');
        echo $form->dropDownList($customer, 'id_type',$list_types);?>
        <?php echo $form->error($customer,'id_type'); ?>
    </div>
	
	<div class="row">
		<?php echo $form->labelEx($customer,'phone'); ?>
		<?php echo $form->textField($customer,'phone'); ?>
		<?php echo $form->error($customer,'phone'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($customer,'name'); ?>
		<?php echo $form->textField($customer,'name',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($customer,'name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($customer,'surname'); ?>
		<?php echo $form->textField($customer,'surname',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($customer,'surname'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($customer,'email'); ?>
		<?php echo $form->textField($customer,'email',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($customer,'email'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($customer,'nickname'); ?>
		<?php echo $form->textField($customer,'nickname',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($customer,'nickname'); ?>
	</div>

    <div class="row">
        <?php echo $form->labelEx($customer,'password'); ?>
        <input type="password" name="Users[password_new]" value=""/>
        <?//php echo CHtml::activePasswordField($customer,'password_new',array('size'=>60,'maxlength'=>255)); ?>
        <?//php echo $form->error($customer,'password'); ?>
    </div>
	<div class="row">
		
		<?php 
		echo $form->labelEx($customer,'photo');
		if(!empty($customer->id) && !empty($customer->photo)) {
			echo CHtml::image(Yii::app()->params['siteUrl'].'/images/users/'.$customer->photo, $customer->name,
				array(
				'width'=>'200',
				'class'=>'image',
				));
		}	
		echo CHtml::activeFileField($customer, 'photo'); ?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($customer,'dop_info'); ?>
		<?php echo $form->textArea($customer,'dop_info'); ?>
		<?php echo $form->error($customer,'dop_info'); ?>
	</div>
	
	<div class="row buttons">
		<?php echo CHtml::submitButton('Сохранить'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->