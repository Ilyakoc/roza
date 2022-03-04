<?
/**
 * Подтверждение удаления
 * @link https://github.com/stefangabos/Zebra_Dialog
 * @link http://stefangabos.ro/jquery/zebra-dialog/
 */
namespace common\widgets\ui\Zebra_Dialog;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;

class ConfirmDelete extends Confirm
{
	/**
	 * (non-PHPDoc)
	 * @see \common\widgets\ui\Zebra_Dialog\Base::$type
	 */
	public $type='warning';
	
	/**
	 * (non-PHPdoc)
	 * @see \common\widgets\ui\Zebra_Dialog\Base::init()
	 */
	public function init()
	{
		$t=Y::ct('\common\widgets\ui\Zebra_Dialog\Confirm.confirm_delete');
		
		if($this->yesLabel === null) $this->yesLabel=$t('label.yes');
		if($this->noLabel === null) $this->noLabel=$t('label.no');
		if($this->title === null) $this->title=$t('title');
		if($this->text === null) {
			$this->text=$t('text');
			$this->options['width']=400;
		}
		
		if(!A::exists('buttons.0.callback', $this->options) && ($this->yesCallback === null)) {
			$this->yesCallback='$.post($("'.$this->selector.'").attr("href"), function() { window.location.href="'.$this->owner->createUrl('index').'"; }); ';
		}		
		
		parent::init();
	} 
	
	/**
	 * (non-PHPdoc)
	 * @see \common\widgets\ui\Zebra_Dialog\Confirm::run()
	 */
	public function run()
	{
		echo '<style>.ZebraDialog_Button_1 { background-color: #c9302c !important; }</style>';
		
		parent::run();
	}
}