/**
 * Документация использования расширения common\ext\sort
 */
Общие положения:
а) Категория (category[string]) имя группы сортировки.
б) Ключ (key[integer]) может использован для разделения основной группы (категории) сортировки на подгруппы (подкатегории).
Например:
    Для сортировки товаров в категориях магазина, можно использовать одно имя общей категории "shop_category", 
    а в качестве ключа передавать ID самой категории, для которой сохраняется сортировка.


1) Подключить поведение в модель.
    
    public function behaviors()
    {
        return [
            ...
        	'sortBehavior'=>['class'=>'\common\ext\sort\behaviors\SortBehavior']
            ...
        ];
    }

2) Администрирование (сохранение сортировки)

2.1) Подключить действие к контроллеру
    use common\components\helpers\HArray as A;
    
    public function actions()
	{
		return A::m(parent::actions(), [
			'saveMySort'=>[
				'class'=>'\common\ext\sort\actions\SaveAction',
				'categories'=>['my_category']
			]	
		]);
	}
    
    Параметр "categories" используется для задания разрешенных категорий, которые будет обрабатывать данное действие.
    
2.2) На странице с элементами подключить виджет
    
    <? $this->widget('\common\ext\sort\widgets\Sortable', [
        'category'=>'my_category',
        'key'=>$myKey,
        'actionUrl'=>$this->createUrl('saveMySort'),
        'selector'=>'.my-sort-wrapper'
    ]); ?>

	---------------
    В новой версии параметры передаются в параметре $options
    /**
     * @var array параметры для js класса CommonExtSortWidgetSortable.
     * Параметры будут отформатированы \CJavaScript::encode()
     * Доступны следующие параметры:
     * "category" (string) имя категории сортировки.
     * "key" (int|null) ключ категории сортировки.
     * "selector" (string) выражение выборки (jQuery) родительского элемента.
     * "saveUrl" (string) ссылка на действие сохранения
     * "dataId" (string) имя атрибута сортировки, в котором будут хранится id модели.
     * По умолчанию "data-sort-id".
     * "autosave" (boolean) автоматически сохранять сортировку.
     * По умолчанию (TRUE) - сохранять.
     * "onAfterSave" (callable) обработчик после сохранения
     * function(PlainObject data, String textStatus, jqXHR jqXHR).
     */

	Пример:
    <? $this->widget('\common\ext\sort\widgets\Sortable', [
        'registerUi'=>false,
        'options'=>[
            'category'=>'mycategory',
            'saveUrl'=>$this->createUrl('saveMySort'),
            'selector'=>'#my-grid table tbody',
        ]

    ]); ?>

    ---------------

    Параметр "key" передается, только в случае, если используются ключи для категории.
    
    В параметре "selector" задается jQuery выражение для выборки родительского элемента.
    
    DOM-элементы, которые будут учитываться при сортировки должны содержать параметр "data-sort-id", значение которого, должно быть ID модели.
    Например: <li data-sort-id="<?= $data->id; ?>">
    
    Имя данного параметра можно сменить, указав параметр виджета: 'dataSortIdName'=>'my-data-sort-id'
    
3) Получение отортированных моделей.

Для добавления условия сортировки используйте метод поведения scopeSort()

Пример:
    $dataProvider=MyModel::model()
		->scopeSort('my_category')
		->getDataProvider();

Пример добавления условия для \CDbCriteria:
	$criteria->scopes['scopeSort']=['my_category', $myKey];
