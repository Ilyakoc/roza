<?php
/**
 * Файл настроек модели \seo\models\Seo
 */
return [
	'class'=>'\seo\models\Seo',
	'crud'=>[
		'form'=>[
			'attributes'=>[
				'seo_h1',
				'seo_meta_title',
				'seo_meta_keywords'=>[
					'type'=>'textArea', 
					'params'=>[
						'htmlOptions'=>['class'=>'form-control', 'style'=>'min-height:50px']
					]
				],
				'seo_meta_desc'=>'textArea',
				'seo_link_title',
			]
		]
	]
];