<?php

Yii::setPathOfAlias('widget', dirname(__FILE__).DS.'..'.DS.'widget');

return array(
	'basePath'=>dirname(__FILE__).DS.'..',
	'name'=>'Новый сайт',

	'preload'=>array('log'),

	'import'=>array(
		'application.models.*',
		'application.components.*',
		'application.components.behaviors.*',
		'application.components.helpers.*',
		'application.components.models.*',
		'application.components.rules.*',
		'application.components.validators.*',
		
        'ext.*',
        'ext.helpers.*',
        'ext.CmsMenu.*',
        'ext.ContentDecorator.*',
	    
	    'ext.YmlGenerator.YmlGenerator'
	),

	'modules'=>array(
        'admin'=>array(),
        'devadmin',
        'common'=>[
            'modules'=>[
                'crud'=>[
                    'class'=>'common.modules.crud.CrudModule',
                    'config'=>include(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'crud.php')             
                ],                              
                'settings'=>[
                    'class'=>'common.modules.settings.SettingsModule',
                    'config'=>include(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'settings.php')
                ]
            ]               
        ],
        'gii'=>array(
            'class'=>'system.gii.GiiModule',
            'password'=>'1',
            'ipFilters'=>false,
            // 'newFileMode'=>0666,
            // 'newDirMode'=>0777,
        ),
	),

	// application components
	'components'=>array(
	    'ymlGenerator'=>[
	        'class'=>'MyYmlGenerator',
	        'outputFile'=>dirname($_SERVER['SCRIPT_FILENAME']).'/yml/export.yml'
	    ],
	    
		'ih'=>array(
        	'class'=>'CImageHandler',
	    ),
		'user'=>array(
			'allowAutoLogin'=>true,
            'loginUrl' => array('admin/default/login'),
		),

        'cache'=>array(
            'class'=>'system.caching.CFileCache',
         ),

        'settings'=>array(
            'class'     => 'CmsSettings',
            'cacheId'   => 'global_website_settings',
            'cacheTime' => 84000,
        ),

		'urlManager'=>array(
			'urlFormat'=>'path',
            'showScriptName'=>false,
			'rules'=>include(dirname(__FILE__).DS.'urls.php')
		),

		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=',
			'emulatePrepare' => true,
			'username' => '',
			'password' => '',
			'charset' => 'utf8',
            'tablePrefix' => ''
		),

        'errorHandler'=>YII_DEBUG ? [] : array(
            'errorAction'=>'error/error',
        ),

		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
			)
		),

        'email' => array(
            'class'=>'ext.email.Email',
            'delivery'=>'php' //debug|php
  		),

        'image' => array(
            'class'=>'ext.image.CImageComponent',
            // GD or ImageMagick
            'driver'=>'ImageMagick',
            // ImageMagick setup path
            //'params'=>array('directory'=>"C:\ImageMagick\\"),
        ),

        'clientScript' => array(
            'class' => 'ext.minify.EClientScript',
            'combineScriptFiles' => false,
            'combineCssFiles' => false,
            'optimizeCssFiles' => false,
            'optimizeScriptFiles' => true
        ),
	),

	'params'=>array(
        'uploadSettingsPath' => '/files/settings/',
        'attributes' => true,
       // 'authServer'          => 'http://login.dishman.ru',
		'delivery_cat_id'=>30,
        'localauth'           => true,
		'adminEmail'            => 'antonvanyukov@gmail.com',
        'menu_limit'            => 5,
        'news_limit'            => 7,
        'posts_limit'           => 10,
        'hide_news'             => false,
        'tmb_height'            => 230,
        'tmb_width'             => 0,
        'max_image_width'       => 800,
        'dev_year'              => 2012,
        'watermark'             => false,
        'subcategories'         => true,
		'hide_shop_categories'  => false,
        'slider'                => array(
                                        'showCaption' => false,
                                        'carousel'  => array(
                                            'width'  => 153,
                                            'height' => 110,
                                        ),
                                        'slideshow' => array(
                                            'width'  => 522,
                                            'height' => 383,
                                        ),
                                    ),
        'banner'                => array(
								        'showCaption' => true,
								        'carousel'  => array(
									        'width'  => 2000,
									        'height' => 110,
								        ),
								        'slideshow' => array(
									        'width'  => 240,
									        'height' => 240,
								        ),
                                    ),
	),

    'language'=>'ru',
    'theme'=>''
);
 
