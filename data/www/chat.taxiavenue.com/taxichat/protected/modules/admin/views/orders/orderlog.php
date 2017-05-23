 <table class="orderStory">
	<thead>
	<tr>
	    <th><span>Дата</span></th>
		<th><span>Диспетчер</span></th>
		<th><span>Поле</span></th>
		<th><span>Было</span></th>
		<th><span>Стало</span></th>
	</tr>
	</thead>
	<?php if(!empty($orderLog)){ ?>

<?php foreach ($orderLog as $log): ?>
	<tbody>
	<tr> 
	   <td><?=$log['date']?></td>
       <td><?=$log['creator']?></td>
       <td><?=$log['type']?></td>
       <td><?=$log['old']?></td>
       <td><?=$log['new']?></td>   
    </tr>
<?php endforeach; ?>

<?php }else{ ?>
<tr> 
   <td colspan="5"> История пуста </td>
</tr>
<?php } ?>
</tbody>
</table>