<?php
/**
 * uncomment the following to define a path alias
 * Yii::setPathOfAlias('local','path/to/local-folder');
 * This is the main Web application configuration. Any writable
 * CWebApplication properties can be configured here.
 * 
 * Настройки на сервере
 * Если надо какие-то настройки переопределить в локальном окружении - создайте файл main.local.php
 */
error_reporting(E_ALL);
ini_set("display_errors", 1);
ini_set('date.timezone', 'Europe/Kiev');

$pathLocalConfig = dirname(__FILE__) . '/main.local.php';

return CMap::mergeArray(
    array(
        'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
        'name'=>'Онлайн Карта',
        'language'=>'ru',
        'defaultController' => 'city',
        // preloading 'log' component
        'preload'=>array('log'),
    
        // autoloading model and component classes
        'import'=>array(
            'application.models.*',
            'application.components.*',
            'application.components.OpenAuth.*',
            'application.components.thirdPartyMark.*',
            'application.controllers.*',
            'ext.eoauth.*',
            'ext.eoauth.lib.*',
            'ext.eauth.*',
            'ext.eauth.services.*',
            'ext.instagram.*',
        ),
        
        'modules'=>array(
            'gii'=>array(
                'class'=>'system.gii.GiiModule',
                'password'=>'123456',
                // If removed, Gii defaults to localhost only. Edit carefully to taste.
                'ipFilters'=>false,
            ),
        ),
    
        // application components
        'components'=>array(
    //        'cache'=>array(
    //            'class'=>'system.caching.CMemCache',
    //            'servers'=>array(
    //                array('host'=>'localhost', 'port'=>11211, 'weight'=>60),
    //            ),
    //        ),
            
            'email'=>array(
                'class'=>'ext.email.Email',
                'delivery'=>'php', //Will use the php mailing function.
                //May also be set to 'debug' to instead dump the contents of the email into the view
             ),
            'sms'=>array(
                'class'=>'components.MainSMS.php',
            ),
            'curl' => array(
                'class' => 'ext.curl.Curl',
                'options' => array(/* additional curl options */),
            ),
    
            //компонент для работы с изображениями
            'ih'=>array('class'=>'CImageHandler'),
                'user'=>array(
                    // enable cookie-based authentication
                    'allowAutoLogin'=>false,
                ),
            'session' => array(
                'sessionName' => 'PHPSESSID',
                'class'=> 'CDbHttpSession',
              // 'autoCreateSessionTable '=> 'true'  ,
                'connectionID' => 'db',
                'sessionTableName' => 'session',
                'useTransparentSessionID'   =>(isset($_POST['PHPSESSID']) && $_POST['PHPSESSID']) ? true : false,
                'cookieMode' => 'allow',
                'timeout' => 3600
            ),
    
            // uncomment the following to enable URLs in path-format
            'urlManager'=>array(
                'showScriptName'=>false,
                'urlFormat'=>'path',
                'rules'=>array(
                    // '<name_en:\w+>/<code:\w+>/'=>'theme/index',
                    //'icon/create'=>'icon/create',
                    'login/<service:(google|google-oauth|yandex|yandex-oauth|twitter|linkedin|vkontakte|facebook|steam|yahoo|mailru|moikrug|github|live|odnoklassniki)>' => 'site/login',
                    'login' => 'site/login', // ???
                    'auth/'=>'auth/index',
    
    //                'gii'=>'gii',
    //                'gii/<controller:\w+>'=>'gii/<controller>',
    //                'gii/<controller:\w+>/<action:\w+>'=>'gii/<controller>/<action>',
    
                    'confirm_code/'=>'confirmcode_json/index',
                    'forgot_password'=>'forgot_password/index',
                    'forgot_password/<action:\w+>'=>'forgot_password/<action>',
                    'auth/<action:\w+>'=>'auth/<action>',
                    'user/<id:\d+>'=>'users/index',
                    'api/<controller:\w+>'=>'<controller>/index',
                    //'api/<controller:\w+>/<id:\d+>'=>'<controller>/view',
                    'api/<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
                    'api/mobile/<controller:\w+>/<action:\w+>'=>'<controller>_mobile/<action>',

                    // Страница - "политика конфиденциальности"
                    'policy' => 'city/policy',
                    '<name_en:[-\w]+>/'=>'city/index',
                    '<name_en:[-\w]+>/<code:[-\w]+>'=>'kind/index',
                    '<name_en:[-\w]+>/<code:[-\w]+>/<id:\d+>'=>'mark/index',

    //                '/'=>'city/index',
    
                    //'api/<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
                ),
            ),
            // uncomment the following to use a MySQL database
            'db'=>array(
                'connectionString' => 'mysql:host=localhost;dbname=map_chat',
                'emulatePrepare' => true,
                'username' => 'taximap',
                'password' => '',
                'charset' => 'utf8',
            ),
            'errorHandler'=>array(
                // use 'site/error' action to display errors
                'errorAction'=>'error/error',
            ),
            'log'=>array(
                'class'=>'CLogRouter',
                'routes'=>array(
                    array(
                        'class'=>'CFileLogRoute',
                        'levels'=>'error, warning',
                    ),
                    // uncomment the following to show log messages on web pages
                    /*
                    array(
                            'class'=>'CWebLogRoute',
                    ),
                    */
                ),
            ),
            'user' => array(
                'allowAutoLogin'=>true,
            ),
        ),
        // application-level parameters that can be accessed
        // using Yii::app()->params['paramName']
        'params'=>array(
            // this is used in contact page
            'adminEmail'=>'archic2@mail.ru',
            //врема жизни проверочного кода исп. при регистрации
            'time_code'=>3600*24,
            //
            'animals'=>array('Слон','Жираф','Кенгуру','Волк','Заяц','Рыба'),
            'baseUrl' => '',
            'socketPort' => '3001'
        ),
    ),
    file_exists($pathLocalConfig) ? require $pathLocalConfig : array()
);