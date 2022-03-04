<?php
namespace common\ext\active\widgets;

use common\components\helpers\HYii as Y;

class InList extends \CWidget
{
	/**
	 * @var \common\ext\active\behaviors\ActiveBehavior поведением активности
	 */
	public $behavior;
	
	/**
	 * @var string ссылка на действие смены активности.
	 */
	public $changeUrl;
	
	 /**
     * @var string тэг общей обертки 
     */
    public $wrapperTagName='div';
    
    /**
     * @var array HTML атрибуты для элемента общей обертки
     */
    public $wrapperOptions=array();
    
    /**
     * @var string css класс для активного элемента.
     */
    public $cssMark='marked'; 
    
    /**
     * @var string css класс для неактивного элемента.
     */
    public $cssUnmark='unmarked'; 
	
    /**
     * (non-PHPdoc)
     * @see CWidget::init()
     */
    public function init()
    {
    	Y::js(md5($this->cssMark.$this->cssUnmark), 
'$(document).on("click", ".js-active-mark", function() {
    var $this=$(this);
    $.post($this.data("url"), function(data) {
		if(data.success) {
    		if($this.hasClass("'.$this->cssUnmark.'")) {
   				$this.removeClass("'.$this->cssUnmark.'");
    			$this.addClass("'.$this->cssMark.'");
    		}
    		else {
    			$this.removeClass("'.$this->cssMark.'");
    			$this.addClass("'.$this->cssUnmark.'");
            }
    	}
    	else if(data.hasErrors) {
    		var msg="";
    		for(var error in data.errors) {
    			msg+=data.errors[error]+"\n";
    		}
    		$.fancybox($.parseHTML("<p>"+msg+"</p>"));	
    	}
    }, "json"); 
	return false; 
});', 
    		\CClientScript::POS_READY
    	);
    }
    
	/**
	 * (non-PHPdoc)
	 * @see CWidget::run()
	 */
	public function run()
	{
		$cssClass=isset($this->wrapperOptions['class']) ? $this->wrapperOptions['class'] . ' ' : '';  
		
		$this->wrapperOptions['class']=$cssClass . 'js-active-mark ' . ($this->behavior->isActive() ? $this->cssMark : $this->cssUnmark);
		$this->wrapperOptions['data-url']=$this->changeUrl;
				
		$this->render('in_list');
	}
}
