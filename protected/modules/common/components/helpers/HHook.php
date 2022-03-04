<?php
/**
 * Помощник для хуков
 *
 */
namespace common\components\helpers;

use common\components\helpers\HArray as A;

class HHook
{
	public static function get($handlers, $name, $default=null)
	{
		$handler=A::get($handlers, $name);
		
		return is_callable($handler) 
			? $handler 
			: (is_callable($default) ? $default : function() { return true; });
	}
	
	/**
	 * 
	 * @param array $handlers массив обработчиков, либо обработчик, который необходимо запустить.
	 * [name=>callable] 
	 * @param string $name имя обработчика.
	 * @param array $params параметры для обработчика. [arg1, arg2] будут переданы, как function(arg1, arg2).
	 * По умолчанию пустой массив.
	 * @param null|callable $default обработчик по умолчанию, если не найден вызываемый. По умолчанию NULL.
	 * @return mixed|NULL
	 */
	public static function exec($handlers, $name, $params=[], $default=null)
	{
		if($handler=self::get($handlers, $name, $default)) {
			return self::hexec($handler, $params);
		}
		
		return null;
	}
	
	public static function hexec($handler, $params=[], $default=null)
	{
		if(is_callable($handler)) {
			return call_user_func_array($handler, $params);
		}
		
		return $default;
	}
}