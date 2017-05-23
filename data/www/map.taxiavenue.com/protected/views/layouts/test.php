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

    <script src="http://localhost:3000/socket.io/socket.io.js" type="text/javascript"></script>
</head>

<body>
    <script type="text/javascript">
        /*window.onload = function(){
            if (typeof io == 'undefined') {
                alert('no');
            } else {
                var ss = io.connect('http://localhost:3000');
                ss.on('connect', function(){
                   alert('fghgf'); 
                });
                //ss.emit('qq', {d: 'tt'});
            }
            
        };*/
        /*jj = function(a,b,l) {
            console.log(a + '*' + b);
            console.log(typeof l);
            if (typeof l == 'function') l(a+b);
        };
        hh = function(g){console.log(g);}
        n = new Array();
        jj(2,3, n);*/
        function counter(i) {
    inc = function() { alert(++i) };
    dec = function() { alert(--i) };
}

counter(0);
inc(), inc(), dec();

    </script>
</body>