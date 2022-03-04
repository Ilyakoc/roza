<?php
namespace common\ext\dataAttribute\widgets;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HFile;
use common\components\helpers\HHtml;

class DataAttribute extends \common\components\base\Widget
{
	/**
	 * @var \common\ext\dataAttribute\behaviors\DataAttributeBehavior поведение табличного атрибута.
	 */
	public $behavior;
	
	/**
	 * @var string имя атрибута
	 */
	public $attribute=null;
	
	/**
	 * Массив заголовков данных
	 * "active" - is RESERVED KEY!
	 * array(key=>title)
	 * @var array
	 */
	public $header=[];
	
	/**
	 * Типы полей.
	 * @var array array(name=>type) 
	 *  
	 * Сложный тип передается как:
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
	 * default: всегда брать значение из списка значений по умолчанию. Только для значений ReadOnly.
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
	 */
	public $types=[];
	
	/**
	 * @var array подписи, будут добавлены после отображения значения.
	 * Только для полей ReadOnly.
	 * Пример: для задания подписи только для второй строки
	 * array([], ["мой дополнительный текст"])
	 */
	public $notes=[];
	
	/**
	 * @var array|boolean список разрешенных типов. По умолчанию 
	 * все типы разрешены (TRUE).
	 * ПРИМЕЧАНИЕ: На данный момент не используется.
	 */
	public $allowTypes=true;

	/**
	 * Список ключей, которые будут только для чтения.
	 * Игнорируется ключ "active".
	 * array(key)
	 * @var array
	 */
	public $readOnly=[];
	
	/**
	 * Данные по умолчанию
	 * Индексы элементов должны совпадать индексами массива загловков.
	 * array(array(key=>value)) 
	 * @var array
	 */
	public $default=[];
	
	/**
	 * @var string|boolean обновить данные из данных по умолчанию.
	 * Только для полей ReadOnly. По умолчанию (FALSE) - не обновлять.
	 * Может быть передано (TRUE) - данные будут полностью обновлены данными по умолчанию.
	 * Может быть передано имя ключа, по которому проверять обновление, если элемента 
	 * со значением ключа нет в данных, будет добавлен новый элемент из данных по умолчанию.
	 */
	public $refreshDefault=false;
	
	/**
	 * @var boolean безопасный режим обновления данных из данных по умолчанию.
	 * В небезопасном режиме (FALSE) сохраненные данные, ключей которых нет в
	 * данных по умолчанию будут удалены.
	 * По умолчанию (TRUE) - безопасный режим (только добавление новых записей).
	 */
	public $refreshDefaultSafe=true;

	/**
	 * @var string подпись для колонки активности.
	 */
	public $activeLabel='Отображать на сайте';
	
	/**
	 * @var boolean значение активности по умолчанию. 
	 * Будет перезаписано, если установлено в DataAttributeWidget::$default.
	 */
	public $defaultActive=false;

	/**
	 * @var boolean скрыть колонку активности
	 */
	public $hideActive = false;
	
	/**
	 * Не отображать кнопку добавления
	 * @var boolean
	 */
	public $hideAddButton = false;

	/**
	 * @var boolean не отображать кнопку удаления
	 */
	public $hideDeleteButton=false;

	/**
     * @var boolean включить сортировку плагином JQuery sortable(). По умолчанию TRUE.
     */
    public $enableSortable=true;
    
    /**
     * @var array HTML-атрибуты для DOM-элемента обертки.
     */
    public $wrapperOptions=[];
    
    /**
     * @var string имя шаблона отображения.
     */
    public $view='default';
    
    /**
     * @var string дополнительные данные для шаблона отображения.
     */
    public $viewData=[];
    
    /**
     * @var string пусть к шаблонам отображения типов.
     */
    public $typeViewPath='common.ext.dataAttribute.widgets.views.types.';
    
    /**
     * (non-PHPdoc)
     * @see \CWidget::init()
     */
	public function init()
	{
		if(!$this->attribute) $this->attribute=$this->behavior->attribute;
		
		Y::publish([
			'path' => HFile::path([__DIR__, 'assets']),
			'js' => 'js/DataAttribute.js',
			'css' => 'css/default.css'
		]);
		
		Y::js(
			'DataAttribute' . $this->attribute, 
			'DataAttribute.init({attribute: "'.$this->attribute.'", enableSortable: '.($this->enableSortable ? 'true' : 'false').'});',
			\CClientScript::POS_END
		);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \CWidget::run()
	 */
	public function run()
	{
		$this->render($this->view, $this->viewData);
	}
	
	/**
	 * Получить данные
	 */
	public function getData()
	{
		if($data=$this->behavior->get()) {
			if($this->refreshDefault === true) return $this->default;
			elseif($this->refreshDefault) {
				$refreshKey=$this->refreshDefault;
				$defaults=array_filter($this->default, function($item) use ($refreshKey) {
					return A::get($item, $refreshKey);
				});
				$updateData=array_filter($defaults, function($item) use ($data, $refreshKey) {
					foreach($data as $dataItem) {
						if(A::get($item, $refreshKey) == A::get($dataItem, $refreshKey)) return false;
					}
					return true;
				});
				
				$data=A::m($data, $updateData);
				if(!$this->refreshDefaultSafe) {
					$data=array_filter($data, function($item) use ($refreshKey) {
						foreach($this->default as $defaultItem) {
							if(A::get($item, $refreshKey) == A::get($defaultItem, $refreshKey)) return true;
						}
						return false;
					});
				}
			}
			
			return $data;
		}
		
		return $this->default;
	}
	
	/**
	 * Устаревший метод. Не рекомендуется к использованию.
	 * Get row data by index.
	 * @param integer $index row index
	 * @return array
	 */
	public function getRowData($index) 
	{
		return A::get($this->behavior->owner->{$this->$attribute}, $index, A::get($this->default, $index, null));		
	}
	
	/**
	 * Generate row HTML code. 
	 * @param integer $index row index. Если передано значение NULl, 
	 * генерится код шаблона для новых элементов.
	 * @param array $data row data. 
	 * @return string html code
	 */
	public function generateRow($index, $data=array()) 
	{
		$tbtn=Y::createT('CommonModule.btn', 'common');
		
		$name=\CHtml::modelName($this->behavior->owner) . "[{$this->attribute}]";
	
		$isTemplate = is_null($index);
		if(is_null($index)) $index = '{{daw-index}}';
		
		$html = '<tr>';
		if($this->hideActive) {
			$html .= \CHtml::hiddenField($name . "[{$index}][active]", A::get($data, 'active', $this->defaultActive), array(
				'disabled'=>$isTemplate
			));			
		}
		else {
			$html .= '<td align="center">';
			$html .= \CHtml::checkBox($name . "[{$index}][active]", A::get($data, 'active', $this->defaultActive), array(
				'class'=>'daw-inpt-active',
				'title'=>$this->activeLabel,
				'value'=>1,
				'disabled'=>$isTemplate,
				'onclick'=>in_array('active', $this->readOnly) ? 'return false;' : ''
			));
			$html .= '</td>';
		}
	
		foreach($this->header as $key=>$title) {
			$rowHtmlOptions=[];
			$htmlRow='';
			
			$value=null;
			if(!is_null($index) && ($value=A::get($data, $key)) && is_string($value)) {
				$value=HHtml::q($value);
			}
			
			if(in_array($key, $this->readOnly)) {
				if(is_string($value)) {
					if(A::get($this->types, $key) == 'default') {
						$value=A::get(A::get($this->default, $index, []), $key);
					}
					$htmlRow .= \CHtml::hiddenField($name . "[{$index}][{$key}]", $value, array('class'=>'daw-inpt', 'readonly'=>true, 'disabled'=>$isTemplate));
					$htmlRow .= $value . A::get(A::get($this->notes, $index, []), $key, '');
				}
			}
			else {
				$typeData=null;
				$typeView=null;
				$typeDefault=null;
				$typeParams=[];
				if($type=A::get($this->types, $key)) {
					if(is_array($type)) {
						$typeData=A::get($type, 'data');
						$typeDefault=A::get($type, 'default'); 
						$typeView=A::get($type, 'view');
						$typeParams=A::get($type, 'params', []); 
						$type=A::get($type, 'type');
						
						if($ajaxTplUrl=A::get($typeParams, 'ajax-tpl-url')) {
							$rowHtmlOptions['data-ajax-tpl-url']=$ajaxTplUrl;
						}
					}
				}
				else $type='string';
				
				if(is_string($type)) {
					if($isTemplate && ($type == 'model')) {
						$htmlRow .= '';
					}
					else {
						if(!$typeView) $typeView=$this->typeViewPath.$type;
						$params=[
							'name'=>$name . "[{$index}][{$key}]",
							'value'=>($value === null) ? $typeDefault : $value,
							'view'=>$typeView,
							'isTemplate'=>$isTemplate,
							'data'=>$typeData,
							'params'=>$typeParams
						];
						
						if($type == 'model') {
							if(empty($value) && ($modelClass=A::get($typeParams, 'class'))) {
								$params['model']= new $modelClass();
							}
							else {
								$params['model']=is_object($value) ? $value : (is_string($value) ? unserialize($value) : (object)$value);						
							}
						}
						
						$htmlRow .= $this->render($typeView, $params, true);
					}
				}
				else {
					$htmlRow .= '*Invalid type*';
				}
			}
			
			$html .= \CHtml::tag('td', $rowHtmlOptions, $htmlRow);
		}
	
	    if(!$this->hideDeleteButton)
			$html .= '<td align="center"><button class="btn btn-danger btn-sm daw-btn-remove">'.$tbtn('remove').'</button></td>';

		$html .= '</tr>';
	
		return $html;
	}	
}
