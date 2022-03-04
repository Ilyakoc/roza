<?php
/**
 * Переключатель Bootstrap Switch
 * 
 * @see http://www.bootstrap-switch.org/
 * @see https://github.com/nostalgiaz/bootstrap-switch/
 */
namespace common\widgets\form;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;

class SwitchField extends \CWidget
{
	/**
	 * @var string имя checkbox элемента.
	 */
	public $name;
	
	/**
	 * @var boolean значение параметра "checked" checkbox элемента.
	 */
	public $checked=false;
	
	/**
	 * @var string имя метки
	 */
	public $label=false;
	
	/**
	 * @var boolean отображать текст метки перед switch-элементом. 
	 * По умолчанию FALSE (отображать после switch-элемента). 
	 */
	public $labelBefore=false;
	
	/**
	 * @var string использовать \CHtml::encode() для метки. 
	 * По умолчанию TRUE (использовать).
	 */
	public $labelEncode=true;
	
	/**
	 * @var array опции чекбокса
	 */
	public $htmlOptions=[];
	
	/**
	 * @var array опции для компонента bootstrapSwitch.
	 * @see http://www.bootstrap-switch.org/options.html
	 */
	public $switchOptions=[
		'onColor'=>'success', 
		'offColor'=>'danger',
		'size'=>'mini'
	];
	
	/**
	 * @var string имя тэга обертки
	 */
	public $wrapperTag='div';
	
	/**
	 * @var array html-опции обертки.
	 */
	public $wrapperOptions=[
		'class'=>'checkbox'
	];

	/**
	 * @var string|NULL js код на событие switchChange.bootstrapSwitch.
	 */
	public $onSwitchChange=null;
	
	/**
	 * (non-PHPdoc)
	 * @see \CWidget::init()
	 */
	public function init()
	{	
		Y::publish([
			'path'=>dirname(__FILE__) . Y::DS . 'assets' . Y::DS . 'switch-field',
			'js'=>'bootstrap-switch/bootstrap-switch.min.js',
			'css'=>'bootstrap-switch/bootstrap-switch.min.css'
		]);
		
		parent::init();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \CWidget::run()
	 */
	public function run() 
	{
		Y::js(
			$this->getCheckboxId(), 
			'$("#'.$this->getCheckboxId().'").bootstrapSwitch('.\CJavaScript::encode($this->switchOptions).');',
			\CClientScript::POS_READY
		);

		if($this->onSwitchChange) {
			Y::js(
				$this->getCheckboxId().'_onswitchchange', 
				'$(document).on("switchChange.bootstrapSwitch", "#'.$this->getCheckboxId().'", '.\CJavaScript::encode($this->onSwitchChange).');'
			);
		}
		
		$this->render('switch-field');	
	}
	
	/**
	 * Получить id checkbox элемента.
	 * @return string
	 */
	public function getCheckboxId()
	{
		return A::get($this->htmlOptions, 'id', \CHtml::getIdByName($this->name));
	}
} 
