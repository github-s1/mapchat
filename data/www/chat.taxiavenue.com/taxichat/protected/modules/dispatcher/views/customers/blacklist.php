<?php
/* @var $this UsersController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Чёрный список',
);
?>
<h1> Чёрный список </h1>
<?php if(!empty($id)): ?>
    <?php if($status==0): ?>
           <h2> Пользователь с ID <?php echo $id; ?> добавлен в чёрный список </h2>
    <?php elseif($status==1): ?>
           <h2> Пользователь с ID <?php echo $id; ?> удалён из чёрного списка </h2>
    <?php endif; ?>
<?php endif; ?>

<div id="tabsNew" class="orders_tables">
  <ul class="tabs">
    <li><a href="#tabsOne" onclick="getTables('CustomersBlacklist', '#tabsOne');">Клиенты <span class="new_orders"> </span></a> </li>
    <li ><a href="#tabsTwo" onclick="getTables('DriversBlacklist', '#tabsTwo');">Водители<span class="pre_orders"> </span></a> </li>
    </ul>
     <div id="tabsOne"></div>
     <div id="tabsTwo"></div>
</div>

<script>
 function getTables(url, elem)
    {
      $.ajax({
      type: "POST",
      url: "<?=Yii::app()->params['siteUrl']?>/dispatcher/customers/"+url+"/ajax/1",
       success: function(data)
       {
        $(elem).html(data); 
        return false;
       }
     });
  }
  $(document).ready(function() { 
		getTables('CustomersBlacklist', '#tabsOne');
	}); 
	
</script>
 
