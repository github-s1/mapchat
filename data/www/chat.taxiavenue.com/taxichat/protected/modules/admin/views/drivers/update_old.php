<script type="text/javascript" language="javascript">
	function del_commission(id, obj){
		$.get("<?=Yii::app()->params['siteUrl']?>/admin/drivers/delete_commission/id/"+id, function(data){
			if(data == 1){
				$(obj).parent().remove();
			} else {
				alert("Ошибка! Не удалось удалить платеж");
			}
		});
		return false;
	}
	
	function addCommission() {
		var id = new Date().getTime();
		$('#filter').prepend('<div class="group"><strong>Новый платеж</strong><input name="commission_add['+id+'][value]" style="width: 50px;" class="text small" value=""><select name="commission_add['+id+'][is_weekly]"><option selected value="0">Ежедневный</option><option value="1">Еженедельный</option></select><input name="commission_add['+id+'][descr]" size="3" style="width: 300px;" class="text small" value=""><a class="delete" onclick="$(this).parent().remove();" href="javascript: void(0);">удалить</a></div>');	
	}
</script>
<?php
if($id != 0)
	$this->breadcrumbs=array('Водители'=>array('index'), 'Редактирование');
else
	$this->breadcrumbs=array('Водители'=>array('index'), 'Новый водитель');
$this->menu=array(
	array('label'=>'Водители', 'url'=>array('index')),
);

if($id != 0) 
	echo('<h1>Редактирование данных водителя</h1>');
else
	echo('<h1>Новый водитель</h1>');
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

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php  echo $form->errorSummary($driver); ?>
	<?php  echo $form->errorSummary($car); ?>
	
	<div class="row">
		<?php echo $form->labelEx($driver,'phone'); ?>
		<?php echo $form->textField($driver,'phone'); ?>
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
		<?php echo $form->labelEx($driver,'balance'); ?>
		<?php echo $form->textField($driver,'balance',array('size'=>8,'maxlength'=>8)); ?>
		<?php echo $form->error($driver,'balance'); ?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($driver,'rating'); ?>
		<?php echo $form->textField($driver,'rating',array('size'=>8,'maxlength'=>8)); ?>
		<?php echo $form->error($driver,'rating'); ?>
	</div>

    <div class="row">
        <?php echo $form->labelEx($driver,'password'); ?>
        <?php echo $form->textField($driver,'password'); ?>
        <?php echo $form->error($driver,'password'); ?>
    </div>

	<div class="row">
		
		<?php 
		echo $form->labelEx($driver,'photo');
		if(!empty($driver->id) && !empty($driver->photo)) {
			echo CHtml::image(Yii::app()->params['siteUrl'].'/images/users/'.$driver->photo, $driver->name,
				array(
				'width'=>'200',
				'class'=>'image',
				));
		}	
		echo CHtml::activeFileField($driver, 'photo'); ?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($driver,'dop_info'); ?>
		<?php echo $form->textArea($driver,'dop_info'); ?>
		<?php echo $form->error($driver,'dop_info'); ?>
	</div>
	
	<input type="hidden" value="<?php echo(isset($driver->car->id)?$driver->car->id:'');?>" name="Cars[id]">
	
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
				echo CHtml::image(Yii::app()->params['siteUrl'].'/images/cars/'.$field, '',
					array(
					'width'=>'200',
					'class'=>'image',
					));
			}	
			echo CHtml::activeFileField($car, 'photo'.$i); ?>
		</div>
	<?php } ?>
	<?php if(!empty($services_all)) { ?>
		<div class="row">
			<!--<label class="required" for="DriverService">Доп. услуги</label> 
			<select id="DriverService" name="DriverService[id][]" multiple>-->
				<?php foreach($services_all as $i => $s) { ?>
						
					<input type="checkbox"<?php echo(in_array($i, $services_driver)?' checked=""':'');?> name="DriverService[id][<?=$i?>]">
					<label><?=$s?></label>
				<?php } ?>	
			<!--</select> -->
		</div>
	<?php } ?>
	<?php if(!empty($price_class_all)) { ?>
		<div class="row">
			<?php echo $form->labelEx($driver,'id_price_class'); ?>
			<?php echo $form->radioButtonList($driver, 'id_price_class',$price_class_all);?>
			<?php echo $form->error($driver,'id_price_class'); ?>
		</div>
	<?php } ?>
	
	<div class="row">
		<?php echo $form->labelEx($driver,'is_percent'); ?>
		<?php $list_types = array('фиксированая','поценты');
		echo $form->dropDownList($driver, 'is_percent',$list_types);?>
		<?php echo $form->error($driver,'is_percent'); ?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($driver,'commission'); ?>
		<?php echo $form->textField($driver,'commission'); ?>
		<?php echo $form->error($driver,'commission'); ?>
	</div>
	
	<div>
		<h2>Регулярные платежи водителя</h2>
		<a href="javascript: void(0);" onclick="addCommission();">Добавить платеж</a>
		<div id="filter">
			<?php foreach($driver_commissions as $comm){  ?>
			<div class="group">
				<input name="driver_commission[<?=$comm->id?>][value]" size="3" style="width: 50px;" class="text small" value="<?=$comm->value?>">
				<select name="driver_commission[<?=$comm->id?>][is_weekly]">
					<option <?php echo(!$comm->is_weekly?' selected':''); ?> value="0">Ежедневный</option>
					<option value="1" <?php echo($comm->is_weekly?' selected':''); ?>>Еженедельный</option>
				</select>
				<input name="driver_commission[<?=$comm->id?>][descr]" size="3" style="width: 300px;" class="text small" value="<?=$comm->descr?>">
				<a href="javascript: void(0);" onclick="del_commission('<?=$comm->id?>',$(this));" class="delete" >удалить</a>
			</div>
			<?php }?>
		</div>
	</div>
	<?php if($id != 0) { 
		if($driver->balance > 0) {?>
			<h2>Добавить штраф</h2>
			<div class="row">
				<?php echo $form->labelEx($new_fine,'value'); ?>
				<?php echo $form->textField($new_fine,'value'); ?>
				<?php echo $form->error($new_fine,'value'); ?>
			</div>
			<div class="row">
				<?php echo $form->labelEx($new_fine,'descr'); ?>
				<?php echo $form->textArea($new_fine,'descr'); ?>
				<?php echo $form->error($new_fine,'descr'); ?>
			</div>
		<?php } ?>
		<?php if(!empty($payments_history)) {?>
			<h2>История отчислений/начислений</h2>
			<table cellspacing="0" cellpadding="0">
				<thead><tr>
						<th>Дата</th>
						<th>Тип</th>
						<th>Сумма</th>
						<th>Баланс</th>
						<th>Рейтинг</th>
						<th>Описание</th>
				</tr></thead>
				<tbody>
				<?php foreach($payments_history as $p) { ?>
					<tr>
						<td><?=date("Y-m-d H:i:s", strtotime($p->date_create))?></td>
						<td><?=$p->type->name?></td>
						<td><?=$p->value?></td>
						<td><?=$p->balance?></td>
						<td><?=$p->rating?></td>
						<td><?=$p->descr?></td>
					</tr>
				<?php } ?>	
				</tbody>
			</table>
		<?php } ?>
		<h2>Отзывы</h2>
		<?php if(!empty($reviews_driver)) {?>	
			<table cellspacing="0" cellpadding="0">
				<thead><tr>
						<th>Дата</th>
						<th>Клиент</th>
						<th>Оценка</th>
						<th>Рейтинг</th>
						<th>Отзыв</th>
				</tr></thead>
				<tbody>
				<?php foreach($reviews_driver as $review) { ?>
					<tr>
						<td><?=$review->date_review?></td>
						<td><?=$review->customer->phone?></td>
						<td><?=$review->evaluation->name?></td>
						<td><?=$review->rating?></td>
						<td><?=$review->text?></td>
					</tr>
				<?php } ?>	
				</tbody>
			</table>
		<?php } else 
			echo('<p>Отзывов нет</p>'); ?>
		
	<?php } ?>
	
	<div class="row buttons">
		<?php echo CHtml::submitButton('Сохранить'); ?>
	</div>

<?php $this->endWidget(); 
if($id != 0) {
	if(!empty($user_status) && ($user_status->moderation == 2 || $user_status->moderation == 0)) { ?>
		<a href="../../activate/id/<?=$id?>">Активировать</a>
	<?php } 
	if(!empty($user_status) && ($user_status->moderation != 0)) { ?>
		<a href="../../banned/id/<?=$id?>">Забанить</a>
	<?php } 
} ?>
	
</div><!-- form -->