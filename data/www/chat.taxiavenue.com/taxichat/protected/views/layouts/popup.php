<?php 
	
?>
	
	
   
	
	<title>TaxiChat Administration</title>
</head>

<body>

<header><!--header-->
    <p class="logo">Таксичат</p>
	<nav>
	<?php 
	$this->widget('MyMenu',array(
			'items'=>array(
				array('label'=>'Архив заказов', 'url'=>array('/admin/orders/order_archive')),
				array('label'=>'База водителей', 'url'=>array('/admin/drivers/index')),
				array('label'=>'База клиентов', 'url'=>array('/admin/customers/index')),
				array('label'=>'Карта водителей', 'url'=>array('/admin/drivers/drivers_map')),
				array('label'=>'Статистика', 'url'=>array('/admin/statistics/index')),
				array('label'=>'Настройки', 'url'=>array('/admin/settings/tariffs')),
				array('label'=>'Выйти', 'url'=>array('/site/logout'), 'visible'=>!Yii::app()->user->isGuest),
				array('label'=>'Account', 'url'=>array('/users/update/id/'.Yii::app()->user->id), 'visible'=>!Yii::app()->user->isGuest)
			),
		)); ?>
		
		<div class="clear"></div>
	</nav>	
</header><!--header_end-->

<section class="wrapper"><!--wrapper-->
	<?php echo $content; ?>

</section><!--wrapper_end-->
<footer>
</footer>
</body>
</html>
