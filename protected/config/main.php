<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.

Yii::setPathOfAlias('booster', dirname(__FILE__).'/../extensions/booster');

return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'Loyalty cards',
        'sourceLanguage'=>'en',
        'language'=>'en',
        'defaultController'=>'cards',

	// preloading 'log' component
	'preload'=>array(
            'log',
            'booster'
        ),

	// autoloading model and component classes
	'import'=>array(
            'application.models.*',
            'application.components.*',
	),

	'modules'=>array(
		// uncomment the following to enable the Gii tool
		
            'gii'=>array(
                'class'=>'system.gii.GiiModule',
                'password'=>false,
                // If removed, Gii defaults to localhost only. Edit carefully to taste.
                'ipFilters'=>array('127.0.0.1','::1'),
            ),
		
	),

	// application components
	'components'=>array(
            
            'clientScript'=>array(
                'packages'=>array(
                    'jquery'=>array(
                        'baseUrl'=>'https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/',
                        'js'=>array('jquery.min.js'),
                )
            )),

            'user'=>array(
                // enable cookie-based authentication
                'allowAutoLogin'=>true,
            ),

            // uncomment the following to enable URLs in path-format

            'urlManager'=>array(
                'showScriptName'=>false,
                'urlFormat'=>'path',
                'rules'=>array(
                    '<controller:\w+>/<id:\d+>'=>'<controller>/view',
                    '<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
                    '<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
                ),
            ),
		

            // database settings are configured in database.php
            'db'=>require(dirname(__FILE__).'/database.php'),

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
                ),
            ),
            
            'booster' => array(
                'class' => 'booster.components.Booster',
            ),

	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'webmaster@example.com',
	),
);
