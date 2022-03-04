<?php
/**
 * Base Rule 
 * 
 */
namespace common\components\base;

abstract class Rule extends \CBaseUrlRule
{
	/**
	 * Получить ссылку из частей маршрута.
	 * @param array $path части маршрута.
	 * @return string
	 */
	protected function getUrl($path)
	{
		return implode('/', $path);
	}
	
	/**
	 * Создание строки параметров для ссылки.
	 * @param \CUrlManager $manager the manager
	 * @param array $params list of parameters (name=>value) associated with the route
	 * @param string $ampersand the token separating name-value pairs in the URL.
	 * @param string $onlyPairs возвращать только строку переменных, без символа "?" в начале.
	 * По умолчанию FALSE (добавлять в начале строки символ "?").
	 * @return string строка параметров.
	 */
	protected function createPathInfo($manager, $params, $ampersand, $onlyPairs=false)
	{
		if(!empty($params)) {
			return ($onlyPairs ? '' : '?') . $manager->createPathInfo($params, '=', $ampersand);
		}
		
		return '';
	}
}
