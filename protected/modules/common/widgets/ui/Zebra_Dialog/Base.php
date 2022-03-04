<?
/**
 * Базовый класс для виджетов jQuery plugin Zebra_Dialog
 * @link https://github.com/stefangabos/Zebra_Dialog
 */
namespace common\widgets\ui\Zebra_Dialog;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HFile;

class Base extends \CWidget
{
	/**
	 * @var string тип окна. По умолчанию "confirmation".
	 * Доступны: error, warning, question, information and confirmation.
	 */
	public $type='confirmation';
	
	/**
	 * @var string заголовок окна
	 */
	public $title=null;
	
	/**
	 * @var string текст сообщения.
	 */
	public $text=null;
	
	/**
	 * @var array опции для плагина Zebra_Dialog
	 */
	public $options=[];
	
	/**
	 * @var string тема. По умолчанию "default".
	 * Доступна также "flat".
	 */
	public $theme='default';

	/**
	 * (non-PHPdoc)
	 * @see \CWidget::init()
	 */
	public function init()
	{
		Y::publish([
			'path'=>HFile::path([dirname(__FILE__), 'assets', 'base']),
			'js'=>'js/zebra_dialog.src.js',
			'css'=>'css/'.$this->theme.'/zebra_dialog.css'
		]);
		
		if(!A::exists('type', $this->options)) {
			$this->options['type']=$this->type;
		}
		if(!A::exists('title', $this->options) && ($this->title !== null)) {
			$this->options['title']=$this->title;
		}
	}
}