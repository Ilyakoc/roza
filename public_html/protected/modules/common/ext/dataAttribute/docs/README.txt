----------------------------------------------------
Документация для раширения \common\ext\dataAttribute
----------------------------------------------------

1) Модель должна быть наследуемой от \common\components\base\ActiveRecord
Иначе, нужно самостоятельно прописывать в модели правило [attribute, 'safe'] и подпись [attribute=>attributeLabel] 

2) Желательно для увеличения быстродействия создавать поле атрибута в базе данных через миграцию. А при подключении
поведения передавать параметр: 'addColumn'=>false
------------
I. Установка 
------------
1. Добавить поведение
public function behaviors()
{
    return [
        'dataAttributeBehavior'=>[
            'class'=>'\common\ext\dataAttribute\behaviors\DataAttributeBehavior',
            'attribute'=>'data',
            'attributeLabel'=>'Дополнительные параметры'
        ]
    ];
}

-----------------------------
II. Получение/установка значений в коде
-----------------------------

1. Получение значений
1.1 Получить все значения:
$model->dataAttributeBehavior->get()
1.2 Получить только активные значения
$model->dataAttributeBehavior->get(true)

2. Установка значений.
Важно! Если данные не соответсвуют структуре заданной в виджете 
редактирования (\common\ext\dataAttribute\widgets\DataAttribute),
то виджет, соответсвенно, будет работать не корректно. 
$model->dataAttributeBehavior->set($array)

------------
III. Видежты
------------
Доступные типы: string, time, radio, dropdown, number, model, raw

Сложный тип передается как:
 *  array(
 * 		name=>array(
 * 			"type"=>type, 
 * 			"data"=>data, 
 * 			"default"=>value,
 * 			"view"=>шаблон отображения, 
 * 			"params"=>array(param=>value)
 * 		)
 * 	)
 * Параметры "data", "default", "view" и "params" необязательны.
 * 
 * Список типов:
 * string: (по умолчанию) строка.
 * time: время.
 * number: число. 
 * dropdown: (сложный тип) выпадающий список. 
 * radio: (сложный тип) список элементов radio.
 * model: (сложный тип) модель.
 * Дополнительно в шаблон отображения элемента будет передан объект $model. 
 * (в разработке) Необходимо передать в "params" параметр "class"=>имя_класса_модели, если 
 * предполагается использвать шаблон по умолчанию для данного типа.
 *
 * Может быть передан параметр array(
 * 	"params"=>array(
 * 		"ajax-tpl-url"=>ajax ссылка для получения кода шаблона для нового элемента.
 *  ) 	   
 *
 * raw: (сложный тип) элемент как есть. Должен быть задан параметр шаблона
 * отображения "view". Шаблон данного типа по умолчанию пуст.

Пример:
$this->widget('\common\ext\dataAttribute\widgets\DataAttribute', [
	'behavior' => $model->dataAttributeBehavior,
	'header'=>['title'=>'Название', 'value'=>'Значение', 'time'=>'Время', 'category'=>'Категория'],
    'types'=>[
        'time'=>'time', 
        'category'=>[
            'type'=>'dropdown', 
            'data'=>[1=>"Элемент 1", 2=>"Элемент 2"], 
            'default'=>2
        ]
     ],
	'hideAddButton'=>true,
	'readOnly'=>['title'],
	'default' => [
		['title'=>'', 'value'=>'']
	]    
]);

Пример типа model:
$this->widget('\common\ext\dataAttribute\widgets\DataAttribute', [
    'behavior' => $model->itemsBehavior,
    'header'=>['item'=>'Область применения'],
    'hideActive'=>true,
    'defaultActive'=>true,
    'types'=>[
        'item'=>[
            'type'=>'model',
            'view'=>'admin.views.settings._rageof_item_form',
            'params'=>[
                'class'=>'\RageofItemSettings',
                'ajax-tpl-url'=>$this->createUrl('settings/getRageofItem')					
            ]
        ],
    ]
]);

--------------------------
IV. Часто используемый код
--------------------------

<div class="row">
	<?= $form->labelEx($model, 'props_data'); ?>
	<? $this->widget('\common\ext\dataAttribute\widgets\DataAttribute', [
		'behavior' => $model->dataAttributeBehavior,
		'header'=>['title'=>'Название', 'value'=>'Значение'],
		'default' =>[
			['title'=>'', 'value'=>''],
		]
	]); ?>
</div>