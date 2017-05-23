
  <div id="tabs" class="settings_container tarifs">
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'ajax-form',
		'enableAjaxValidation'=>true,
		'htmlOptions'=>array('enctype'=>'multipart/form-data'),
	));  ?>
	<ul id="inset" class="clearfix">
	  <li class="active"><a href="javascript: void(0);" data-tab="#tabs-1">Основные</a></li>
	</ul>
	<div id="tabs-1" class="clearfix ed_driver inv_tab active">
		
		<div class="left_ed_driver">
			<?php  echo $form->errorSummary($driver); ?>
		<fieldset>
			<legend>Личная информация</legend>
			<label>Фамилия:
				<?php echo $form->textField($driver,'surname',array('size'=>60,'maxlength'=>255)); ?>
			</label>
			<label>Имя:
				<?php echo $form->textField($driver,'name',array('size'=>60,'maxlength'=>255)); ?>
			</label>
			<label>Позывной:
				<?php echo $form->textField($driver,'phone'); ?>
			</label>
			<label>E-m@il:
				<?php echo $form->textField($driver,'email',array('size'=>60,'maxlength'=>255)); ?>
			</label>
			<label>Никнейм:
				<?php echo $form->textField($driver,'nickname',array('size'=>60,'maxlength'=>255)); ?>
			</label>
			<label>Пароль:
				<?php echo $form->passwordField($driver,'password'); ?>
			</label>
		  
		</fieldset>
		<fieldset class="ed_dr_options">
		  <legend>Услуги водителя</legend>
		  
		<?php if(!empty($services_all)) { ?>
			<?php foreach($services_all as $i => $s) { ?>	
				<input type="checkbox"<?php echo(in_array($i, $services_driver)?' checked=""':'');?> name="DriverService[<?=$i?>]" id="service<?=$i?>">
				<label for="service<?=$i?>"><?=$s?></label>
			<?php } ?>	
	<?php } ?>
		 
		</fieldset>
	  </div>
	  <div class="right_ed_driver">
		<?php  echo $form->errorSummary($car); ?>
		<fieldset>
		  <legend>Информация о машине</legend>
			<label>Марка:
				<?php echo $form->textField($car,'marka'); ?>
			</label>
			<label>Модель:
				<?php echo $form->textField($car,'model'); ?>
			</label>
			
			<label>Цвет:
				<?php echo $form->textField($car,'color'); ?>
			</label>
			<label>Госномер:
				<?php echo $form->textField($car,'number'); ?>
			</label>
			<label>Тип кузова:
				<?php echo $form->dropDownList($car, 'id_bodytype',$bodytype_all);?>
			</label>
		 
		</fieldset>
		<fieldset class="ed_driver_class">
			<legend>Класс</legend>
			<?php echo $form->radioButtonList($driver, 'id_price_class',$price_class_all, array('separator'=>'&nbsp;'));?> 
		</fieldset>
	  </div>
	  <div class="ed_photos clearfix">
		<fieldset>
			<legend>Фото водителя</legend>
			<div>
				<p>Водитель</p>
				<div class="img_file" style="background-image: url(<?php echo(!empty($driver->photo)?Yii::app()->params['siteUrl'].'/images/users_temp/small/'.$driver->photo:Yii::app()->params['siteUrl'].'/img/ed_photo.png');?>);">
                  <div class="img_file_inner">
                   <?php if(!empty($driver->photo)) { ?><a class="img_eye" href="<?php echo(!empty($driver->photo)?Yii::app()->params['siteUrl'].'/images/users_temp/'.$driver->photo:Yii::app()->params['siteUrl'].'/img/ed_photo.png');?>"></a><?php } ?>
                    <?php echo CHtml::activeFileField($driver, 'photo'); ?>
                  </div>
                </div>
			</div>
			<?php $img_fields = array($car->photo1, $car->photo2, $car->photo3, $car->photo4, $car->photo5, $car->photo6, $car->photo7); ?>
			<div>
				<p>Машина</p>
				<?php for($i = 0; $i < 3; $i++) { ?>
					<div class="img_file" style="background-image: url(<?php echo(!empty($img_fields[$i])?Yii::app()->params['siteUrl'].'/images/cars_temp/small/'.$img_fields[$i].'?rnd='.time():Yii::app()->params['siteUrl'].'/img/ed_photo.png');?>);">
					  <div class="img_file_inner">
						<?php if(!empty($img_fields[$i])) { ?><a class="img_eye" href="<?php echo(!empty($img_fields[$i])?Yii::app()->params['siteUrl'].'/images/cars_temp/'.$img_fields[$i]:Yii::app()->params['siteUrl'].'/img/ed_photo.png');?>"></a><?php } ?>
						<?php echo CHtml::activeFileField($car, 'photo'.($i+1)); ?>
					  </div>
					</div>
				<?php } ?>
			</div>
			<div>
				<p>Права и документы</p>
				<?php for($i = 3; $i < 7; $i++) { ?>
					<div class="img_file" style="background-image: url(<?php echo(!empty($img_fields[$i])?Yii::app()->params['siteUrl'].'/images/cars_temp/small/'.$img_fields[$i].'?rnd='.time():Yii::app()->params['siteUrl'].'/img/ed_photo.png');?>);">
					  <div class="img_file_inner">
						<?php if(!empty($img_fields[$i])) { ?><a class="img_eye" href="<?php echo(!empty($img_fields[$i])?Yii::app()->params['siteUrl'].'/images/cars_temp/'.$img_fields[$i]:Yii::app()->params['siteUrl'].'/img/ed_photo.png');?>"></a><?php } ?>
						<?php echo CHtml::activeFileField($car, 'photo'.($i+1)); ?>
					  </div>
					</div>
				<?php } ?>
			</div>
		</fieldset>
	  </div>
	  <div class="ed_ui clearfix">
		<div>Баланс: <?=$driver->balance?> грн</div>
	  </div>
	</div>
	<div class="s_c">
		<a href="javascript: void(0);" class="pop_cancel" onclick="closePopup()">Отмена</a>
		<a href="<?php echo Yii::app()->request->baseUrl; ?>/admin/drivers/cancel_changes/id/<?=$id?>" class="pop_banned">Отклонить</a>
		
		<?php echo CHtml::submitButton('Промодерировать', array(
		   'class'=>'pop_push',
		)); ?>
	</div>
	<?php $this->endWidget();  ?>
  </div>