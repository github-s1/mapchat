<?php

// This is the configuration for yiic console application.
// Any writable CConsoleApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'Cron',

	// preloading 'log' component
	'preload'=>array('log'),
	
	'import'=>array(
		'application.models.*',
		'application.components.*',
	),
	
	// application components
	'components'=>array(
		'db'=>array(
			'connectionString' => 'pgsql:host=localhost;port=5432;dbname=taxichat',
			'username' => 'taxiavenue',
			'password' => 'anastasia101',
		),
		
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				 array(
                    'class'=>'CFileLogRoute',
                    'logFile'=>'cron.log',
                    'levels'=>'error, warning',
                ),
                array(
                    'class'=>'CFileLogRoute',
                    'logFile'=>'cron_trace.log',
                    'levels'=>'trace',
                ),
			),
		),
	),
);