<?php
/**
 * Виджет поля формы "Редактор TinyMce". 
 * 
 */
namespace common\widgets\form;

use common\components\widgets\form\BaseField;

// @const int режим отображения редактора "полный функционал".
defined('TINYMCEFIELD_MODE_FULL') or define('TINYMCEFIELD_MODE_FULL', 1);

class TinyMceField extends BaseField
{
	/**
	 * @var int режим отображения редактора.
	 * @todo в процессе разработки.
	 */
	public $mode=TINYMCEFIELD_MODE_FULL;
	
	/**
	 * @var bool отображать полноценный редактор. По умолчанию TRUE.
	 * Если передано FALSE будет отображен сокращенный тип редактора.
	 * @todo введено для поддержания старых версий данного виджета.
	 * @todo В новом необходимо использовать параметр TinyMceField::$mode
	 */
	public $full=true;
	
	/**
	 * @var bool отображать блок формы загрузки картинок. 
	 * По умолчанию (TRUE) отображать.
	 **/
	public $uploadImages=true;
	
	/**
	 * @var bool отображать блок формы загрузки файлов. 
	 * По умолчанию (TRUE) отображать. 
	 */
	public $uploadFiles=true;
	
	/**
	 * @var bool отображать блок аккордиона. По умолчанию (TRUE) отображать.
	 */
	public $showAccordion=true;
	
	/**
	 * (non-PHPDoc)
	 * @see \common\components\widgets\form\BaseField::$htmlOptions
	 */
	public $htmlOptions=[
		'class'=>'big'
	];
	
	/**
	 * (non-PHPDoc)
	 * @see \common\components\widgets\form\BaseField::$view
	 */
	public $view='tinymce-field';
	
	/**
	 * (non-PHPdoc)
	 * @see \common\components\widgets\form\BaseField::run()
	 */
	public function run()
	{	
		if(!$this->full) {
			$this->uploadImages=false;
			$this->uploadFiles=false;
			$this->showAccordion=false;
		}
		
		parent::run();
	}
}