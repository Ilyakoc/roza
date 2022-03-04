<?php
/**
 * Виджет однострочного текстового поля формы для номера телефона. 
 * 
 */
namespace common\widgets\form;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\widgets\form\BaseField;

class PhoneField extends BaseField
{
	/**
	 * @var string единица измерения. 
	 */
	public $mask='+7 ( 999 ) 999 - 99 - 99';
	
	/**
	 * (non-PHPDoc)
	 * @see \common\components\widgets\form\BaseField::$view
	 */
	public $view='phone-field';
    
    public function run()
    {
        $jsClassName=$this->attribute . rand(0, 1000000);
        $this->htmlOptions['class']=trim($jsClassName . ' ' . A::get($this->htmlOptions, 'class', ''));
        
        Y::js(false, "jQuery('.{$jsClassName}').mask('{$this->mask}');", \CClientScript::POS_READY);
        
        parent::run();
    }
}
