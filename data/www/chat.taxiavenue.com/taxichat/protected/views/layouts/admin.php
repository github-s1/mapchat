<?php /* @var $this Controller */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="language" content="en" />

    <!-- blueprint CSS framework -->
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/reset.css" media="screen, projection" />
    <!--<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/jquery-ui.css" media="print" /> -->
     <link href="<?php echo Yii::app()->request->baseUrl; ?>/css/jquery-ui.min.css" rel="stylesheet" media="screen">

    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/jquery.fancybox.css" />
    <script src="<?php echo Yii::app()->request->baseUrl; ?>/js/dispatcher.js"></script>
    <script src="<?php echo Yii::app()->request->baseUrl; ?>/js/sequre.js"></script>
	
	
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
  <script> 
     $(document).ready(function() {
        preventSelection(document);
      });
  </script>
<?php if (Yii::app()->user->checkAccess('4') || Yii::app()->user->checkAccess('5')) { ?>
	<script type="text/javascript">

	setInterval(function()
	{
            $.ajax({
                type: "POST",
                url: "<?=Yii::app()->params['siteUrl']?>/dispatcher/default/showNewBaikal",
                success: function(data){
                    if (data != 0){
                        alert("Baikal " + data);
                    }
                }
            });
	}, 30000);
    function mainCount()
    {
	        $.ajax({
                type: "POST",
                url: "<?=Yii::app()->params['siteUrl']?>/dispatcher/orders/MainCounter",
                success: function(data){
                    var result = JSON.parse(data);
                    var span = document.getElementById('count_span');
                     if (result.count == 0)
                     { 
                       span.className = "";                    
                     }else{
                       span.className = "new_drivers";
                       span.innerHTML =  result.count;
                    }
                }
	        });
	}
	setInterval(mainCount(),30000);

</script>
<?php } ?>
<header><!--header-->
    <p class="logo">Таксичат - <?php echo(Yii::app()->user->checkAccess('3')?'Админ панель':(Yii::app()->user->checkAccess('4')?'Диспетчерская':(Yii::app()->user->checkAccess('5')?'Оператор':(Yii::app()->user->checkAccess('6')?'Агент панель':(Yii::app()->user->checkAccess('7')?'Учредитель':(Yii::app()->user->checkAccess('8')?'Диспетчер(Агент)':'')))))); ?></p>
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
				array('label'=>'Архив заказов', 'url'=>array('/dispatcher/orders/order_archive')),
				array('label'=>'База водителей', 'url'=>array('/admin/drivers/index')),
				array('label'=>'База клиентов', 'url'=>array('/dispatcher/customers/index')),
				array('label'=>'Чёрный список', 'url'=>array('/dispatcher/customers/BlackList')),
				array('label'=>'Карта водителей', 'url'=>array('/admin/drivers/drivers_map')),
				array('label'=>'Байкал', 'url'=>array('/dispatcher/ShowBaikal/index')),
				array('label'=>'Выйти', 'url'=>array('/site/logout'), 'visible'=>!Yii::app()->user->isGuest),
				array('label'=>'Account', 'url'=>array('/admin/users/update/id/'.Yii::app()->user->id), 'visible'=>!Yii::app()->user->isGuest)
			),
		));
    } elseif(Yii::app()->user->checkAccess('5')){
              $this->widget('MainMenu',array(
			'items'=>array(
				array('label'=>'Заказы', 'url'=>array('/dispatcher/orders/index')),
				array('label'=>'Выйти', 'url'=>array('/site/logout'), 'visible'=>!Yii::app()->user->isGuest),
				array('label'=>'Account', 'url'=>array('/admin/users/update/id/'.Yii::app()->user->id), 'visible'=>!Yii::app()->user->isGuest)
			),
		));
    } elseif(Yii::app()->user->checkAccess('6')){
              $this->widget('MainMenu',array(
			  'items'=>array(
				array('label'=>'Заказы', 'url'=>array('/agent/orders/index')),
				array('label'=>'Архив заказов', 'url'=>array('/agent/orders/order_archive')),
				array('label'=>'Водители', 'url'=>array('/agent/drivers/index')),
				array('label'=>'Статистика', 'url'=>array('/agent/statistics/index')),
				array('label'=>'Настройки', 'url'=>array('/agent/users/index')), 
				array('label'=>'Выйти', 'url'=>array('/site/logout'), 'visible'=>!Yii::app()->user->isGuest),
				array('label'=>'Account', 'url'=>array('/admin/users/update/id/'.Yii::app()->user->id), 'visible'=>!Yii::app()->user->isGuest)
			),
		));
    } elseif(Yii::app()->user->checkAccess('7')) {
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
		}elseif(Yii::app()->user->checkAccess('8')){
                    $this->widget('MainMenu',array(
                        'items'=>array(
                                array('label'=>'Заказы', 'url'=>array('/agent/orders/index')),
                                array('label'=>'Выйти', 'url'=>array('/site/logout'), 'visible'=>!Yii::app()->user->isGuest),
                                array('label'=>'Account', 'url'=>array('/admin/users/update/id/'.Yii::app()->user->id), 'visible'=>!Yii::app()->user->isGuest)
                            ),
                    ));
                } ?>
		
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
