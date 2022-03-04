<?php
/**
 * Hash helper
 * 
 * @version 1.0
 */

class HHash 
{
	/**
	 * Генерирует хэш-строку
	 * 
	 * @param string $data строка для хеширования. Если передано значение NULL, 
	 * строка генерится случайным образом. По умолчанию NULL.
	 * @param number $length длина возвращаемой строки.  
	 * @param string $algo алгоритм хеширования. 
	 * (например, "md5", "sha256", "haval160,4" и т.д.)
	 * По умолчанию "sha1".
	 * @see \hash()
	 *  	 
	 * @return string сгенерированный хэш.
	 */
	public static function get($data=null, $length=0, $algo='sha1')
	{
		if($data === null) $data = self::random();
		
		return $length ? substr($algo($data), 0, $length) : $algo($data);
	}

	/**
	 * Generate random hash string
	 * @param number $length длина возвращаемого хэша. Если значение 
	 * не передано или 0(нуль), возвращется полностью вся строка 
	 * сгенерированного хэша. Default is 0(zero).
	 * @param string $algo алгоритм используемый при хешировании. Default is sha1. 
	 */
	public static function generate($length=0, $algo='sha1')
	{
		return self::get(null, $length, $algo);
	}
	
	/**
	 * Generate random string
	 * @return number
	 */
	public static function random()
	{
		list($usec, $sec) = explode(' ', microtime());
		$seed = (float) $sec + ((float) $usec * 100000);
		mt_srand($seed);
		
		return mt_rand();
	}
	
	/**
	 * Generate security hash
	 * @param mixed $data входные данные
	 * @param string $securityKey секретный ключ
	 * @param number $cost @see \CPasswordHelper::hashPassword()
	 * @return string
	 */
	public static function security($data, $securityKey=null, $cost=13)
	{
		if($securityKey) 
			$securityKey = self::get($securityKey);
		
		return \CPasswordHelper::hashPassword(self::get($data) . $securityKey, $cost);
	}
	
	/**
	 * Verify security hash
	 * @param mixed $data входные данные
	 * @param string $hash хеш-строка
	 * @param string $securityKey секретный ключ
	 * @param number $cost @see \CPasswordHelper::hashPassword()
	 * @return boolean
	 */
	public static function verifySecurity($data, $hash, $securityKey=null, $cost=13)
	{
		return \CPasswordHelper::verifyPassword(self::security($data, $securityKey, $cost), $hash);
	}
	
	/**
	 * Generate hash. 
	 * @param string $str хешируемая строка
	 * @param string $algo алгоритм хеширования. По умолчанию md5. 
	 * (например, "md5", "sha256", "haval160,4" и т.д.)
	 * @return string
	 */
	public static function hash($str, $algo='md5')
	{
		return $algo($str);
	}
	
	/**
	 * Verify md5 hash.
	 * @param string $str source string
	 * @param string $hash hash
	 * @param string $algo алгоритм хеширования. По умолчанию md5. 
	 * (например, "md5", "sha256", "haval160,4" и т.д.)
	 * @return boolean
	 */
	public static function verifyHash($str, $hash, $algo='md5')
	{
		return ($hash === self::hash($str, $algo));
	}
	
	/**
	 * Алгоритм crc32
	 * @see HHash::get()
	 */
	public static function crc32($str=null)
	{
		return self::get($str, 0, 'crc32');
	}
	
	/**
	 * Алгоритм md5
	 * @see HHash::get()
	 */
	public static function md5($str=null)
	{
		return self::get($str, 0, 'md5');
	} 
	
	/**
	 * Получить хэш модели.
	 * @param mixed $model объект модели или имя класса модели.
	 * @return string 
	 */
	public static function hashModel($model)
	{
		return self::md5(\CHtml::modelName($model));
	}
	
	/**
	 * Получить имя параметра по хэшу модели.
	 * хэш(md5) имени параметра должен совпадать с $hash.
	 * Для генерации значения хэша можно использовать метод HHash::hashModel().
	 * @param string $hash хэш имени параметра.
	 * @param string $isPost данные переданы методом POST. По умолчанию TRUE.
	 * @return string|NULL имя параметра, либо NULL если параметр не найден.
	 */
	public static function nameModel($hash, $isPost=true)
	{
		$get=function($array) use ($hash) {
			foreach($array as $name=>$value) {
				if(self::md5($name) == $hash)
					return $name;
			}
			return null;
		};
		
		return $isPost ? $get($_POST) : $get($_REQUEST);
	}
	
	/**
	 * Обратимое шифрование методом "Двойного квадрата" (Reversible crypting of "Double square" method)
	 * @see http://habrahabr.ru/post/61309/
	 * 
	 * @param  String $input   Строка с исходным текстом
	 * @param  bool   $decrypt Флаг для дешифрования
	 * @return String          Строка с результатом Шифрования|Дешифрования
	 * @author runcore
	 */
	function dsCrypt($input,$decrypt=false) {
		$o = $s1 = $s2 = array(); // Arrays for: Output, Square1, Square2
		// формируем базовый массив с набором символов
		$basea = array('?','(','@',';','$','#',"]","&",'*');  // base symbol set
		$basea = array_merge($basea, range('a','z'), range('A','Z'), range(0,9) );
		$basea = array_merge($basea, array('!',')','_','+','|','%','/','[','.',' ') );
		$dimension=9; // of squares
		for($i=0;$i<$dimension;$i++) { // create Squares
			for($j=0;$j<$dimension;$j++) {
				$s1[$i][$j] = $basea[$i*$dimension+$j];
				$s2[$i][$j] = str_rot13($basea[($dimension*$dimension-1) - ($i*$dimension+$j)]);
			}
		}
		unset($basea);
		$m = floor(strlen($input)/2)*2; // !strlen%2
		$symbl = $m==strlen($input) ? '':$input[strlen($input)-1]; // last symbol (unpaired)
		$al = array();
		// crypt/uncrypt pairs of symbols
		for ($ii=0; $ii<$m; $ii+=2) {
			$symb1 = $symbn1 = strval($input[$ii]);
			$symb2 = $symbn2 = strval($input[$ii+1]);
			$a1 = $a2 = array();
			for($i=0;$i<$dimension;$i++) { // search symbols in Squares
				for($j=0;$j<$dimension;$j++) {
					if ($decrypt) {
						if ($symb1===strval($s2[$i][$j]) ) $a1=array($i,$j);
						if ($symb2===strval($s1[$i][$j]) ) $a2=array($i,$j);
						if (!empty($symbl) && $symbl===strval($s2[$i][$j])) $al=array($i,$j);
					}
					else {
						if ($symb1===strval($s1[$i][$j]) ) $a1=array($i,$j);
						if ($symb2===strval($s2[$i][$j]) ) $a2=array($i,$j);
						if (!empty($symbl) && $symbl===strval($s1[$i][$j])) $al=array($i,$j);
					}
				}
			}
			if (sizeof($a1) && sizeof($a2)) {
				$symbn1 = $decrypt ? $s1[$a1[0]][$a2[1]] : $s2[$a1[0]][$a2[1]];
				$symbn2 = $decrypt ? $s2[$a2[0]][$a1[1]] : $s1[$a2[0]][$a1[1]];
			}
			$o[] = $symbn1.$symbn2;
		}
		if (!empty($symbl) && sizeof($al)) // last symbol
			$o[] = $decrypt ? $s1[$al[1]][$al[0]] : $s2[$al[1]][$al[0]];
		
		return implode('',$o);
	}

	/**
	 * Unsigned crc32
	 */
	public static function ucrc32($str)
	{
		$crc32=crc32($str);

		if($crc32 < 0) $crc32*=-1;

		return $crc32;
	}
}