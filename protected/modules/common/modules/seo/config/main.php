<?php
return [
	'aliases'=>[
		'seo'=>'common.modules.seo'	
	],
	'modules'=>[
		'admin'=>[
			'class'=>'\seo\modules\admin\AdminModule'
		]	
	],
	'controllerMap'=>[
		'default'=>[
			'class'=>'\seo\controllers\DefaultController'
		]	
	]
];