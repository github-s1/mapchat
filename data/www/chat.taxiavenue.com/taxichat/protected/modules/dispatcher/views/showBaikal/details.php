<meta charset="UTF-8"/>
<?php if (!empty($baikal)){    ?>

<table> 
<tr> 
    <td><h2> Байкал № <?=$baikal?> </h2></td> 
</tr>

<tr>
   <td> 
     <h3>Пришли на помошь:</h3>
      <ul>
       <?php 
         if (!empty($helpers)){
         	$g=0;
            foreach ($helpers as $hp) { ?>
             <li><a href="" onclick="window.open('/taxi/taxichat/admin/drivers/edit_moderation/id/<?=$helpersID[$g]?>')"> <?=$hp?> </a></li>	

            <?php $g++; } ?>
         <?php }else{ ?>
           <p> Данных нет </p>
         <?php } ?>
      </ul>
    </td>
     <td> 
      <h3>Отказались прийти на помошь:</h3>
        <ul>
         <?php 
           if (!empty($nonHelpers)){
           	$g=0;
               foreach ($nonHelpers as $nhp) { ?>
                 <li><a href="window.open('/taxi/taxichat/admin/drivers/edit_moderation/id/<?=$helpersID[$g]?>')"> <?=$nhp?> </a></li>	

               <?php $g++;} ?>
           <?php }else{ ?>
             <p> Данных нет </p>
           <?php } ?>
        </ul>
      </td>


</tr>



</table>
<?php }else{  ?>

<h1> Данных нет </h1>

<?php } ?>