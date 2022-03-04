<?php
/**
 * Виджет поля ЧПУ для форм модуля администрирования
 */
use YiiHelper as Y;

class AliasFieldWidget extends \CWidget
{
	public $form;
	public $model;
	public $attributeTitle='title';
	public $attributeAlias='alias';
	
	private $_afw;
	
	public function init()
	{
		AssetHelper::publish(array(
			'path'=>__DIR__ . DS . 'assets',
			'js'=>array('js/urls.translit.js', 'js/AliasFieldWidget.js')
		));
		
		$titleActiveId=CHtml::activeId($this->model, $this->attributeTitle);
		$aliasActiveId=CHtml::activeId($this->model, $this->attributeAlias);
		$this->_afw=uniqid('afw_');
		Y::csjs($this->_afw,
			"var {$this->_afw}=new AliasFieldWidget('{$titleActiveId}','{$aliasActiveId}',".($this->model->isNewRecord ? 1 : 0).');', 
			CClientScript::POS_READY
		);
	}
	
	public function run()
	{
		$this->render('default');
	}
	
	public function getAFW()
	{
		return $this->$_afw;
	}
}