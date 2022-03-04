<?php
/**
 * Array Helper
 * 
 */
class HArray
{
	/**
	 * @var null значение NULL для методов с возвратом значений по ссылке
	 */
	public static $null=null;
	
	/**
	 * Получить значение элемента массива.
	 * 
	 * @param array $array массив.
	 * @param mixed $key ключ.
	 * @param mixed $default значение по умочанию, если элемент не существует.
	 * @return mixed
	 */
	public static function get($array, $key, $default=null)
	{
		if($array instanceof \CAttributeCollection) {
			if($array->offsetExists($key)) return $array[$key];
			return $default;
		}
		return is_array($array) && array_key_exists($key, $array) ? $array[$key] : $default;
	}
	
	/**
	 * Получение значения элемента массива в глубь (рекурсивно)
	 * @param array $array массив
	 * @param mixed $key ключ или ключи массива от ключа верхнего уровня до ключа нижнего уровня.
	 * (string) ключи должны передаваться с разделителем указанным в параметре $delimiter.
	 * (array) массив ключей от ключа верхнего уровня до ключа нижнего уровня.
	 * @param string $default значение по умолчанию. По умолчанию NULL.
	 * @param string $delimiter разделитель для параметра $key. По умолчанию "."(точка).
	 * @return mixed если значение не найдено, возвратит NULL.
	 */
	public static function rget($array, $key, $default=null, $delimiter='.')
	{
		if(!is_string($key) && !is_array($key)) return null;
		
		if(is_string($key)) $key = explode($delimiter, $key);
		
		return (count($key) > 1) 
			? self::rget(self::get($array, array_shift($key)), $key, $default) 
			: self::get($array, @array_shift($key), $default);
	}
	
	/**
	 * Получение обязательного значения из массива
	 * 
	 * Если не найдено значение для переданного ключа, 
	 * бросается исключение типа \HArrayException
	 * 
	 * @param array $array array.
	 * @param mixed $key key.
	 * @return mixed attribute value.
	 */
	public static function getR($attributes, $name)
	{
		if(!isset($array[$key])) 
			throw new HArrayException("Array key \"{$key}\" not found");
		
		return $array[$key];
	}
	
	/**
	 * Установка значения массиву
	 * @param array $array массив
	 * @param string $key ключ массива
	 * @param mixed $value значение
	 * @param string $merge флаг, сливать ли имеющееся значение с передаваемым?
	 * На данный момент, слияние разделяется на два типа, для массивов и всего остального.
	 * По умолчанию FALSE. 
	 * @param number $direction направление слияния. По умолчанию 1(единица).
	 * для ($direction >= 0):
	 * array: \CMap::mergeArray($array[$key], $value)
	 * !array: $array[key] . $value
	 * для ($direction < 0):
	 * array: \CMap::mergeArray($value, $array[$key])
	 * !array: $value . $array[key] 
	 */
	public static function set(&$array, $key, $value, $merge=false, $direction=1)
	{
		if($merge && isset($array[$key])) {
			if(is_array($value)) 
				$value = ($direction < 0) ? \CMap::mergeArray($value, $array[$key]) : \CMap::mergeArray($array[$key], $value);
			else 
				$value = ($direction < 0) ? ($value . $array[$key]) : ($array[$key] . $value); 
		}
		
		$array[$key] = $value;
	}
	
	/**
	 * Удаляет из массива элемент с переданным ключом.
	 * @param array $array array
	 * @param mixed $key key
	 */
	public static function delete(&$array, $key)
	{
		if(isset($array[$key])) { 
			unset($array[$key]);
		}
	}
	
	/**
	 * Преобразовать в массив.
	 * @param mixed $data данные.
	 * @param array $params дополнительные параметры array(param=>value).
	 * Для объектов производится попытка вызвать метод __toArray().
	 * @return array
	 */
	public static function toArray($data, $params=array())
	{
		if(is_array($data)) 
			return $data;
		elseif(is_object($data) && method_exists($data, '__toArray')) 
			return $data->__toArray($params);
		elseif(!is_object($data)) 
			return array($data);
		
		return array();
	}
	
	/**
	 * Convert array to PHP code string.
	 * @param array $array array
	 * @param boolean $php54 формат версии PHP5.4. По умолчанию - TRUE.
	 * Если будет передано FALSE, то формат будет таким: "array(...)".
	 * @return string PHP code of this array
	 */
	public static function toPHPString($array, $php54=true)
	{
		$data = array();
		foreach($array as $key=>$value) {
			if(is_array($value)) $value = self::toPHPString($value, $php54);
			elseif(is_string($value)) $value='\'' . \CHtml::encode($value) . '\'';
			elseif($value === true) $value='true';
			elseif($value === false) $value='false';
					
			$data[] = '\'' . \CHtml::encode($key) . '\'=>' . $value;
		}
	
		return ($php54?'[':'array(') . implode(', ', $data) . ($php54?']':')');
	}

	/**
	 * Объединить строки значения массива и переданной строки.
	 * @param &array $array массив
	 * @param mixed $key ключ или ключи массива от ключа верхнего уровня до ключа нижнего уровня.
	 * (string) ключи должны передаваться с разделителем указанным в параметре $delimiter.
	 * (array) массив ключей от ключа верхнего уровня до ключа нижнего уровня.
	 * @param string $str строка для объединения.
	 * @param boolean $before добавить строку в начало значения массива. По умолчанию FALSE (добавить в конец).
	 * @param string $delimiter разделитель для параметра $key. По умолчанию "."(точка).
	 * @return результат объединения.
	 */
	public static function concat($array, $key, $str, $before=false, $delimiter='.')
	{
		$value=self::rget($array, $key, '', $delimiter);
		
		return $before ? ($str.$value) : ($value.$str);
	} 
	
	/**
	 * Псевдоним для \CMap::mergeArray()
	 * @see \CMap::mergeArray()
	 */
	public static function m($a, $b)
	{
		return \CMap::mergeArray($a, $b);
	}
	
	/**
	 * Сортировка массива
	 * Сортировка массива по указанному порядку в массиве ключей
	 * Не возвращаются все ключи не входящие в $orderedKeys
	 * @param array $array сортируемый массив
	 * @param array $orderedKeys массив упорядоченных ключей для сортировки
	 * @return array отсортированный и отфильтрованный массив.
	 */
	public static function sort($array, $orderedKeys=array())
	{
		$result = array();
		foreach($orderedKeys as $key) {
			if(isset($array[$key])) $result[$key] = $array[$key];
		}
		return $result;
	}

	/**
	 * Проверка существования ключа.
	 * @param mixed $key ключ или ключи массива от ключа верхнего уровня до ключа нижнего уровня.
	 * (string) ключи должны передаваться с разделителем указанным в параметре $delimiter.
	 * (array) массив ключей от ключа верхнего уровня до ключа нижнего уровня.
	 * @param string $delimiter разделитель для параметра $key. По умолчанию "."(точка).
	 * @param array массив
	 */
	public static function exists($key, $array, $delimiter='.')
	{
		if(!is_array($array) || (!is_string($key) && !is_array($key))) {
			return false;
		}
		
		if(is_string($key) && (strpos($key, $delimiter) !== false)) {
			$key = explode($delimiter, $key);
		}
		
		return is_array($key) 
			? self::exists($key, self::get($array, array_shift($key))) 
			: array_key_exists($key, $array);		if(is_string($key)) $key = explode($delimiter, $key);
	}
}

/**
 * Array helper exception class.
 *
 */
class HArrayException extends \CException
{	
}