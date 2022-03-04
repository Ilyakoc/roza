<?php
/**
 * Файл настроек модели
 */
use common\components\helpers\HYii as Y;

$t=Y::ct('MyModule.crud', 'mymodule');
return [
	// @param string|array имя класса модели. Может быть задано как массив
	// ['\crud\models\ExampleARModel', 'scenario'] 
	// "scenario" - имя сценария, которым будет проинициализорована модель
	// при автоматическом создании объекта модели.
	'class'=>'\crud\models\ExampleARModel',
	// конфигурация пунктов глобального меню.
	'menu'=>[
		'frontend'=>['label'=>$t('frontend.menu.item.label'), 'disabled'=>true],	
		'backend'=>['label'=>$t('backend.menu.item.label')]	
	],
	// хлебные крошки, если требуется, напр. модель может являтся моделью элемента модели группы.
	'breadcrumbs'=>[
		$t('parent.page.index.title')=>\Yii::app()->createUrl("/cp/crud/index", ["cid"=>"parent"])
	],
	// кнопки действий на страницах
	'buttons'=>[
		'create'=>['label'=>$t('button.create')],
		'settings'=>['label'=>$t('button.settings')],
		'update'=>'', // может быть задана пустая строка, если требуется не отображать кнопку.
	],
	// конфигурация модели настроек (для модуля Settings)
	// @todo интеграция настроек. На данный момент, необходимо перенести настройку
	// в основной файл конфигурации /protected/config/defaults.php
	'settings'=>[
		'mymodule'=>[
			'class'=>'\MyModuleSettings',
			'title'=>$t('settings.title'),
			'menuItemLabel'=>$t('settings.menuItemLabel'),
			'viewForm'=>'mymodule.modules.admin.views.crud.mymodel._settings_form'				
		]
	],
	'crud'=>[
		'index'=>[
			// обработчик перед началом обработки страницы. 
			'onBeforeLoad'=>function() { return true; },
			// сценарий модели
			'scenario'=>'view',
			// заголовок страницы
			'title'=>$t('page.index.title'),
			// специальные хлебные крошки для страницы
			'breadcrumbs'=>[
				$t('parent.page.index.title')=>\Yii::app()->createUrl("/cp/crud/index", ["cid"=>"parent"])
			],
			// специальное значение страницы в хлебных крошках.
			'titleBreadcrumb'=>$t('page.index.title.breadcrumb'),
			// настройки для виджета zii.widgets.grid.CGridView 
			'gridView'=>[
				// включить сортировку элементов
				'sortable'=>[
					// отключить сортировку
					'disabled'=>true,
					// (обязательно) имя категории сортировки. 
					// Для действия \common\ext\sort\actions\SaveAction может быть передана через параметр запроса "sortable_category"
					'category'=>'my_category',
					// (необязательно) ссылка на действие сохранения сортировки. По умолчанию "/crud/admin/default/sortableSave". 
					'url'=>'/cp/crud/sortableSave',
					// (необязательно) дополнительный ключ сортировки
					// Для действия \common\ext\sort\actions\SaveAction может быть передана через параметр запроса "sortable_key"
					'key'=>null,
					// дополнительные параметры для виджета \common\ext\sort\widgets\Sortable::$options
					'selector'=>'.grid-view > table > tbody',
					'dataId'=>'id',
					// автосохранение, по умолчанию FALSE.
					'autosave'=>true,
					'onAfterSave'=>null,
					
				],
				// "dataProvider" параметры для \CDataProvider
				'dataProvider'=>[
					'criteria'=>[
						'select'=>'id, title, published'
					]						
				],
				// "columns.sort" для быстрой пересортировки столбцов
				'columns.sort'=>['mynewattribute', 'id', 'crud.buttons'],
				// применять при пересортировке столбцов фильтрацию, т.е. те атрибуты, которых нет 
				// в массиве "columns.sort" не будут отображены.   
				'columns.sort.filter'=>true,
				// специальный атрибут "columns.sort.reverse" - обратная обработка.
				'columns.sort.reverse'=>true,
				'columns'=>[
					'myattribute',
					[
						'name'=>'title',
						'header'=>$t('crud.index.gridView.columns.title.header'),
						'type'=>'raw',
						'value'=>'"<strong>".CHtml::link($data->title,["/cp/crud/update", "cid"=>"myid", "id"=>$data->id])."</strong>"'
					],
					[
						'name'=>'published',
						// "common.ext.active" специальный тип для атрибута поведения \common\ext\active\behaviors\ActiveBehavior
						// может быть передано как 'type'=>'common.ext.active', в этом случае behaviorName будет установлен в "activeBehavior".
						'type'=>[
							'common.ext.active'=>[
								'behaviorName'=>'activeBehavior'		
							]								
						],
							 
					],
					[
						'name'=>'image',
						// "common.ext.file.image" специальный тип для атрибута поведения \common\ext\file\behaviors\FileBehavior
						// может быть передано как 'type'=>'common.ext.file.image', в таком случае передаются следующие параметры:
						// behaviorName=imageBehavior, width=120, height=120
						// также любой из параметров является необязательным, будет заменен значениями по умолчанию.
						// формат задания данного типа 'common.ext.file.image'=>[массив параметров]
						// Дополнительные параметры:
						// "proportional" (boolean) генерация пропорционального мини-изображения. По умолчанию TRUE.
						// "htmlOptions" (array) массив дополнительных HTML атрибутов для элемента изображения.
						// "default" (boolean|string) ссылка на изображение по умолчанию. По умолчанию (TRUE) -
						// изображение будет сформировано автоматически исползуя сервис http://placehold.it
						// Если будет передано FALSE - будет отображен непустой пробел при отсутствии изображения. 
						'type'=>[
							'common.ext.file.image'=>[
								'behaviorName'=>'imageBehavior',
								'width'=>120,
								'height'=>120,
								'proportional'=>true, 
								'htmlOptions'=>[],
								'default'=>true
						]],
						'headerHtmlOptions'=>['style'=>'width:15%']
					],
					// если столбец buttons задан не будет, то будет добавлен стандартный набор кнопок (редатированить, удалить)
					[
						'type'=>'crud.buttons',
						'params'=>[] // дополнительные параметры для колонки buttons
					] 
					// может быть передано как 'crud.buttons' - специальное значение колонки кнопок  (редатированить, удалить)
					// может быть передано как 'crud.buttons'=>['type'=>'crud.buttons', 'params'=>[]] для того, чтобы можно 
					// было переопределить настройки при использовании "use" в другой конфигурации.
				]
			]
		],
		'create'=>[
			// обработчик перед началом обработки страницы.
			'onBeforeLoad'=>function() { return true; },
			// сценарий модели
			'scenario'=>'insert',
			// ссылка страницы, если требуется переопределить ссылку по умолчанию. 
			'url'=>'/cp/crud/update',
			// заголовок страницы
			'title'=>$t('page.create.title'),
			// параметр "use" указывает из какого блока брать конфигурацию, по умолчанию "form" 
			// (если "form" не задано будет использовано "tabs") 
			'use'=>'tabs',
			// дополнительная конфигурация для виджета формы \CActiveForm
			'form'=>[
				// может быть использован параметр use, который указывает из какой конфигурации брать данные.
				// use может быть передан как массив [пусть к файлу конфигурации, путь к значению внтури массива конфигурации]
				// напр., ['myseomodule.config.crud.seo', 'crud.update.form']
				// параметры будут объеденены. Приоритет параметров ниже у заданных в "use".
				'use'=>'crud.form',
				'id'=>'crud-create-form'
			]
		],
		'update'=>[
			// обработчик перед началом обработки страницы.
			'onBeforeLoad'=>function() { return true; },
			// сценарий модели
			'scenario'=>'update',
			// ссылка страницы, если требуется переопределить ссылку по умолчанию.
			// может быть передана, как массив вида (url, param1=>value1, param2=>value2, ...) 
			'url'=>['/cp/crud/update', 'mode'=>'form'],
			'title'=>$t('page.update.title'),
			// дополнительная конфигурация для виджета формы \CActiveForm
		],
		'delete'=>[
			// обработчик перед началом обработки страницы.
			'onBeforeLoad'=>function() { return true; },
			// сценарий модели
			'scenario'=>'delete',
			// @todo дополнительные обработчики (callable) перед и после удаления. 
			// 'before'=>[],
			// 'after'=>[]
		],
		// form - конфигурация для одностраничного отображения
		// общая дополнительная конфигурация для виджета формы \CActiveForm
		'form'=>[			
			// если требуется изменить параметры тэга формы
			'htmlOptions'=>['enctype'=>'multipart/form-data'],
			// может быть передано только имя шаблона "view", 
			'view'=>'mymodule.modules.admin.views.crud.mymodel._form',
			'buttons'=>[
				// не отображать кнопку удаления
				'delete'=>false
			],
			// либо "attributes" для шаблона модуля CRUD.
			'attributes'=>[
				// специальный атрибут "attributes.sort" для быстрой пересортировки атрибутов.
				'attributes.sort'=>['myattribute3', 'myattribute2', 'myattribute5'],
				// применять при пересортировке атрибутов фильтрацию, т.е. те атрибуты, которых нет
				// в массиве "attributes.sort" отображены не будут.
				'attributes.sort.filter'=>true,
				// специальный атрибут "attributes.sort.reverse" - обратная обработка.
				'attributes.sort.reverse'=>true,
				// attribute,
				// в этом случае по умолчанию тип "text" 
				// attribute=>type, 
				// Тип ("type") должен быть одним из типов \common\widgets\form\*Field					
				// может быть также передан как массив 'type'=>['class'=>'\common\widgets\form\Zelect']
				//
				// Также может быть передано как: 
				// либо attribute=>["type"=>type, "params"=>массив дополнительных параметров, "strictParams"=>false, "isNewRecord"=>null]
				// Массив дополнительных параметров будет расширен при передаче параметрами 
				// ['form'=>$form, 'model'=>$model, 'attribute'=>$attribute], если не указан явно "strictParams"=>true.
				//
				// может быть передан параметр "isNewRecord", который указывает 
				// TRUE - отображать данное поле только для новой записи
				// FALSE - отображать данное поле только для существующих записей
				// NULL - отображать всегда   
				//
				// либо attribute=>["php"=>строка с php-кодом отображения]
				// В "params" будут переданы переменные: 
				// $this (\CController) объект контроллера;  
				// $form (\CActiveForm) объект формы ;  
				// $model (\CModel) объект модели;  
				// $attribute (string) имя атрибута;  
				'myattribute'=>'text',
				'myattribute2'=>'textArea',
				'myattribute3'=>['type'=>'text', 'params'=>['unit'=>$t('column.myattribute3.unit')]],
				'myattribute4'=>[
					'type'=>['class'=>'\MyTypeWidget'], 
					'params'=>['note'=>$t('column.myattribute4.note')],
					'strictParams'=>true
				],
				'myattribute5'=>[
					'php'=>'$this->widget("\MyFieldWidget", A::m(compact("form", "model", "attribute"), true)'
				],
				// специальный тип атрибута поведения \common\ext\file\behaviors\FileBehavior
				'myattribute6'=>[
					'type'=>'common.ext.file.image',
					'behaviorName'=>'myFileBehavior', // по умолчанию imageBehavior
					// дополнительные параметры для виджета,
					'params'=>[
						'actionDelete'=>\Yii::app()->getController()->createAction('removeImage'), // необязательно, по умолчанию /crud/admin/default/removeImage
						'tmbWidth'=>200, // необязательно, только для изображения, по умолчанию 200
						'tmbHeight'=>200, // необязательно, только для изображения, по умолчанию 200
					]
				]
			]
		],
		// tabs для страницы со вкладками
		'tabs'=>[
			'main'=>[
				// обработчик перед отображением, возвращает (boolean) - отображать вкладку или нет. 
				// будут переданы параметры function($cid, $form, $model, $attributes)
				'onBeforeRender'=>function($cid, $form, $model, $attributes) {
					return !$model->isNewRecord;
				},
				// не отображать вкладку (необязательно, по умолчанию TRUE)				
				'disabled'=>true,
				// заголовок вкладки (необязательно)
				'title'=>$t('tabs.main.title'),
				// идентификатор вкладки. (необязательно)
				'id'=>'tab-main',
				// может быть задан параметр "ajax"=>URL, является приоритетным, т.е.
				// при заданном параметре, контент будет получен по ajax ссылке.
				'ajax'=>'/ajax-url',
				// значение параметра processOutput метода $controller->renderPartial() при генерации контента вкладки.
				// по умолчанию FALSE. 
				'processOutput'=>true,
				// шаблон отображения вкладки (в шаблон будет переданы параметры $cid, $form, $model, $attributes)
				'view'=>'mymodule.modules.admin.views.crud.mymodel._tab_main',
				// также может быть передано как "view", так и "attributes",
				'attributes'=>[
					// см. настройки /crud/form/attributes
				]
			],
			'seo'=>[
				// в "use" может быть передан массив [<путь к файлу конфигурации>, <путь к массиву конфигурации>]
				// <путь к массиву конфигурации> - не обязателен, по умолчанию 'crud.form'
				'use'=>['application.config.crud.seo', 'crud.tabs.main']
			]
		]
	],
	// настройки публичной части
	'public'=>[
		'index'=>[
			// обработчик перед началом обработки страницы.
			'onBeforeLoad'=>function() { return true; },
			// параметры для \CListView 
			'listView'=>[
				// "dataProvider" параметры для \CDataProvider
				'dataProvider'=>[
					'criteria'=>[
						'select'=>'id, title, published',
						'scopes'=>'published'
					]
				],
			],
		],
		'view'=>[
			// обработчик перед началом обработки страницы.
			'onBeforeLoad'=>function() { return true; },
		]
	]
];
