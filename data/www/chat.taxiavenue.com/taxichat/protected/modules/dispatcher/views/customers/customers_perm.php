<script>
	$(document).ready(function() { 
		ClickPaginate();
	}); 
	
	function ClickPaginate() {
		$('#tabsTwo .pagination a').click(function () {
			var url = $(this).attr('href')+"/ajax/1";
			$.ajax({
				url: url,
				type: 'get',
				success: function(data){	
					$('#tabsTwo').html(data);	
					return false;
				},
				failure:function(){
				}
			});
			return false;
		});
		return false;
	}	
</script> 


<?php if(!empty($customers)) { ?>
<table class="orders">
	<thead>
	<tr>
		<th>Ник</th>
		<th>Логин</th>
		<th></th>
		<th></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach($customers as $i => $cs) { ?>
		<tr class="<?php echo($i%2!=0?'active':'');?>" id="trline_<?=$cs->id?>">
			<td><?=$cs->nickname?></td>
			<td><?=$cs->phone?></td>
			<td>
				<a class="msg write_msg" href="#"><span></span>Написать</a>
				<a class="msg call" href="#"><span></span>Позвонить</a>
				<a class="msg audio_msg" href="#"><span></span>Аудиосообщение</a>
			</td>
			<td>
				<a onclick="popup('<?php echo Yii::app()->request->baseUrl; ?>/dispatcher/customers/update/id/<?=$cs->id?>', 'Редактировать', 'pop_chat pop_zone pop_rating'); return false;" class="edit_with_bg popup" title="Редактировать" data-title="Клиенты" data-css_class="pop_customers"></a>
				<a href="<?php echo Yii::app()->request->baseUrl; ?>/dispatcher/customers/delete/id/<?=$cs->id?>" class="delete_with_bg" title="Удалить"></a>
		    	<?php $tpr = "if(confirm('Вы уверены?')){
							$.get('".Yii::app()->request->baseUrl."/dispatcher/customers/AddToBlackList/id/".$cs->id."', function(data){
								if(data==1){
									$('#trline_".$cs->id."').fadeOut();
								} else {
									alert('Выполняется заказ клиента. На данный момент его бан не возможен.');
								} 
							});
						 }  
				return false;"; ?>
				<a href="javascript: void(0);" onclick="<?=$tpr?>" class="black-list" title="Добавить в чёрный список"></a>
		    	<a href="<?php echo Yii::app()->request->baseUrl; ?>/dispatcher/customers/PermanentUsers/id/<?=$cs->id?>" class="skidka" title="Cформировать скидку"></a>
		    </td>
		</tr>
	<?php } ?>
	</tbody>
</table>
<?php $this->widget('MyLinkPager', array(
				   'pages' => $pages,
			    )) ?>
<?php } else {
	echo('	<table class="orders"> 
				<thead>
					<tr>
						<th>Ник</th>
						<th>Логин</th>
						<th></th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td collspan="4">Нет результатов</td>
					</tr>
				</tbody>
			</table>');
} ?>