<?php
/* @var $this Controller */
$baseURL = Yii::app()->getBaseUrl(true);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="language" content="ru" />
    <title><?php echo CHtml::encode($this->pageTitle); ?></title>
    <link href="<?php echo $baseURL; ?>/css/jquery-ui.min.css" rel="stylesheet" media="screen">
    <link href="<?php echo $baseURL; ?>/css/main.css" rel="stylesheet" media="screen">
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="<?php echo $baseURL; ?>/js/libs/html5shiv.js"></script>
    <script src="<?php echo $baseURL; ?>/js/libs/respond.min.js"></script>
    <![endif]-->

    <script src="<?php echo $baseURL; ?>/js/libs/sugar.js"></script>
    <script src="http://api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>
    <script src="<?=Yii::app()->request->hostInfo?>:<?=Yii::app()->params->socketPort?>/socket.io/socket.io.js" type="text/javascript"></script>
</head>

<body>
<?php echo $this->renderPartial('/_inc/topControl', array('baseURL' => $baseURL)); ?>
<section id="map"></section>
<?php echo $content; ?>
<!---->
<footer></footer>
<script type="text/javascript" class="appInfo">
    var appInfo = window.appInfo || {};
    appInfo.selfUser = <?php echo $this->GetSelfUserJSON()?>;
    var baseUrl = '<?=$baseURL?>';
    var socketPort = <?=Yii::app()->params->socketPort?>;
    var backboneHistoryUrl = '<?=Yii::app()->getBaseUrl()?>';
    var forgotChecked = '<?=$this->getPopup('forgotChecked')?>';
</script>
<script src="<?php echo $baseURL; ?>/js/libs/requirejs/RequireJS.js"></script>
<script src="<?php echo $baseURL; ?>/js/requireConfig.js"></script>
<script src="<?php echo $baseURL; ?>/js/main.js"></script>
</body>
</html>
