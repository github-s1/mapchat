<h1>База клиентов</h1>
<div class="search_block"><!--search_block-->
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'filter-form',
		'enableAjaxValidation'=>false,
		'htmlOptions'=>array('enctype'=>'multipart/form-data'),
	)); ?>
		<label for="">Строка поиска:<input type="text" size="50" name="filter[customer]" value="<?php echo(!empty($_GET['customer'])?$_GET['customer']:''); ?>"/></label>
		<input class="search_button" type="submit" value="Искать"/>
	<?php $this->endWidget(); ?>
	
	<a href="javascript: void(0);" class="add_button popup" onclick="popup('<?php echo Yii::app()->request->baseUrl; ?>/admin/customers/create', 'Новый клиент', 'pop_customers'); return false;">Создать новый</a>
	<div class="clear"></div>
	<?php if(Yii::app()->user->hasFlash('success')){ ?>
		<div class="flash_success">
			<p><?=Yii::app()->user->getFlash('success')?></p>
		</div>	
	<?php } ?>
</div><!--search_block_end-->
<?php if(!empty($customers)) { ?>
<table>
	<thead>
	<tr>
		<th>Статус</th>
		<th>Ник</th>
		<th>Логин</th>
		<th></th>
		<th></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach($customers as $i => $cs) { ?>
		<tr class="<?php echo($cs->moderation == 0?'new':'');?>" id="trline_<?=$cs->user->id?>">
			<td><?php echo($cs->moderation == 0?'Забанен':($cs->id_status == 1?'В сети':$cs->status->name));?></td>
			<td><?=$cs->user->nickname?></td>
			<td><?=$cs->user->phone?></td>
			<td><a class="msg write_msg" href="#"><span></span>Написать</a><a class="msg call" href="#"><span></span>Позвонить</a><a class="msg audio_msg" href="#"><span></span>Аудиосообщение</a></td>
			<td>
				<a href="javascript: void(0);" class="edit_with_bg popup" title="Редактировать" onclick="popup('<?php echo Yii::app()->request->baseUrl; ?>/admin/customers/update/id/<?=$cs->user->id?>', 'Клиент', 'pop_customers'); return false;"></a>
				
				<?php $tpr = "if(confirm('Вы уверены?')){
								$.get('".Yii::app()->request->baseUrl."/admin/customers/delete/id/".$cs->user->id."', function(data){
									if(data==1){
										$('#trline_".$cs->user->id."').fadeOut();
									} else {
										alert('Не удается удалить клиента.');
									} 
								});
							 }  
						return false;"; ?>
				
				<a href="javascript: void(0);" onclick="<?=$tpr?>" class="delete_with_bg" title="Удалить"></a>
				
				<?php if($cs->moderation != 0 ) { ?>
					<?php $tpr = "if(confirm('Вы уверены?')){
                                $.get('".Yii::app()->request->baseUrl."/dispatcher/customers/AddToBlackList/id/".$cs->user->id."', function(data){
                                    if(data==1){
                                        $('#trline_".$cs->user->id."').fadeOut();
                                    } else {
                                        alert('Выполняется заказ клиента. На данный момент его бан не возможен.');
                                    } 
                                });
                             }  
                    return false;"; ?>
                    <a href="javascript: void(0);" onclick="<?=$tpr?>" class="black-list" title="Добавить в чёрный список"></a>
				<?php } ?>
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
