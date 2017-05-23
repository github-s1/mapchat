<script src="http://maps.googleapis.com/maps/api/js?sensor=false&amp;libraries=places"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.geocomplete.min.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/geocomplete.js"></script>

<div class="settings_container">
	 <div class="users clearfix">
	 	<div class="search_block">
			<?php $form=$this->beginWidget('CActiveForm', array(
				'id'=>'filter-form',
				'enableAjaxValidation'=>false,
				'htmlOptions'=>array('enctype'=>'multipart/form-data'),
			)); ?>
			<label for="">Строка поиска:<input type="text" size="50" name="filter[adress]" value="<?php echo(!empty($_GET['adress'])?$_GET['adress']:''); ?>"/></label>
			<input class="search_button" type="submit" value="Искать"/>
			<?php $this->endWidget(); ?>
			<a href="<?php echo Yii::app()->request->baseUrl; ?>/admin/addresses/create" class="add_button" data-title="Новое популярное место" data-css_class="pop_chat pop_zone">Добавить</a>
			<div class="clear"></div>
		</div>
		<div class="clearfix"></div>
		<?php if(!empty($addresses)) { ?>
		<table class="table_client">
			<thead>
				<tr>
				  <th>Название</th>
				  <th>Адрес</th>
				  <th></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($addresses as $i => $adr) { ?>
					<tr id="trline_<?=$adr->id?>">
						<td><?=$adr->popular_name?></td>
						<td><?=$adr->name?></td>
						<td>
							<?php $tpr = "if(confirm('Вы уверены?')){
								$.get('".Yii::app()->request->baseUrl."/admin/addresses/delete/id/".$adr->id."', function(data){
									if(data==1){
										$('#trline_".$adr->id."').fadeOut();
									} else {
										alert('Популярное место используется в заказах. Удаление запрещено.');
									} 
								});
							 }  
						return false;"; ?>
							<a href="<?php echo Yii::app()->request->baseUrl; ?>/admin/addresses/delete/id/<?=$adr->id?>" onclick="<?=$tpr?>" class="delete" title="Удалить"></a>
							<a href="<?php echo Yii::app()->request->baseUrl; ?>/admin/addresses/update/id/<?=$adr->id?>" class="edit" title="Редактировать" data-title="Популярное место" data-css_class="pop_chat pop_zone"></a>	
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