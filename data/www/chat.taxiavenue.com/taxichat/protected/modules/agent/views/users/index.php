 <div class="settings_container">
	 <div class="users clearfix">
	 	<a href="javascript: void(0);" class="add_button popup" onclick="popup('<?php echo Yii::app()->request->baseUrl; ?>/agent/users/create', 'Новый пользователь', 'pop_users'); return false;">Создать новый</a>
		<div class="clearfix"></div>
	<?php if(!empty($users)) { ?>
		<table class="table_client">
			<thead>
				<tr>
				  <th>Логин</th>
				  <th>Роль</th>
				  <th>E-m@il</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($users as $i => $cs) { ?>
					<tr id="trline_<?=$cs->id?>">
						<td><?=$cs->nickname?></td>
						<td><?=$cs->type->name?></td>
						<td><?=$cs->email?>
							<?php $tpr = "if(confirm('Вы уверены?')){
								$.get('".Yii::app()->request->baseUrl."/agent/users/delete/id/".$cs->id."', function(data){
									if(data==1){
										$('#trline_".$cs->id."').fadeOut();
									} else {
										alert('Не удается удалить пользователя.');
									} 
								});
							}  
							return false;"; ?>
							<a href="<?php echo Yii::app()->request->baseUrl; ?>/agent/users/delete/id/<?=$cs->id?>" onclick="<?=$tpr?>" class="delete" title="Удалить"></a>		
							<a href="javascript: void(0);" class="edit popup" title="Редактировать" onclick="popup('<?php echo Yii::app()->request->baseUrl; ?>/agent/users/update/id/<?=$cs->id?>', 'Пользователи', 'pop_users'); return false;"></a>
						</td>
					</tr>
				<?php } ?>
			
		  </tbody>
		</table>
	<?php $this->widget('MyLinkPager', array(
		'pages' => $pages,
	)) ?>	
	<?php } else { 
		echo('<p>Нет результатов</p>'); 
	} ?>
  </div>
 </div>