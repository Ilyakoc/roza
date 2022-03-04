<?php
namespace common\ext\cache\components;

use common\components\helpers\HYii as Y;
use common\ext\cache\interfaces\IAdapter;

class Cache extends \CComponent
{
	/**
	 * @var string имя класса адаптера, либо конфигурация инициализации поведения.
	 */
	public $adapter;
	
	/**
	 * @var \common\ext\cache\interfaces\IAdapter объект адаптера.
	 */
	protected $_adapter;
	
	/**
	 * (non-PHPdoc)
	 * @see \CComponent::init()
	 */
	public function init()
	{
		$adapter=$this->attachBehavior('adapterBehavior', $this->adapter);
		
		if(!($adapter instanceof IAdapter)) {
			$t=Y::ct('\common\ext\cache\Messages.components/cache', 'common');
			throw new \CException($t('error.invalidAdapterClass', ['{class}'=>$this->class]));
		}
	}
}