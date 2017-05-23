<div class="settings_container">
  <div class="messages clearfix">
	<form action="">
	  <fieldset>
		<legend>Сообщение в чат всем</legend>
		<label>Текст сообщения:
		  <textarea></textarea>
		</label><br>
		<input type="checkbox" id="client">
		<label for="client" class="labClient">Клиент</label>
		<input type="checkbox" id="driver" checked>
		<label for="driver" class="labDriver">Водитель</label>
		<button type="submit" class="save_set clearfix">Сохранить</button>
	  </fieldset>
	</form>
  </div>
  <div class="messages second clearfix">
	<fieldset>
	  <legend>Оповещение клиента</legend>
	  
	  <?php foreach ($messages_settings as $i=>$item) { ?>
			<fieldset>
				<legend><?=$item->descr?></legend>
				<?php $form=$this->beginWidget('CActiveForm', array(
					'enableAjaxValidation'=>false,
					'htmlOptions'=>array('enctype'=>'multipart/form-data'),
				));  ?>
				<fieldset>
				  <legend>Сообщение в чат</legend>
					<input type="checkbox" id="service<?=$item->id?>" <?php echo($item->type == 1?' checked':''); ?> value="<?=$item->type?>" name="Settings[<?=$item->id?>][type]">
					<label for="service<?=$item->id?>">Включено</label>
					<br/>
					<label>Шаблон оповещения::
					  <textarea name="Settings[<?=$item->id?>][value]"><?=$item->value?></textarea>
					</label>
				</fieldset>
				<?php echo CHtml::submitButton('Сохранить', array(
				   'class'=>'save_set clearfix',
				)); ?>
				<?php $this->endWidget(); ?>
			</fieldset>
		<?php } ?>
	</fieldset>
  </div>
</div>