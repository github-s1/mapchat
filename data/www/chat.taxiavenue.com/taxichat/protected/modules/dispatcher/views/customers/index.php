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
	<a href="javascript: void(0);" onclick="popup('<?php echo Yii::app()->request->baseUrl; ?>/dispatcher/customers/create', 'Новый клиент', 'pop_chat pop_zone pop_rating'); return false;" class="add_button popup" data-title="Новый клиент" data-css_class="pop_customers">Создать новый</a>
	<div class="clear"></div>
</div><!--search_block_end-->
<div id="tabsNew" class="orders_tables">
  <ul class="tabs">
    <li><a href="#tabsOne" onclick="getTables('customers_all', '#tabsOne');">Все клиенты </a> </li>
    <li><a href="#tabsTwo" onclick="getTables('Customers_permanent', '#tabsTwo');">Постоянные клиенты </a> </li>
    </ul>
     <div id="tabsOne"></div>
     <div id="tabsTwo"></div>
</div>

<script>
 function getTables(url, elem)
    {
      $.ajax({
      type: "POST",
      url: "<?=Yii::app()->params['siteUrl']?>/dispatcher/customers/"+url+"/ajax/1<?php echo(isset($_GET['customer']) && $_GET['customer'] != ''?'/customer/'.$_GET['customer']:'');?>",
       success: function(data)
       {
        $(elem).html(data); 
        return false;
       }
     });
  }
  $(document).ready(function() { 
		getTables('customers_all', '#tabsOne');
	}); 
	
</script>