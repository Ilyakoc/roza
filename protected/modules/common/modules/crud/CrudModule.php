<?php
/**
 * Модуль CRUD
 */
use common\components\helpers\HYii as Y;
use common\components\base\WebModule;

class CrudModule extends WebModule
{
	/**
	 * @var array конфигурация моделей.
	 * 
	 * Формат конфигурации:
	 * <id настроек> => <путь к файлу настроек>
	 * "id настроек" - может быть только строковым значением.
	 * Пример файла настроек общего файла crud.config.crud.main 
	 * Пример файла настроек модели crud.config.crud.example
	 * 
	 * СПЕЦИФИКАЦИЯ
	 * Массив конфигурации может: 
	 * - либо содержать только путь к файлу глобальной общей конфигурации
	 * 'config' => 'application.config.crud'
	 * 
	 * Глобальный файл общей конфигурации должен возвращать массив,
	 * аналогичный (2). 
	 * (2) - либо содержать ТОЛЬКО список путей к файлам конфигураций 
	 * 'config' => [
	 * 		'application.modules.mymodule.config.crud.main',
	 * 		'application.modules.mymodule2.config.crud.main',
	 * 		'mymodel'=>'application.modules.mymodule.config.crud.mymodel',
	 * 		'application.modules.mymodule3.config.crud.main',
	 * 		...
	 * ]
	 * 
	 * Каждый файл конфигурации может быть либо общим, либо модели.
	 * Общий файл конфигурации может содержать ТОЛЬКО МАССИВ ПУТЕЙ 
	 * к файлам конфигурации моделей. 
	 * [
	 * 		'mymodel'=>'application.modules.mymodule.config.crud.mymodel'
	 * 		'mymodel2'=>'application.modules.mymodule.config.crud.mymodel2'
	 * ]
	 */
	public $config=[];

	/**
	 * (non-PHPdoc)
	 * @see CModule::init()
	 */
	public function init()
	{
		parent::init();
		
		// $this->assetsBaseUrl=Y::publish($this->getAssetsBasePath());

		$this->setImport(array(
			'crud.models.*',
			'crud.behaviors.*',
			'crud.components.*',
		));		
	}
}