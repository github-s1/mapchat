<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.form.js"></script>
<script>
$(document).ready(function() { 
    var options = { 
        target:        '#popup_content',   // target element(s) to be updated with server response 
    }; 
	$('#ajax-form').ajaxForm(options);
	TabsTransitions();
	Reviews(); 
	
}); 
 
	function del_commission(id, obj){
		$.get("<?=Yii::app()->params['siteUrl']?>/admin/drivers/delete_commission/id/"+id, function(data){
			if(data == 1){
				$(obj).parent().parent().remove();
			} else {
				alert("Ошибка! Не удалось удалить платеж");
			}
		});
		return false;
	}

	function addCommission() {
		var id = new Date().getTime();
		$('#driver_commissions tbody').prepend('<tr><td><input type="text" name="commission_add['+id+'][descr]" value=""></td><td><select name="commission_add['+id+'][is_weekly]"><option selected value="0">Ежедневный</option><option value="1">Еженедельный</option></select></td><td><input type="text" name="commission_add['+id+'][value]" value=""><a class="delete" onclick="$(this).parent().parent().remove();" href="javascript: void(0);"></a></td></tr>');	
		return false;
	}
	
	function Reviews() {
		$.ajax({
			url: "<?=Yii::app()->params['siteUrl']?>/admin/drivers/reviews/id/<?=$id?>",
			type: 'get',
			success: function(data){	
				$('#tabs-2').html(data);	
				return false;
			},
			failure:function(){
			}
		});
	}
	
	function TabsTransitions() {
		$('.clearfix li a').click(function () {
			var id_tab = $(this).attr('data-tab');
			
			$('.clearfix li').removeClass('active');
			$(this).parent().addClass('active');
			$('.clearfix').removeClass('active');
			$('#tabs '+id_tab).addClass('active');
			return false;
		}); 
		return false;
	}
	
</script>

  <div id="tabs" class="settings_container tarifs">
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'ajax-form',
		'enableAjaxValidation'=>true,
		'htmlOptions'=>array('enctype'=>'multipart/form-data'),
	));  ?>
	<ul id="inset" class="clearfix">
	  <li class="active"><a href="javascript: void(0);" data-tab="#tabs-1">Основные</a></li>
	  <li><a href="javascript: void(0);" data-tab="#tabs-2">Рейтинг и отзывы</a></li>
	</ul>
	<div id="tabs-2" class="pop_tarif_inner clearfix inv_tab ">
	</div>
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
		<fieldset class="ed_dr_2nd">
		  <legend>Биллинг</legend>
		 
			<label>Тип комиссии с заказа:
				<?php $list_types = array('фиксированая','поценты');
				echo $form->dropDownList($driver, 'is_percent',$list_types);?>
			</label>
			<label>Размер  комиссии с заказа:
				<?php echo $form->textField($driver,'commission'); ?>
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
		<fieldset>
		  <legend>Регулярные латежи водителя</legend>
			<div class="pay_wrapper">
				<table id="driver_commissions">
				  <thead>
					<tr>
					  <th>Описание</th>
					  <th>Переодичность</th>
					  <th>Сумма</th>
					</tr>
				  </thead>
				  <tbody>
					<?php if(!empty($driver_commissions)) {
						foreach($driver_commissions as $comm){  ?>
						<tr>
							<td><input type="text" name="driver_commission[<?=$comm->id?>][descr]" value="<?=$comm->descr?>"></td>
							<td><select name="driver_commission[<?=$comm->id?>][is_weekly]">
								<option <?php echo(!$comm->is_weekly?' selected':''); ?> value="0">Ежедневный</option>
								<option value="1" <?php echo($comm->is_weekly?' selected':''); ?>>Еженедельный</option>
							</td>
							<td><input type="text" name="driver_commission[<?=$comm->id?>][value]" value="<?=$comm->value?>">
								<a href="javascript: void(0);" onclick="del_commission('<?=$comm->id?>',$(this));" class="delete" ></a>
							</td>
						</tr>
						<?php }
					} ?>
					
				  </tbody>
				</table>
			</div>
			<a href="javascript: void(0);" onclick="addCommission();" class="add_button">Добавить платеж</a>
		</fieldset>
	  </div>
	  <div class="ed_photos clearfix">
		<fieldset>
			<legend>Фото водителя</legend>
			<div>
				<p>Водитель</p>
				<div class="img_file" style="background-image: url(<?php echo(!empty($driver->id) && !empty($driver->photo)?Yii::app()->params['siteUrl'].'/images/users/small/'.$driver->photo:Yii::app()->params['siteUrl'].'/img/ed_photo.png');?>);">
                  <div class="img_file_inner">
                   <?php if(!empty($driver->photo)) { ?><a class="img_eye" href="<?php echo(!empty($driver->id) && !empty($driver->photo)?Yii::app()->params['siteUrl'].'/images/users/'.$driver->photo:Yii::app()->params['siteUrl'].'/img/ed_photo.png');?>"></a><?php } ?>
                    <?php echo CHtml::activeFileField($driver, 'photo'); ?>
                  </div>
                </div>
			</div>
			<?php $img_fields = array($car->photo1, $car->photo2, $car->photo3, $car->photo4, $car->photo5, $car->photo6, $car->photo7); ?>
			<div>
				<p>Машина</p>
				<?php for($i = 0; $i < 3; $i++) { ?>
					<div class="img_file" style="background-image: url(<?php echo(!empty($img_fields[$i])?Yii::app()->params['siteUrl'].'/images/cars/small/'.$img_fields[$i]:Yii::app()->params['siteUrl'].'/img/ed_photo.png');?>);">
					  <div class="img_file_inner">
						<?php if(!empty($img_fields[$i])) { ?><a class="img_eye" href="<?php echo(!empty($img_fields[$i])?Yii::app()->params['siteUrl'].'/images/cars/'.$img_fields[$i]:Yii::app()->params['siteUrl'].'/img/ed_photo.png');?>"></a><?php } ?>
						<?php echo CHtml::activeFileField($car, 'photo'.($i+1)); ?>
					  </div>
					</div>
				<?php } ?>
			</div>
			<div>
				<p>Права и документы</p>
				<?php for($i = 3; $i < 7; $i++) { ?>
					<div class="img_file" style="background-image: url(<?php echo(!empty($img_fields[$i])?Yii::app()->params['siteUrl'].'/images/cars/small/'.$img_fields[$i]:Yii::app()->params['siteUrl'].'/img/ed_photo.png');?>);">
					  <div class="img_file_inner">
						<?php if(!empty($img_fields[$i])) { ?><a class="img_eye" href="<?php echo(!empty($img_fields[$i])?Yii::app()->params['siteUrl'].'/images/cars/'.$img_fields[$i]:Yii::app()->params['siteUrl'].'/img/ed_photo.png');?>"></a><?php } ?>
						<?php echo CHtml::activeFileField($car, 'photo'.($i+1)); ?>
					  </div>
					</div>
				<?php } ?>
			</div>
		</fieldset>
	  </div>
	  <div class="ed_ui clearfix">
		<fieldset>
		  <legend>Действия</legend><a href="#" class="msg write_msg"><span></span>Написать</a><a href="#" class="msg call"><span></span>Позвонить</a><a href="#" class="msg audio_msg"><span></span>Аудиосообщение</a>
		</fieldset>
		<div>Баланс: <?=$driver->balance?> грн</div>
	  </div>
		<?php if(Yii::app()->user->hasFlash('success')){ ?>
			<div class="flash_success">
				<p><?=Yii::app()->user->getFlash('success')?></p>
			</div>	
		<?php } ?>
	</div>
	<div class="s_c">
		<a href="javascript: void(0);" class="pop_cancel" onclick="closePopup()">Отмена</a>
		<!--<a href="javascript: void(0);" class="pop_push" id="submit_button">Сохранить</a> -->
		<?php if($id != 0) {
			if(!empty($user_status) && ($user_status->moderation == 2 || $user_status->moderation == 0)) { ?>
				<a href="<?php echo Yii::app()->request->baseUrl; ?>/admin/drivers/activate/id/<?=$id?>" class="pop_push">Активировать</a>
			<?php }  
		} ?>
		
		<?php echo CHtml::submitButton('Сохранить', array(
		   'class'=>'pop_push',
		)); ?>
		<?php /* echo CHtml::ajaxSubmitButton('Сохранить',CHtml::normalizeUrl(array('/admin/drivers/update/id/'.$id)), array(
			'type' => 'POST',
			'update' => '#users-form',
			'success'=>'function(data) 
			{
			  $("#popup_content").html(data);
			  return false;
			}'
		),
		array(
		   'type' => 'submit',
		   'class'=>'pop_push',
		)); */
		?>
	</div>
	<?php $this->endWidget();  ?>
  </div>