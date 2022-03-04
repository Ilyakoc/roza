<?php
/**
 * Модуль SEO
 */
use common\components\helpers\HYii as Y;
use common\components\base\WebModule;

class SeoModule extends WebModule
{
	/**
	 * (non-PHPdoc)
	 * @see CModule::init()
	 */
	public function init()
	{
		parent::init();
		
		// $this->assetsBaseUrl=Y::publish($this->getAssetsBasePath());

		$this->setImport(array(
			'seo.models.*',
			'seo.behaviors.*',
			'seo.components.*',
		));		
	}
}