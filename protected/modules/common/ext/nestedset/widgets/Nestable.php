<?php
/**
 * Nestable widget
 * Виджет редактирования вложенной структуры
 * @see https://github.com/dbushell/Nestable
 */
namespace common\ext\nestedset\widgets;

class Nestable extends BaseNestable
{
	/**
	 * @var boolean использовать скин dd3
	 */
	public $skinDd3=false;
	 
	/**
	 * (non-PHPdoc)
	 * @see \CWidget::run()
	 */
	public function run()
	{
		$this->render('common.ext.nestedset.widgets.views.' . ($this->skinDd3 ? 'dd3' : 'default'));
	}
}