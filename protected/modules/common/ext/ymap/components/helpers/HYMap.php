<?php
/**
 * Класс-помощник HYMap
 * 
 *  
 */
namespace common\ext\ymap\components\helpers;

use common\components\helpers\HYii as Y;

class HYMap
{
	/**
	 * @var string имя компонента кэширования.
	 * По умолчанию "cache". Рекомендуется установить в "dbcache".
	 * Подключить в конфигурации.
	 * 'dbcache'=>['class'=>'system.caching.CDbCache'],
	 */
	public static $cacheComponentName='dbcache';
	
	/**
	 * @access private
	 * @var string|false путь к опубликованным ресурсам данного раширения. 
	 */
	private static $_assetsUrl = false;
	
	/**
	 * Опубликовать основные ресурсы расширения.
	 */
	public static function publishAssets()
	{
		self::$_assetsUrl = Y::publish(\Yii::getPathOfAlias('common.ext.ymap.assets'));
	}
	
	/**
	 * Зарегистрировать скрипт яндекс-карты.
	 */
	public static function registerYMap()
	{
		Y::jsFile('https://api-maps.yandex.ru/2.1/?lang=ru_RU', \CClientScript::POS_HEAD);
		/*if(!self::$_assetsUrl) {
			self::publishAssets();
		}
		
		Y::jsFile(self::$_assetsUrl . '/js/y-map.min.js', \CClientScript::POS_HEAD);*/
	}
	
	/**
	 * 
	 * @param string $hash
	 * @return string
	 */
	public static function getGeoSearchCacheId($hash)
	{
		return md5($hash);
	}
	
	/**
	 * Получить компонент кэша.
	 * @return \CCache
	 */
	public static function getCacheComponent()
	{
		$cacheName=static::$cacheComponentName;
		return \Yii::app()->$cacheName;
	}
	
	/**
	 * 
	 * @param string $hash
	 * @return mixed|false
	 */
	public static function getGeoSearchData($hash)
	{
		return static::getCacheComponent()->get(static::getGeoSearchCacheId($hash));
	}
	
	/**
	 * 
	 * @param string $hash
	 * @param array $data
	 */
	public static function setGeoSearchData($hash, $data)
	{
		static::getCacheComponent()->set(static::getGeoSearchCacheId($hash), $data);
	}
}
