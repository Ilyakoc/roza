<?php
/**
 * Zelect. 
 * Стилизированный выпадающий список с поиском. 
 */
namespace common\widgets\form;

use common\components\helpers\HYii as Y;

class Zelect extends \CWidget
{
	/**
	 * @var string jQuery селектор элемента списка. 
	 */
	public $selector;
	
	/**
	 * @var string направление отображения списка ('down', 'up'). По умолчанию 'down'.
	 */
	public $drop='down';
	
	/**
	 * (non-PHPdoc)
	 * @see \CWidget::init()
	 */
	public function init()
	{
		Y::publish([
			'path'=>dirname(__FILE__) . Y::DS . 'assets' . Y::DS . 'zelect',
			'js'=>'js/zelect.js',
			'css'=>($this->drop == 'up') ? 'css/base-up.css' : 'css/base.css'
		]);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \CWidget::run()
	 */
	public function run()
	{
		Y::js(uniqid('js'), "$(\"{$this->selector}\").zelect({searchPos: '".(($this->drop=='up') ? 'bottom' : 'top')."'});", \CClientScript::POS_READY);
	}
}
 