<?php
/**
 * Файл настроек модели
 */
use common\components\helpers\HYii as Y;

return [
	'class'=>'\MyModel',
	'menu'=>[
		'backend'=>['label'=>'Новый модуль']
	],
	'buttons'=>[
		'create'=>['label'=>'Добавить'],
	],
	'crud'=>[
		'index'=>[
            'url'=>'/cp/crud/index',
			'title'=>'Список',
			'gridView'=>[
				'dataProvider'=>[
					'criteria'=>[
						'select'=>'id, title, active'
					]
				],
				'columns'=>[
					[
						'name'=>'title',
						'header'=>'Наименование',
						'type'=>'raw',
						'value'=>'"<strong>".CHtml::link($data->title,["/cp/crud/update", "cid"=>"oil_model", "id"=>$data->id])."</strong>"'
					],
					[
						'name'=>'active',
						'type'=>'common.ext.active'
					],
					'crud.buttons'
				]
			]
		],
		'create'=>[
			'url'=>'/cp/crud/create',
			'title'=>'Добавление',
		],
		'update'=>[
			'url'=>['/cp/crud/update'],
			'title'=>'Редактирование',
		],
		'delete'=>[
            'url'=>['/cp/crud/delete'],
		],
		'form'=>[
			'attributes'=>[
                'active'=>'checkbox',
				'title',
				'alias'=>'alias'
			]
		]
	],
];
