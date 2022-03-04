<?php
return array(
	'modules'=>array(
        'common'=>[
			'modules'=>[
				'settings'=>[
					'class'=>'application.modules.settings.SettingsModule',
					'config'=>[
						'rangeof'=>[
							'class'=>'\RangeofSettings',
							'title'=>'Области применения',
							'menuItemLabel'=>'Области применения',
							'viewForm'=>'admin.views.settings._rangeof_form'
						],
					]	
				]					
			]        		
        ],
	),
);
 
