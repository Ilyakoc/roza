<?php
/**
 * Модуль администрирования модуля
 *
 */
namespace crud\modules\admin;

use common\components\base\WebModule;
use common\components\helpers\HYii as Y;

class AdminModule extends WebModule
{
	/**
	 * (non-PHPdoc)
	 * @see CModule::init()
	 */
	public function init()
	{
		parent::init();

		// $this->assetsBaseUrl=Y::publish($this->getAssetsBasePath());
	}
}