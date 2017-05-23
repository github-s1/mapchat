<?php /* @var $this Controller */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />

	<!-- blueprint CSS framework -->
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/reset.css" media="screen, projection" />
	<link href="<?php echo Yii::app()->request->baseUrl; ?>/css/jquery-ui.min.css" rel="stylesheet" media="screen">
	
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css" />

    <!--<link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl; ?>/css/jquery-ui.css">-->
	<?php 
	Yii::app()->getClientScript()->registerCoreScript('jquery');
	Yii::app()->getClientScript()->registerCoreScript('jquery.ui');
	Yii::app()->clientScript->registerPackage('datetimepicker');
	Yii::app()->getClientScript()->registerCoreScript('fancybox');
	Yii::app()->clientScript->registerPackage('custom');  
	?>
	
	<title>TaxiChat Administration</title>
</head>

<body>
<?php if (Yii::app()->user->checkAccess('4') || Yii::app()->user->checkAccess('5')) { ?>
	<script type="text/javascript">

	setInterval(function()
	{
		$.ajax({
			type: "POST",
			url: "<?=Yii::app()->params['siteUrl']?>/dispatcher/default/showNewBaikal",
			success: function(data){
			 if (data!=0){
			  alert("Baikal " + data);
			  }

			 // var a =document.getElementsByClassName('denger');
			//  a.href = "/driver_application/Baikal/View/?id=" + data;
			}
		});
	}, 30000);

</script>
<?php } ?>
<header><!--header-->
    <p class="logo">Таксичат</p>
	<nav>
	<?php if (Yii::app()->user->checkAccess('3')){ 		
		$this->widget('MainMenu',array(
			'items'=>array(
				array('label'=>'Архив заказов', 'url'=>array('/admin/orders/order_archive')),
				array('label'=>'База водителей', 'url'=>array('/admin/drivers/index')),
				array('label'=>'Чёрный список', 'url'=>array('/dispatcher/customers/BlackList')),
				array('label'=>'База клиентов', 'url'=>array('/admin/customers/index')),
				array('label'=>'Карта водителей', 'url'=>array('/admin/drivers/drivers_map')),
				array('label'=>'Статистика', 'url'=>array('/admin/statistics/index')),
				array('label'=>'Настройки', 'url'=>array('/admin/settings/tariffs')),
				array('label'=>'Выйти', 'url'=>array('/site/logout'), 'visible'=>!Yii::app()->user->isGuest),
				array('label'=>'Account', 'url'=>array('/admin/users/update/id/'.Yii::app()->user->id), 'visible'=>!Yii::app()->user->isGuest)
			),
		)); 
	} elseif(Yii::app()->user->checkAccess('4')){
		$this->widget('MainMenu',array(
			'items'=>array(
				array('label'=>'Заказы', 'url'=>array('/dispatcher/orders/index')),
				array('label'=>'Архив заказов', 'url'=>array('/admin/orders/order_archive')),
				array('label'=>'База водителей', 'url'=>array('/admin/drivers/index')),
				array('label'=>'База клиентов', 'url'=>array('/dispatcher/customers/index')),
				array('label'=>'Чёрный список', 'url'=>array('/dispatcher/customers/BlackList')),
				array('label'=>'Карта водителей', 'url'=>array('/admin/drivers/drivers_map')),
				array('label'=>'Статистика', 'url'=>array('/admin/statistics/index')),
				array('label'=>'Настройки', 'url'=>array('/admin/settings/tariffs')),
				array('label'=>'Байкал', 'url'=>array('/dispatcher/ShowBaikal/index')),
				array('label'=>'Выйти', 'url'=>array('/site/logout'), 'visible'=>!Yii::app()->user->isGuest),
				array('label'=>'Account', 'url'=>array('/admin/users/update/id/'.Yii::app()->user->id), 'visible'=>!Yii::app()->user->isGuest)
			),
		));
    }elseif(Yii::app()->user->checkAccess('5')){
              $this->widget('MainMenu',array(
			'items'=>array(
				array('label'=>'Заказы', 'url'=>array('/dispatcher/orders/index')),
				array('label'=>'Выйти', 'url'=>array('/site/logout'), 'visible'=>!Yii::app()->user->isGuest),
				array('label'=>'Account', 'url'=>array('/admin/users/update/id/'.Yii::app()->user->id), 'visible'=>!Yii::app()->user->isGuest)
			),
		));
    }elseif(Yii::app()->user->checkAccess('7')){
           $this->widget('MainMenu',array(
			'items'=>array(
				array('label'=>'Архив заказов', 'url'=>array('/admin/orders/order_archive')),
				array('label'=>'База водителей', 'url'=>array('/admin/drivers/index')),
				array('label'=>'Чёрный список', 'url'=>array('/dispatcher/customers/BlackList')),
				array('label'=>'База клиентов', 'url'=>array('/admin/customers/index')),
				array('label'=>'Карта водителей', 'url'=>array('/admin/drivers/drivers_map')),
				array('label'=>'Статистика', 'url'=>array('/admin/statistics/index')),
				array('label'=>'Настройки', 'url'=>array('/admin/settings/tariffs')),
				array('label'=>'Выйти', 'url'=>array('/site/logout'), 'visible'=>!Yii::app()->user->isGuest),
				array('label'=>'Account', 'url'=>array('/admin/users/update/id/'.Yii::app()->user->id), 'visible'=>!Yii::app()->user->isGuest)
			),
		)); 
    	}elseif(Yii::app()->user->checkAccess('6')){
    		  $this->widget('MainMenu',array(
			   'items'=>array(
				array('label'=>'Заказы', 'url'=>array('/agent/orders/index')),
				array('label'=>'База водителей', 'url'=>array('/agent/drivers/index')),
				array('label'=>'Настройки', 'url'=>array('/agent/users/index')), 
				array('label'=>'Выйти', 'url'=>array('/site/logout'), 'visible'=>!Yii::app()->user->isGuest),
				array('label'=>'Account', 'url'=>array('/admin/users/update/id/'.Yii::app()->user->id), 'visible'=>!Yii::app()->user->isGuest)
			  ),
   		     )); 
     		} ?>
		<div class="clear"></div>
	</nav>	
</header><!--header_end-->

<section class="wrapper"><!--wrapper-->
    <h1>Настройки</h1>
	<div class="settings">
		<div class="menu_settings">
		<?php if (!Yii::app()->user->checkAccess('6')){ ?>
			<?php $this->widget('SettingsMenu',array(
			'items'=>array(
				array('label'=>'Тарифы', 'url'=>array('/admin/settings/tariffs')),
				array('label'=>'Тарифные зоны', 'url'=>array('/admin/zones/index')),
				array('label'=>'Популярные места', 'url'=>array('/admin/addresses/index')),
				array('label'=>'Штрафы', 'url'=>array('/admin/settings/fines')),
				array('label'=>'Предварительная подача', 'url'=>array('/admin/settings/pre_filing')),
				array('label'=>'Оповещения', 'url'=>array('/admin/settings/messages')),
				array('label'=>'Пользователи', 'url'=>array('/admin/users/index')),
				array('label'=>'Постоянные клиенты', 'url'=>array('/dispatcher/settings/permanent')),
				array('label'=>'Ценовые классы авто', 'url'=>array('/admin/settings/price_class')),
				array('label'=>'Оценки водителей', 'url'=>array('/admin/settings/driver_evaluations')),
				array('label'=>'Ограничения доступа', 'url'=>array('/admin/settings/restrictions_access')),
			),
		)); ?>
	    <?php }else{ ?>
              <?php $this->widget('SettingsMenu',array(
			    'items'=>array(
				  array('label'=>'Пользователи', 'url'=>array('/agent/users/index')),
				  array('label'=>'Комиссия', 'url'=>array('/agent/settings/commission')),
			),
		)); ?>
	    <?php } ?>
		</div>
		
			<?php echo $content; ?>

        <div class="clear"></div>
	</div>	
	
</section><!--wrapper_end-->
<footer>
</footer>
</body>
</html>