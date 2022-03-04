<?php
/**
 * Виджет CheckBoxList поля формы поведения 
 * \common\ext\dataAttribute\behaviors\DataAttributeBehavior
 *  
 */
namespace settings\widgets\form;

use common\components\helpers\HArray as A;
use settings\components\helpers\HSettings;

class DataAttributeCheckBoxList extends \CWidget
{
	/**
	 * @var \CModel объект модели. 
	 */
	public $model;
	
	/**
	 * @var string имя аттрибута модели.
	 */
	public $attribute;
	
	/**
	 * @var \CActiveForm объект формы.
	 * Может быть не передан, если не используется 
	 * в шаблоне отображения.
	 */
	public $form=null;
	
	/**
	 * @var string идентификатор настроек.
	 */
	public $settingsId=null;
	
	/**
	 * @var string|\common\ext\dataAttribute\behaviors\DataAttributeBehavior 
	 * поведение модели из настроек, которое хранит значения для списка.
	 * Может быть передано только название поведения в модели настроек, 
	 * в этом случае необходимо передать $settingsId. 
	 */
	public $behavior=null;	
	
	/**
	 * @var string имя аттрибута подписи для элемента списка.
	 * По умолчанию "title".
	 */
	public $textField='title';
	
	/**
	 * @var string имя аттрибута значения для элемента списка.
	 * По умолчанию (NULL) будет получено из DataAttributeCheckBoxList::$textField
	 */
	public $valueField=null;
	
	/**
	 * @var string|NULL текст сообщения при пустом списке элементов.
	 */
	public $emptyText=null;
	
	/**
	 * @var string имя шаблона отбражения.
	 */
	public $view='data_attribute_checkbox_list';
	
	/**
	 * @var string дополнительные параметры для шаблона отображения.
	 */
	public $params=[];
	
	/**
	 * @var \CArrayDataProvider
	 */
	protected $dataProvider=null;
	
	/**
	 * (non-PHPdoc)
	 * @see CWidget::init()
	 */
	public function init()
	{
		if(!($this->form instanceof \CActiveForm)) {
			$this->form=new \CActiveForm($this->owner);
		}
		
		if(!$this->valueField) {
			$this->valueField=$this->textField;
		}
		
		return parent::init();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CWidget::run()
	 */
	public function run()
	{	
		$this->render($this->view, $this->params);
	}
	
	/**
	 * Получить поведение данных настроек.
	 * @return \common\ext\dataAttribute\behaviors\DataAttributeBehavior
	 */
	public function getDataBehavior()
	{
		if(!($this->behavior instanceof \common\ext\dataAttribute\behaviors\DataAttributeBehavior)) {
			$settings=HSettings::getById($this->settingsId);
			$this->behavior=$settings->asa($this->behavior);	
		}
		
		return $this->behavior;
	}
	
	/**
	 * Получить объект провайдера.
	 * @return \CArrayDataProvider
	 */
	public function getDataProvider()
	{
		if(!($this->dataProvider instanceof \CDataProvider)) {
			$this->dataProvider=$this->getDataBehavior()->getDataProvider(true);
		}
		
		return $this->dataProvider;
	}
	
	/**
	 * Получить массив array(значение=>название).
	 * @return array
	 */
	public function getListData()
	{
		return \CHtml::listData($this->getDataProvider()->getData(), $this->valueField, $this->textField);
	}
}