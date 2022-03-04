<?php
/**
 * Array Helper
 * 
 */
namespace common\components\helpers;

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
		return self::rcallback($array, $key, function($item, $key) use ($default) {
			return self::get($item, $key, $default);
		}, $default, $delimiter);
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
	public static function getR($array, $key)
	{
		
		if(!static::existsKey($array, $key)) 
			throw new HArrayException("Array key \"{$key}\" not found");
		
		return $array[$key];
	}
	
	/**
	 * Установка значения элементу массива
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
			if(is_array($array[$key])) { 
				if(is_array($value)) {
					if($direction < 0) $array[$key]=self::m($value, $array[$key]);
					else $array[$key]=self::m($array[$key], $value);
				}
				else {
					if($direction < 0) array_unshift($array[$key], $value);
					else array_push($array[$key], $value);
				}
			}
			else {
				if(is_array($value)) {
					if($direction < 0) array_unshift($value, $array[$key]);
					else array_push($value, $array[$key]);					
				}
				else {
					$value = ($direction < 0) ? ($value . $array[$key]) : ($array[$key] . $value); 
				}
				$array[$key]=$value;
			}
		}
		else {
			$array[$key] = $value;
		}
	}
	
	/**
	 * Установка значения элементу массива находящегося в глубине.
	 * @param array $array массив
	 * @param string $key ключ или ключи массива от ключа верхнего уровня до ключа нижнего уровня.
	 * (string) ключи должны передаваться с разделителем указанным в параметре $delimiter.
	 * (array) массив ключей от ключа верхнего уровня до ключа нижнего уровня.
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
	 * @param string $delimiter разделитель для параметра $key. По умолчанию "."(точка).
	 * @param boolean $force принудительно создавать элементы для несуществующих ключей массива.
	 * По умолчанию (FALSE) - не создавать.
	 */
	public static function rset(&$array, $key, $value, $merge=false, $direction=1, $delimiter='.', $force=false)
	{
		self::rcallback($array, $key, function(&$item, $key) use ($value, $merge, $direction) {
			self::set($item, $key, $value, $merge, $direction);
		}, null, $delimiter, $force);
	}
	
	/**
	 * Удалить элемент массива в глубине.
	  * @param array $array массив
	 * @param string $key ключ или ключи массива от ключа верхнего уровня до ключа нижнего уровня.
	 * (string) ключи должны передаваться с разделителем указанным в параметре $delimiter.
	 * (array) массив ключей от ключа верхнего уровня до ключа нижнего уровня.
	 * @param string $delimiter разделитель для параметра $key. По умолчанию "."(точка).
	 */
	public static function runset(&$array, $key, $delimiter='.')
	{
		self::rcallback($array, $key, function(&$item, $key) {
			unset($item[$key]);
		}, null, $delimiter);
	}
	
	/**
	 * Применить callback функцию к элементу массива в глубине.
	 * @param array $array массив
	 * @param string $key ключ или ключи массива от ключа верхнего уровня до ключа нижнего уровня.
	 * (string) ключи должны передаваться с разделителем указанным в параметре $delimiter.
	 * (array) массив ключей от ключа верхнего уровня до ключа нижнего уровня.
	 * @param callable $callback callable функция, которая будет вызвана для найденного элемента.
	 * Параметры функции function(&$item, $key)
	 * @param string $default значение, возвращаемое по умолчанию, если элемент не найден. 
	 * По умолчанию NULL.
	 * @param string $delimiter разделитель для параметра $key. По умолчанию "."(точка).
	 * @param boolean $force принудительно создавать элементы для несуществующих ключей массива.
	 * По умолчанию (FALSE) - не создавать.
	 */
	public static function rcallback(&$array, $key, $callback, $default=null, $delimiter='.', $force=false)
	{
		if(is_string($key)) $key = explode($delimiter, $key);
		
		$akey=array_shift($key);
		if(!self::existsKey($array, $akey)) {
			if($force) $array[$akey]=[];
			else return $default;
		}
		
		if(count($key) > 0) {
			return self::rcallback($array[$akey], $key, $callback, $default, $delimiter, $force);
		}
		else {
			return call_user_func_array($callback, [&$array, $akey]);
		}
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
	 * Новый метод преобразования значения в массив.
	 * @param mixed $value значение.
	 * @param boolean $empty при пустом значении возвращать пустой массив.
	 * По умолчанию (TRUE) возвращать пустой массив.
	 * @param array $params дополнительные параметры для 
	 * метода преобразования $method вида array(var1, var2).
	 * @param string $method имя метода преобразования для объекта.
	 * По умолчанию "__toArray".
	 * @return array
	 */
	public static function toa($value, $empty=true, $params=[], $method='__toArray')
	{
		if($empty && !$value) {
			return [];
		}
		
		if(is_array($value)) {
			return $value;
		}
		elseif(is_object($value) && method_exists($value, $method)) {
			return call_user_func_array([$value, $method], $params);
		}
		
		return [$value]; 
	}
	
	/**
	 * Convert array to PHP code string.
	 * @param array $array array
	 * @param boolean $php54 формат версии PHP5.4. По умолчанию - TRUE.
	 * Если будет передано FALSE, то формат будет таким: "array(...)".
	 * @param boolean $special учитывать специальные метки значений для тип STRING.
	 * По умолчанию (FALSE) - не учитывать.
	 * Специальные метки:
	 * "php:$value", будет преобразовано в: $value 
	 * @return string PHP code of this array
	 */
	public static function toPHPString($array, $php54=true, $special=false)
	{
		$data = array();
		foreach($array as $key=>$value) {
			if(is_array($value)) $value = self::toPHPString($value, $php54);
			elseif(is_string($value)) {
				if(substr($value,0,4) == 'php:') $value=substr($value,4);
				else $value='\'' . \CHtml::encode($value) . '\'';
			}
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
	public static function m()
	{
		return call_user_func_array('\CMap::mergeArray', func_get_args());
	}
	
	/**
	 * Сортировка массива
	 * Сортировка массива по указанному порядку в массиве ключей
	 * Не возвращаются все ключи не входящие в $orderedKeys
	 * @param array $array сортируемый массив
	 * @param array $keys массив упорядоченных ключей для сортировки и фильтрации.
	 * Значения с ключами, которых нет в $keys, не попадут в результирующий массив,
	 * если не задан параметр $onlySort=TRUE.
	 * @param boolean $onlySort только сортировать. Не применять фильтрацию.
	 * По умолчанию (FALSE) - применять фильтрацию.
	 * @param boolean $reverse обратный режим. При режиме только сортировки ($onlySort=TRUE) 
	 * сперва обрабатывать элементы массива, затем массив ключей.
	 * По умолчанию (FALSE) - в первую очередь обрабатывать массив ключей.
	 * @return array отсортированный и отфильтрованный массив.
	 */
	public static function sort($array, $keys=[], $onlySort=false, $reverse=false)
	{
		$result = [];
		
		if($onlySort) {
			$source=array_diff_key($array, array_flip($keys));
			$fSortFill=function($key) use (&$result, &$source) {
				while((list($k,$v)=each($source)) && ($k !== $key)) {
					$result[$k]=$v;
					unset($source[$k]);
				}				
			};
		}
		
		foreach($keys as $key) {
			if($onlySort && $reverse) $fSortFill($key);			
			if(isset($array[$key])) {
				$result[$key] = $array[$key];
				continue;
			}			
			if($onlySort) $fSortFill($key);
		}		
		if($onlySort) $fSortFill(null);	
		
		return $result;
	}
	
	/**
	 * Проверка существования ключа в массиве
	 * @param array $array массив
	 * @param string $key ключ
	 * @return boolean
	 */
	public static function existsKey($array, $key)
	{
		if($array instanceof \CAttributeCollection) {
			return $array->offsetExists($key);
		}
		return (is_array($array) && array_key_exists($key, $array));
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
			: self::existsKey($array, $key);
	}
	
	/**
	 * Рекурсивный обход массива с применением callback-функции.
	 * Отличие от array_walk_recursive() в том, что callback-функция 
	 * применяется ко всем элементам, а не только к конченым. 
	 * @see array_walk_recursive()
	 * @param array &$array массив
	 * @param callback $callback callback функция. 
	 * Параметры function(&$item, &$key) 
	 * Полный список параметров function(&$item, &$key, &$userdata, $level) 
	 * @param mixed $userdata дополнительные данные
	 * @param integer $level уровень вложенности. По умолчанию 0(нуль).
	 */
	public static function rwalk(&$array, $callback, $userdata=null, $level=0)
	{
		foreach($array as $key=>$item) {
			call_user_func_array($callback, [&$item, &$key, &$userdata, $level]);
			if(is_array($item)) self::rwalk($item, $callback, $userdata, $level+1);
		}
	}
}

/**
 * Array helper exception class.
 *
 */
class HArrayException extends \CException
{	
}