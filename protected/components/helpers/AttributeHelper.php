<?php
/**
 * Attribute Helper
 * 
 * @version 1.01
 *
 * @history
 * 1.01
 *  - add q() method.
 */
class AttributeHelper extends CComponent
{
	/**
	 * Get attribute value.
	 * 
	 * @param array $attributes attributes array.
	 * @param string $name attribute name.
	 * @param string $default default value, if attribute not exists.
	 * @return Ambigous <string, unknown>
	 */
	public static function get($attributes, $name, $default=null)
	{
		return is_array($attributes) && array_key_exists($name, $attributes) ? $attributes[$name] : $default;
	}	
	
	/**
	 * Получение обязательного аттрибута
	 * 
	 * Если не найдено атрибута с переданным именем, 
	 * бросается исключение типа \AttributeHelperException
	 * 
	 * @param array $attributes attributes array.
	 * @param string $name attribute name.
	 * @return mixed attribute value.
	 */
	public static function getR($attributes, $name)
	{
		if(!isset($attributes[$name])) 
			throw new \AttributeHelperException("Attribute \"{$name}\" not found.");
		
		return $attributes[$name];
	}

	/**
	 * Заменяет в строке двойные кавычки на одинарные
	 * @param string $str входная строка
	 * @return string
	 */
	public static function q($str)
	{
		return str_replace('"', "'", $str);
	}
	
	/**
	 * Convert array to PHP code string.
	 * @param array $array array
	 * @return string PHP code of this array
	 */
	public static function toPHPString($array)
	{
		$data = array();
		foreach($array as $key=>$value) {
			if(is_array($value)) $value = self::toPHPString($value);
			elseif(is_string($value)) $value='\'' . \CHtml::encode($value) . '\'';
			elseif($value === true) $value='true';
			elseif($value === false) $value='false';
					
			$data[] = '\'' . \CHtml::encode($key) . '\'=>' . $value;
		}
	
		return 'array(' . implode(', ', $data) . ');';
	}
}

/**
 * Attribute helper exception class.
 *
 * @see \Exception
 * 
 */
class AttributeHelperException extends \Exception
{	
}