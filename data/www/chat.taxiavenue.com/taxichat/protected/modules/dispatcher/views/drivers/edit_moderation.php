<?php
$this->breadcrumbs=array('Водители'=>array('index'), 'Модерация изменений');
$this->menu=array(
	array('label'=>'Водители', 'url'=>array('index')),
);

echo('<h1>Модерация изменений</h1>');
?>


<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'users-form',
	'enableAjaxValidation'=>false,
	'htmlOptions'=>array('enctype'=>'multipart/form-data'),
));  ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php  echo $form->errorSummary($driver); ?>
	
	<div class="row">
		<?php echo $form->labelEx($driver,'phone'); ?>
		<?php echo $form->textField($driver,'phone',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($driver,'phone'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($driver,'name'); ?>
		<?php echo $form->textField($driver,'name',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($driver,'name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($driver,'surname'); ?>
		<?php echo $form->textField($driver,'surname',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($driver,'surname'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($driver,'email'); ?>
		<?php echo $form->textField($driver,'email',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($driver,'email'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($driver,'nickname'); ?>
		<?php echo $form->textField($driver,'nickname',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($driver,'nickname'); ?>
	</div>

    <div class="row">
        <?php echo $form->labelEx($driver,'password'); ?>
        <?php echo $form->textField($driver,'password'); ?>
        <?php echo $form->error($driver,'password'); ?>
    </div>

	<div class="row">
		
		<?php 
		echo $form->labelEx($driver,'photo');
		if(!empty($driver->photo)) {
			echo CHtml::image(Yii::app()->params['siteUrl'].'/images/users_temp/'.$driver->photo, $driver->name,
				array(
				'width'=>'200',
				'class'=>'image',
				));
		}	
		?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($driver,'dop_info'); ?>
		<?php echo $form->textArea($driver,'dop_info'); ?>
		<?php echo $form->error($driver,'dop_info'); ?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($car,'marka'); ?>
		<?php echo $form->textField($car,'marka'); ?>
		<?php echo $form->error($car,'marka'); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($car,'model'); ?>
		<?php echo $form->textField($car,'model'); ?>
		<?php echo $form->error($car,'model'); ?>
	</div>
	<?php if(!empty($bodytype_all)) { ?>
		<div class="row">
			<?php echo $form->labelEx($car,'id_bodytype'); ?>
			<?php echo $form->dropDownList($car, 'id_bodytype',$bodytype_all);?>
			<?php echo $form->error($car,'id_bodytype'); ?>
		</div>
	<?php } ?>
	<div class="row">
		<?php echo $form->labelEx($car,'color'); ?>
		<?php echo $form->textField($car,'color'); ?>
		<?php echo $form->error($car,'color'); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($car,'number'); ?>
		<?php echo $form->textField($car,'number'); ?>
		<?php echo $form->error($car,'number'); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($car,'year'); ?>
		<?php echo $form->textField($car,'year'); ?>
		<?php echo $form->error($car,'year'); ?>
	</div>
	<?php $img_fields = array('1' =>$car->photo1, '2' => $car->photo2, '3' => $car->photo3, '4' => $car->photo4, '5' => $car->photo5, '6' => $car->photo6, '7' => $car->photo7);
	foreach($img_fields as $i => $field) {  
		switch ($i) {
			case 1:
				echo('<label>Фото автомобиля</label>');
				break;
			case 4:
				echo('<label>Фото документов</label>');
				break;
			case 6:
				echo('<label>Фото прав</label>');
				break;
		} ?> 
		<div class="row">
			<?php 
			if(!empty($field)) {
				echo CHtml::image(Yii::app()->params['siteUrl'].'/images/cars_temp/'.$field, '',
					array(
					'width'=>'200',
					'class'=>'image',
					));
			}	
			?>
		</div>
	<?php } ?>
	<?php if(!empty($services_all)) { ?>
		<div class="row">
			<label class="required" for="DriverService">Доп. услуги</label>
			<select id="DriverService" name="DriverService[id][]" multiple>
				<?php foreach($services_all as $i => $s) { ?>
					<option value="<?=$i?>"<?php echo(in_array($i, $services_driver)?' selected':'');?>><?=$s?></option>
				<?php } ?>	
			</select>
		</div>
	<?php } ?>
	<?php if(!empty($price_class_all)) { ?>
		<div class="row">
			<?php echo $form->labelEx($driver,'id_price_class'); ?>
			<?php echo $form->radioButtonList($driver, 'id_price_class',$price_class_all);?>
			<?php echo $form->error($driver,'id_price_class'); ?>
		</div>
	<?php } ?>
	
	<div class="row buttons">
		<?php echo CHtml::submitButton('Сохранить'); ?>
	</div>

<?php $this->endWidget(); ?>
</div><!-- form -->