<?
/**
 * Widget for jQuery plugin Zebra_Dialog
 * @link https://github.com/stefangabos/Zebra_Dialog
 * @link http://stefangabos.ro/jquery/zebra-dialog/
 */
namespace common\widgets\ui\Zebra_Dialog;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;

class Confirm extends Base
{
	/**
	 * @var string jQuery селектор элемента, на который назначается обработчик события.
	 */
	public $selector='';
	
	/**
	 * @var string событие, по которому будет вызвано окно. 
	 */
	public $event='click';
	
	/**
	 * @var string подпись кнопки "Да".
	 */
	public $yesLabel=null;
	
	/**
	 * @var string подпись кнопки "Нет".
	 */
	public $noLabel=null;
	
	/**
	 * @var string код тела функции callback для кнопки YES.
	 * По умолчанию закрытие окна.
	 */
	public $yesCallback=null;
	
	/**
	 * (non-PHPdoc)
	 * @see \CWidget::run()
	 */
	public function run()
	{
		$t=Y::ct('\common\widgets\ui\Zebra_Dialog\Confirm.confirm');
		
		if($this->yesLabel === null) $this->yesLabel=$t('label.yes');
		if($this->noLabel === null) $this->noLabel=$t('label.no');
		
		if(!A::exists('buttons.0.caption', $this->options)) {
			$this->options['buttons'][0]['caption']=$this->yesLabel;
		}
		if(!A::exists('buttons.0.callback', $this->options)) {
			if($this->yesCallback === null) {
				$this->options['buttons'][0]['callback']='js:function() { return dlg.close(); }';
			}
			elseif(empty($this->yesCallback)) {
				$this->options['buttons'][0]['callback']='js:function() { return true; }';
			}
			else {
				$this->options['buttons'][0]['callback']='js:function() { ' . $this->yesCallback . '}';
			}
		}
		if(!A::exists('buttons.1.caption', $this->options)) {
			$this->options['buttons'][1]['caption']=$this->noLabel;
		}
		if(!A::exists('buttons.1.callback', $this->options)) {
			$this->options['buttons'][1]['callback']='js:function() { return dlg.close(); }';
		}
		else {
			$this->options['buttons'][1]['callback']='js:function() { ' . $this->options['buttons'][1]['callback'] . '}';
		}
		
		Y::js(uniqid('js'), '$(document).on("'.$this->event.'", "'.$this->selector.'", function(e) {
			e.preventDefault();
			var dlg=$.Zebra_Dialog('.\CJavaScript::encode($this->text).', '.\CJavaScript::encode($this->options).');
			return false;
		});', \CClientScript::POS_READY);
	}
}