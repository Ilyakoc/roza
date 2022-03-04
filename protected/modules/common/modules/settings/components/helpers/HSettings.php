<?php
/**
 * Класс-помощник для модуля "Настройки"
 */
namespace settings\components\helpers;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;

class HSettings 
{
	/**
	 * 
	 * @param string $id идентификатор настроек.
	 * @param boolean $load загружать настройки. 
	 * По умолчанию (TRUE) - загружать.
	 * @return object|NULL объект модели, либо NULL, если 
	 * возникли ошибки в конфигурации.
	 */
	public static function getById($id, $load=true)
	{
		if($className=self::param($id, 'class')) {
			$model=new $className();
			if($load) {
				$model->loadSettings();
			}
			return $model;
		}
		
		return null;
	}
	
	/**
	 * Получить конфигурацию настроек.
	 * @return array|NULL
	 */
	public static function config()
	{
		if($module=Y::module('common.settings')) {
			return $module->config;
		}
		
		return null;
	} 
	
	/**
	 * Получить значение параметра конфигурации.
	 * @param string $id идентификатор конфигурации.
	 * @param string $name имя параметра.
	 * @param mixed $default значение по умолчанию. По умолчанию NULL.
	 * @return mixed
	 */
	public static function param($id, $name, $default=null)
	{
		if($config=self::config()) {
			if($params=A::get($config, $id)) {
				return A::get($params, $name, $default);
			}
		}
		
		return $default;
	}
	
	/**
	 * Получить пункты меню для виджета zii.widgets.CMenu (для раздела администрирования).
	 * @param \CController $controller объект контроллера,
	 * который будет использован для создания ссылки.
	 * @param string|array|NULL $id идетификатор настроек 
	 * для которых возвращать пункт меню. Может быть передан массив идентфикаторов.
	 * По умолчанию (NULL) - возвращать все пункты меню. 
	 * @param string $baseUrl базовая ссылка для пунктов меню.
	 * @param boolean $returnItem возвращать только конфигурацию одного пункта меню. 
	 * По умолчанию (FALSE) - если результат будут содеражать только один пункт меню, 
	 * возвратиться как массив из одного пункта меню.
     * @return array|mixed
     */
	public static function getMenuItems($controller, $id=null, $baseUrl='settings/admin/default/index', $returnItem=false)
	{
		$items=[];
		
		$module=Y::module('common.settings.admin');
		
		if($config=self::config()) {
			// @var callable получить пункт меню.
			$fAddItem=function($id, $params) use (&$items, $controller, $baseUrl, $config) {
				if($label=A::get($params, 'menuItemLabel', A::get($params, 'title'))) {
					$items[]=[
						'label'=>$label,
						'url'=>[$baseUrl, 'id'=>$id]
					];
				}
			};
			
			if(is_string($id)) {
				$fAddItem($id, A::get($config, $id));
			}
			else {
				if(is_array($id)) {
					$config=array_intersect_key($config, array_flip($id));
				}
				
				foreach($config as $id=>$params) {
					$fAddItem($id, $params);
				}
			}
		}
		
		if($returnItem) return array_pop($items);
		else return $items;
	}
}