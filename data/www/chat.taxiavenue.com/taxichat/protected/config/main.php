<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.

error_reporting(E_ALL);
ini_set("display_errors", 1);
ini_set('date.timezone', 'Europe/Kiev');

return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'Такси Чат',
	
	'sourceLanguage'=>'en_US',
    'language'=>'ru',
    'charset'=>'utf-8',
	
	// preloading 'log' component.3
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
	),

	'modules'=>array(
		// uncomment the following to enable the Gii tool
		'admin',
		'dispatcher',
		'customer_application',
		'driver_application',
		'agent', 
		
        //'DriversApplication',
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'123456',
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array(),
		),
		
	),

	// application components
	'components'=>array(
		'authManager'=>array(
			'class'=>'PhpAuthManager',
			'defaultRoles'=>array('guest'),
		),
		'ih'=>array('class'=>'CImageHandler'),
		'user'=>array(
			// enable cookie-based authentication
			'class' => 'WebUser',
			'allowAutoLogin'=>true,
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
		'image'=>array(
          'class'=>'application.extensions.image.CImageComponent',
            // GD or ImageMagick
            'driver'=>'GD',
            // ImageMagick setup path
            //'params'=>array('directory'=>'/opt/local/bin'),
        ),
		// uncomment the following to enable URLs in path-format
			
		'urlManager'=>array(
			'urlFormat'=>'path',
			'showScriptName'=>false,
			'rules'=>array(
				//'users/type_atributes/<id:\d+>' => 'users/default/type_atributes',
				//'users/model_atributes/<id:\d+>' => 'users/default/model_atributes',
				//'admin/<controller:\w+>/<id:\d+>'=>'<controller>/view_admin', 
				//'admin/<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>_admin',
				//'admin/<controller:\w+>/<action:\w+>'=>'<controller>/<action>_admin',
				//'admin/login'=>'admin/default/login',
				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),		
		
		// uncomment the following to use a MySQL database
		/*
		'db'=>array(
			//	'class'=> 'MydbConection',
			'connectionString' => 'pgsql:host=91.203.60.46;port=8888;dbname=taxi',
			'username' => 'postgres',
			'password' => 'trustno1',
		), 
		'db'=>array(
			'connectionString' => 'pgsql:host=localhost;port=5432;dbname=taxichat',
			'username' => 'taxiavenue',
			'password' => 'anastasia101',
		), 
		*/
		
		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=maptaxiavenuecom',
			'emulatePrepare' => true,
			'username' => 'taximap',
			'password' => 'UFCCzrUK9BPcwGXv', // 589cc30f-1395-4c42-913c-0cea0e5ed2e2
			'charset' => 'utf8',
		),
		
		'errorHandler'=>array(
			// use 'site/error' action to display errors
			'errorAction'=>'site/error',
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
		
		'clientScript'=>array(
			'packages' => array(
			   // Уникальное имя пакета
			   'custom' => array(
					// Где искать подключаемые файлы JS и CSS
					'baseUrl' => '/js/',
					// Если включен дебаг-режим, то подключает /js/highcharts/highcharts.src.js
					// Иначе /js/highcharts/highcharts.js
					'js'=>array('custom.js'),
					// Подключает файл /js/highcharts/highcharts.css
					//'css' => array('highcharts.css'),
					// Зависимость от другого пакета
					'depends'=>array('jquery'),
				),
				'jquery_ui' => array(
					// Где искать подключаемые файлы JS и CSS
					'baseUrl' => '/js/',
					// Если включен дебаг-режим, то подключает /js/highcharts/highcharts.src.js
					// Иначе /js/highcharts/highcharts.js
					'js'=>array('jquery-ui.js'),
					// Подключает файл /js/highcharts/highcharts.css
					//'css' => array('highcharts.css'),
					// Зависимость от другого пакета
					'depends'=>array('jquery'),
				),
				'datetimepicker' => array(
					// Где искать подключаемые файлы JS и CSS
					'baseUrl' => '/js/',
					// Если включен дебаг-режим, то подключает /js/highcharts/highcharts.src.js
					// Иначе /js/highcharts/highcharts.js
					'js'=>array('datetimepicker.js'),
					// Подключает файл /js/highcharts/highcharts.css
					//'css' => array('highcharts.css'),
					// Зависимость от другого пакета
					'depends'=>array('jquery'),
				),
				'fancybox' => array(
					// Где искать подключаемые файлы JS и CSS
					'baseUrl' => '/js/',
					// Если включен дебаг-режим, то подключает /js/highcharts/highcharts.src.js
					// Иначе /js/highcharts/highcharts.js
					'js'=>array('jquery.fancybox.pack.js'),
					// Подключает файл /js/highcharts/highcharts.css
					//'css' => array('highcharts.css'),
					// Зависимость от другого пакета
					'depends'=>array('jquery'),
				),
			)
		),
	),
	
	

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'webmaster@example.com',
		'siteUrl' =>'',
		'siteIP' =>'188.40.107.79',
	),
);
