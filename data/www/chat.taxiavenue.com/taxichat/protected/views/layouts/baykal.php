<?php /* @var $this Controller */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />
    <script src="http://code.jquery.com/jquery-1.10.2.js"></script>
    <script src="http://code.jquery.com/ui/1.11.1/jquery-ui.js"></script>
	<!-- blueprint CSS framework -->
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/reset.css" media="screen, projection" />
	<!--<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/jquery-ui.css" media="print" /> -->
	 <link href="<?php echo Yii::app()->request->baseUrl; ?>/css/jquery-ui.min.css" rel="stylesheet" media="screen">

	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css" />

    <!--<link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl; ?>/css/jquery-ui.css">-->
<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/jquery.fancybox.css" />

 <script>
   var params = 'scrollbars=no,resizable=no,status=no,location=no,toolbar=no,menubar=no, width=600,height=500,left=-1000,top=-1000';
 </script>
 


	<?php
	Yii::app()->getClientScript()->registerCoreScript('jquery');
	Yii::app()->getClientScript()->registerCoreScript('jquery.ui');
	Yii::app()->clientScript->registerPackage('datetimepicker');
	Yii::app()->getClientScript()->registerCoreScript('fancybox');
	Yii::app()->clientScript->registerPackage('custom');
	?>
<script>
  $(function() {
    $( "#tabs" ).tabs();
  });
  </script>
  
	<title>TaxiChat Administration</title>
</head>

<body>


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

<header><!--header-->
    <p class="logo">Таксичат - Диспетчерская</p>
	<nav>
	<?php
	$this->widget('MainMenu',array(
			'items'=>array(
				array('label'=>'Заказы', 'url'=>array('/dispatcher/orders/index')),
				array('label'=>'Архив заказов', 'url'=>array('/dispatcher/orders/order_archive')),
				array('label'=>'База водителей', 'url'=>array('/dispatcher/drivers/index')),
				array('label'=>'База клиентов', 'url'=>array('/dispatcher/customers/index')),
				array('label'=>'Чёрный список', 'url'=>array('/dispatcher/customers/BlackList')),
				array('label'=>'Карта водителей', 'url'=>array('/dispatcher/drivers/drivers_map')),
				array('label'=>'Байкал', 'url'=>array('/dispatcher/ShowBaikal/index')),
				array('label'=>'Выйти', 'url'=>array('/site/logout'), 'visible'=>!Yii::app()->user->isGuest),
				array('label'=>'Account', 'url'=>array('/users/update/id/'.Yii::app()->user->id), 'visible'=>!Yii::app()->user->isGuest)
			),
		)); ?>

		<div class="clear"></div>
	</nav>
</header><!--header_end-->

<section class="wrapper"><!--wrapper-->
<?php echo $content; ?>
</section>
<footer>
</footer>
</body>
</html>
