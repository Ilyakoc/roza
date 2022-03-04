<?php

return array(
	array('class'=>'application.components.rules.DAliasRule'),
		
    // Admin
    'cp'=>'admin/default/index',
    'cp/<controller>/<action:\w+>/<id:\d+>'=>'admin/<controller>/<action>',
    'cp/<controller>/<action>'=>'admin/<controller>/<action>',
    'cp/<controller>'=>'admin/<controller>',

    // Admin
    'devcp'=>'devadmin/default/index',
    'devcp/<controller>/<action:\w+>/<id:\d+>'=>'devadmin/<controller>/<action>',
    'devcp/<controller>/<action>'=>'devadmin/<controller>/<action>',
    'devcp/<controller>'=>'devadmin/<controller>',

    // Site Defaults
    ''=>'site/index',
    'shop'=>'shop/index',
    'questions'=>'question/index',
    'news'=>'site/events',
    'articles'=>'site/articles',
    'wiki'=>'site/wikis',
    'news/<id:\d+>'=>'site/event',
    'article/<id:\d+>'=>'site/article',
    'wiki/<id:\d+>'=>'site/wiki',
    'akcii'=>'site/actions',
	'sitemap'=>'sitemap/index',


    // Default Rules
    '<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
    '<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
    '<module>/<controller>/<action:\w+>/<id:\d+>'=>'<module>/<controller>/<action>',
    '<module>/<controller>/<action:\w+>'=>'<module>/<controller>/<action>',
);
