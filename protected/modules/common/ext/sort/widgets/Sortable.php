<?php
/**
 * Виджет. Подключение сортировки элементов. 
 */
namespace common\ext\sort\widgets;

use common\components\helpers\HYii as Y;
use common\components\helpers\HFile;
use common\components\helpers\HHash;

class Sortable extends \CWidget
{
	/**
	 * @var string id виджета. 
	 * По умолчанию NULL - будет сгенерирован автоматически.
	 * Для использования на одной странице разных сортировок ID 
	 * виджета должны отличатся друг от друга.
	 */
	public $id=null;
	
	/**
	 * @var array параметры для js класса CommonExtSortWidgetSortable.
	 * Параметры будут отформатированы \CJavaScript::encode()
	 * Доступны следующие параметры:
	 * "category" (string) имя категории сортировки.
 	 * "key" (int|null) ключ категории сортировки.
     * "level" (int) уровень. может использоваться при постраничной сортировке.
 	 * "selector" (string) выражение выборки (jQuery) родительского элемента.
 	 * "saveUrl" (string) ссылка на действие сохранения
 	 * "dataId" (string) имя атрибута сортировки, в котором будут хранится id модели.
 	 * По умолчанию "data-sort-id".
 	 * "autosave" (boolean) автоматически сохранять сортировку. 
 	 * По умолчанию (TRUE) - сохранять.
 	 * "onAfterSave" (callable) обработчик после сохранения 
 	 * function(PlainObject data, String textStatus, jqXHR jqXHR).  
	 */
	public $options=[];
	
	/**
	 * @var boolean инициализировать сортировку. 
	 * По умолчанию (TRUE) - инициализировать.
	 */
	public $initialize=true;
	
	/**
	 * @var boolean регистрировать библиотеку ядра "jquery.ui".
	 * По умолчанию (TRUE) - инициализировать.
	 */
	public $registerUi=true;
	
	/**
	 * (non-PHPdoc)
	 * @see \CWidget::init()
	 */
	public function init()
	{
		if(!$this->id) $this->id=HHash::ujs();
		
		Y::publish([
			'path'=>HFile::path([dirname(__FILE__), 'assets']),
			'js'=>'js/Sortable.js'
		]);
		
		if($this->registerUi) {
			Y::cs()->registerCoreScript('jquery.ui');
		}
	}
	
	/**
	 * Получить имя js-переменной объекта класса 
	 * виджета сортировки CommonExtSortWidgetSortable.
	 * @return string
	 */
	public function getJsVar()
	{
		return 'window.'.$this->id.'v';		
	}
	
	/**
	 * Получить js-код инициализации сортировки.
	 * @return string
	 */
	public function getJsInit()
	{
		return $this->getJsVar().'.init();';		
	}
	
	/**
	 * Получить js-код сохранения сортировки.
	 * @return string
	 */
	public function getJsSave()
	{
		return $this->getJsVar().'.save();';		
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \CWidget::run()
	 */
	public function run()
	{	
		$js=';'.$this->getJsVar().'=new CommonExtSortWidgetSortable('.\CJavaScript::encode($this->options).');';
		if($this->initialize) {
			$js.=$this->getJsInit();
		}
		
		Y::js($this->id, $js, \CClientScript::POS_READY);
	}
}
