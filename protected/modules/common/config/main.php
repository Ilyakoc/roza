<?php
return [
	'aliases'=>[
		'crud'=>'common.modules.crud',
		'settings'=>'common.modules.settings',
		'seo'=>'common.modules.seo'
	],
	'modules'=>[
		'crud'=>['class'=>'crud.CrudModule', 'autoload'=>true],
		'settings'=>['class'=>'settings.SettingsModule', 'autoload'=>true],
		'seo'=>['class'=>'seo.SeoModule', 'autoload'=>true]
	]
];