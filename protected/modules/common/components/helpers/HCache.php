<?php
/**
 * Cache helper
 */
namespace common\components\helpers;

class HCache
{
	/**
	 * @const integer время в секундах 1 год (365 дней) 
	 */
	const YEAR = 31536000;
	
	/**
	 * @const integer время в секундах 1 месяц (30 дней) 
	 */
	const MONTH = 2592000;
	
	/**
	 * @const integer время в секундах 1 неделя (7 дней) 
	 */
	const WEEK = 604800;
	
	/**
	 * @const integer время в секундах 1 день (24 часа) 
	 */
	const DAY = 86400;
	
	/**
	 * @const integer время в секундах 1/2 дня (12 часов) 
	 */

	const HALFDAY = 43200;
	
	/**
	 * @const integer время в секундах 1 час (60 минут) 
	 */
	const HOUR = 3600;
	
	/**
	 * @const integer время в секундах 1/2 часа (30 минут) 
	 */
	const HALFHOUR = 1800;
	
	/**
	 * @const integer время в секундах 1 минута (60 секунд) 
	 */
	const MINUTE = 60;
	
	/**
	 * @const integer время в секундах 1/2 минуты (30 секунд) 
	 */
	const HALFMINUTE = 30;
	/**
	 * @const integer время 15 секунд 
	 */
	const SECONDS_15 = 15;
	/**
	 * @const integer время 10 секунд 
	 */
	const SECONDS_10 = 10;
	/**
	 * @const integer время 5 секунд 
	 */
	const SECONDS_5 = 5;
	
	/**
	 * @const integer время 3 секунды 
	 */
	const SECONDS_3 = 3;
	
	/**
	 * @const integer время в секундах 1 секунда
	 */
	const SECOND = 1;
	
	/**
	 * Get expression dependency
	 * @param string $expression php expression.
	 * @return \CExpressionDependency
	 */
	public static function getExpressionDependency($expression)
	{
		return new \CExpressionDependency($expression);
	}
}