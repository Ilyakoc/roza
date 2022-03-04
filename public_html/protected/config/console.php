<?php
$dir=dirname(__FILE__);
return array(
	'basePath'=>"{$dir}/..",
	'components'=>array(
		'db'=>/*YII_DEBUG ? array(
			'connectionString'=>'mysql:host=localhost;dbname=bazaroza.ru.local',
			'username'=>'root',
			'password'=>'',
		) : */array(
   			'connectionString'=>'mysql:host=localhost;dbname=cm46029_roza',
            'username'=>'cm46029_roza',
            'password'=>'f8KSX9dG',
		),
	),
	'commandMap'=>array(
		'migrate'=>array(
			'class'=>'system.cli.commands.MigrateCommand',
			'migrationPath'=>'application.migrations',
			'migrationTable'=>'tbl_migration',
			'connectionID'=>'db',
			// 'templateFile'=>'application.migrations.template',
		)
	)
);
