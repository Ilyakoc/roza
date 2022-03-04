Подключение
-----------
1) Добавить поведение в модель (модель должны наследоваться от \common\components\base\ActiveRecord)
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;

public function behaviors()
{
    $tc=Y::ct('CommonModule.labels', 'common');
    return A::m(parent::behaviors(), [
        'publishedBehavior'=>[
            'class'=>'\common\ext\active\behaviors\ActiveBehavior',
            'attribute'=>'published',
            'attributeLabel'=>$tc('published'),
            'scopeActivlyName'=>'published',
            'scopeNotActivlyName'=>'unpublished'
        ],
    ]);
}

НЕ ЗАБЫТЬ, добавить:

	public function scopes()
	{
		return $this->getScopes([				
		]);
	},
    public function relations()
	{
		return $this->getRelations([				
		]);
	}
    public function rules()
	{
		return $this->getRules([
            ...
        ]);
    }
    public function attributeLabels()
	{    
		return $this->getAttributeLabels([
            ...
        ]);
    }    

2) Раздел администрирования. Добавить действие в контроллер.
use common\components\helpers\HArray as A;
    
public function filters()
{
    return A::m(parent::filters(), [
        'ajaxOnly +changePublished'
    ]);
}

\myModel - класс модели
Простой вариант:

public function actions()
{
    return A::m(parent::actions(), [
        'changePublished'=>[
            'class'=>'\common\ext\active\actions\AjaxChangeActive',
            'className'=>'\myModel',
            'behaviorName'=>'publishedBehavior'
        ]
    ]);
}
    
Расширенный вариант с предвалидацией onBeforeSave   

public function actions()
{
    return A::m(parent::actions(), [
        'changePublished'=>[
            'class'=>'\common\ext\active\actions\AjaxChangeActive',
            'className'=>'\myModel',
            'behaviorName'=>'publishedBehavior',
            'onBeforeSave'=>[$this, 'onBeforeSaveChangePublished']
        ]
    ]);
}

$this->loadModel() - метод поведения \common\behaviors\ARControllerBehavior
public function onBeforeSaveChangeActive(&$model) 
{
    $model=$this->loadModel('\MyModel', $model->id, true, ['select'=>'id,my_attribute']);
    
    return $model->validate();
}

Использование
-------------
1) В шаблоне формы
<? use common\components\helpers\HArray as A; ?>
<? $this->widget('\common\widgets\form\CheckboxField', A::m(compact('form', 'model'), ['attribute'=>'published'])); ?>

2) В шаблоне списка
<? $this->widget('\common\ext\active\widgets\InList', [
    'behavior'=>$model->publishedBehavior, 
    'changeUrl'=>$this->createUrl('myController/changePublished', ['id'=>$model->id]), 
    'cssMark'=>'unmarked', 
    'cssUnmark'=>'marked', 
    'wrapperOptions'=>['class'=>'mark']
]) ?>

3) В виджете zii.widgets.grid.CGridView
* "changeUrl"=>...myController/changePublished - ссылка на действие активации

    [
        'name'=>'published', 
        'type'=>'raw',
        'headerHtmlOptions'=>['style'=>'width:10%'],
        'htmlOptions'=>['style'=>'text-align:center'],
        'value'=>'$this->grid->owner->widget("\common\\\\ext\active\widgets\InList", [
            "behavior"=>$data->publishedBehavior, 
            "changeUrl"=>$this->grid->owner->createUrl("myController/changePublished", ["id"=>$data->id]), 
            "cssMark"=>"unmarked", 
            "cssUnmark"=>"marked", 
            "wrapperOptions"=>["class"=>"mark"]
        ], true)'
    ],
    
    
