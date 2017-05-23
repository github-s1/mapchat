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
 <?php if(!empty($drivers)): ?>
<table class="black-list">
	<thead>
	<tr>
	    <th>ID</th>
		<th>Ник</th>
		<th>Логин</th>
		<th></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach($drivers as $i => $cs) { ?>
		<tr class="<?php echo($i%2!=0?'active':'');?>" id="trline_<?=$cs->user->id?>">
		    <td><?=$cs->user->id?></td>
			<td><?=$cs->user->nickname?></td>
			<td><?=$cs->user->name?></td>
			<td>
				<?php $tpr = "if(confirm('Вы уверены?')){
                                $.get('".Yii::app()->request->baseUrl."/dispatcher/customers/RemoveFromBlackList/id/".$cs->user->id."', function(data){
                                    if(data==1){
                                        $('#trline_".$cs->user->id."').fadeOut();
                                    } else {
                                        alert('Не удается активировать водителя.');
                                    } 
                                });
                             }  
                    return false;"; ?>
				<a href="<?php echo Yii::app()->request->baseUrl; ?>/dispatcher/customers/RemoveFromBlackList/id/<?=$cs->user->id?>" onclick="<?=$tpr?>" class="white-list" title="Убрать из чёрного списка"></a>
			</td>
		
		</tr>
	<?php } ?>
	</tbody>
</table>
<?php $this->widget('MyLinkPager', array(
				   'pages' => $pages,
			    )) ?>
<?php else: ?>

<table class="black-list">
	<thead>
	<tr>
	    <th>ID</th>
		<th>Ник</th>
		<th>Логин</th>
		<th></th>
	</tr>
	</thead>
	<tbody>
		<tr>
		    <td colspan="4">Нет результатов</td>
		</tr>
	</tbody>
</table>

<?php endif; ?>