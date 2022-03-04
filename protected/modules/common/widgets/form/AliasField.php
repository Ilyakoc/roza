<?php
/**
 * Виджет поля ЧПУ для форм модуля администрирования
 * 
 * ГОСТ 16876-71
 * @see http://textpattern.ru/html/transliteration-tables.htm
 * 
 */
namespace common\widgets\form;

use common\components\widgets\form\BaseField;
use common\components\helpers\HYii as Y;

class AliasField extends BaseField
{
	/**
	 * @var string имя атрибута ЧПУ модели.
	 * @see \common\components\widgets\form\BaseField::$attribute
	 */
	public $attribute='alias';
	
	/**
	 * @var string имя атрибута модели заголовка, 
	 * откуда будет браться значение для транслитерации.
	 */
	public $attributeTitle='title';
	
	/**
	 * @var string|NULL значение id поля заголовка. 
	 * По умолчанию (NULL) будет получено \CHtml::activeId().
	 */
	public $titleActiveId=null;

	/**
	 * @var string|NULL имя атрибута ЧПУ модели. По умолчанию NULL.
	 * @todo Введено для поддержки старых версий.
	 * Если задано, то будет использовано, вместо AliasField::$attribute.
	 */
	public $attributeAlias=null;

	/**
	 * @var string|NULL значение id поля ЧПУ. 
	 * По умолчанию (NULL) будет получено \CHtml::activeId().
	 */
	public $aliasActiveId=null;

	/**
	 * @var bool отображать кнопку обновления. По умолчанию TRUE.
	 * Если модель новая (\CActiveRecord::$isNewRecord==TRUE)
	 * кнопка в стандартном шаблоне отображена не будет, но будет 
	 * происходить автоматическое заполнение транслитерацией из поля заголовка. 
	 */
	public $btnUpdate=true;
	
	/**
	 * @var string|NULL текст кнопки обновления. По умолчанию (NULL) 
	 * будет использовано \Yii::t('CommonModule.btn', 'reload').  
	 */
	public $btnLabel=null;
	
	/**
	 * @var array дополнительные HTML-атрибуты для элемента кнопки обновления.
	 */
	public $btnOptions=[
		'class'=>'btn btn-default'
	];
	
	/**
	 * (non-PHPDoc)
	 * @see \common\components\widgets\form\BaseField::$htmlOptions
	 */
	public $htmlOptions=[
		'class'=>'form-control inline',
		'size'=>160,
		'maxlength'=>255
	];
	
	/**
	 * (non-PHPDoc)
	 * @see \common\components\widgets\form\BaseField::$view
	 */
	public $view='alias-field';
	
	/**
	 * (non-PHPdoc)
	 * @see \common\components\widgets\form\BaseField::init()
	 */
	public function init()
	{
		if(!empty($this->attributeAlias)) {
			$this->attribute=$this->attributeAlias;
		}
		
		parent::init();
		
		Y::publish(array(
			'path'=>__DIR__ . Y::DS . 'assets' . Y::DS . 'alias-field',
			'js'=>array('js/urls.translit.js', 'js/AliasField.js')
		));
	
	    if(!$this->titleActiveId) {
			$this->titleActiveId=\CHtml::activeId($this->model, $this->attributeTitle);
		}
		if(!$this->aliasActiveId) {
			$this->aliasActiveId=\CHtml::activeId($this->model, $this->attribute);
		}
		Y::js(uniqid('afw_'),
			"AliasField.init('{$this->titleActiveId}','{$this->aliasActiveId}',".($this->model->isNewRecord ? 1 : 0).');',
			\CClientScript::POS_READY
		);
		
		$this->btnOptions['data-js']='afw-btn-update';
		
		if($this->btnLabel === null) {
			$this->btnLabel=\Yii::t('CommonModule.btn', 'reload');
		}
	}
}