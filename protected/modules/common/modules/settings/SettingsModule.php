<?php
/**
 * Модуль "Настройки"
 *
 */
use common\components\base\WebModule;

class SettingsModule extends WebModule
{
	/**
	 * @var array конфигурация моделей.
	 * Формат конфигурации:
	 * <id настроек> => array(
	 * 		'class' => (обязательный) имя класса модели настроек, 
	 * 		наследуемый от \settings\components\base\SettingsModel,
	 * 
	 * 		'title' => заголовок настроек в разделе администрирования.
	 *
	 * 		'menuItemLabel' => заголовок пункта меню данных настроек 
	 * 		в разделе администрирования.
	 *
	 * 		'breadcrumbs' => дополнительный массив для хлебных крошек в формате 
	 *		array([title=>url], ...), либо array([title=>[url, param=>value]], ...)
	 * 
	 * 		'viewForm' => путь к шаблону формы редактирования настроек 
	 * 		в разделе администрирования. Основа шаблона может быть 
	 * 		взята из settings.views.default._form 
	 * )
	 */
	public $config=[];
	
	/**
	 * (non-PHPdoc)
	 * @see CModule::init()
	 */
	public function init()
	{
		// import the module-level models and components
		$this->setImport(array(
			'settings.models.*',
			'settings.components.*',
		));
	}
}
