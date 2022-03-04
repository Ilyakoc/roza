<?php
/**
 * Request Helper
 */
namespace common\components\helpers;

use common\components\helpers\HYii as Y;

class HRequest
{
	/**
	 * @see \Yii::app()->request
	 * @return \CHttpRequest
	 */
	public static function request()
	{
		return \Yii::app()->request;
	}
	
	/**
	 * Псевдоним для HRequest::request()
	 * @return \CHttpRequest
	 */
	public static function r()
	{
		return \Yii::app()->request;
	}
	
	/**
	 * Получить текущий абсолютную ссылку
	 * @param boolean|array $params переменные запроса.
	 * Может быть передно (FALSE) - без переменных.
	 * По умолчанию (TRUE) - текущие переменные из QUERY_STRING.
	 */
	public static function absoluteUrl($params=true)
	{
		$url=static::request()->getHostInfo() . '/' . static::request()->getPathInfo();
		
		if($params === true) $url.='?'.$_SERVER['QUERY_STRING'];
		elseif(is_array($params) && !empty($params)) {
			if($qs=static::qs($params)) $url.='?'.$qs;
		}
		
		return $url;
	}
	
	/**
	 * Преобразование массива параметров в строку запроса.
	 * @param array $params массив параметров.
	 * Если значение параметра равно TRUE, будет взято значение 
	 * соответствующего параметра из GET.
	 * @return string
	 */
	public static function qs($params)
	{
		$qs='';
		if(is_array($params)) {
			foreach($params as $n=>$v) {
				if($v === true) {
					$v=static::request()->getParam($n, null);
					if($v === null) continue; 
				}
				$qs.=$n.'='.$v.'&';
			}
		}
		return rtrim($qs, '&');
	}
	
	/**
	 * Получить значение параметра запроса
	 * @param string $name имя параметра
	 * @param string $default значение по умолчанию
	 * @param string $onlyPost получать только данные из POST.
	 * По умолчанию (FALSE) - впервую очередь из GET, затем из POST.
	 * @return mixed
	 */
	public static function requestGet($name, $default=null, $onlyPost=false)
	{
		$r=static::request();
		if($onlyPost) {
			return $r->getPost($name, $default);
		}
		return $r->getQuery($name, $r->getPost($name, $default));
	}
	
    /**
	 * Псевдоним для метода requestGet()
	 * @see HRequest::requestGet()
	 * @return mixed
	 */
	public static function get($name, $default=null, $onlyPost=false)
	{
		return static::requestGet($name, $default, $onlyPost);
	}
    
    /**
	 * Получение переменной из $_POST
	 * @see HRequest::requestGet()
	 * @return mixed
	 */
	public static function post($name, $default=null)
	{
		return static::requestGet($name, $default, true);
	}
    
	/**
	 * Псевдоним для метода requestGet()
	 * @see HRequest::requestGet()
	 * @return mixed
	 */
	public static function rget($name, $default=null, $onlyPost=false)
	{
		return static::requestGet($name, $default, $onlyPost);
	}
	
	/**
	 * Alias for \Yii::app()->request->isAjaxRequest
	 * @return boolean
	 */
	public static function isAjaxRequest()
	{
		return static::request()->isAjaxRequest;
	}
	
	/**
	 * * @see \CHttpRequest::redirect()
	 */
	public static function redirect($url, $terminate=true, $statusCode=302)
	{
		static::request()->redirect($url, $terminate, $statusCode);
	}
	
	/**
	 * Send http response code 404.
	 * @param boolean $terminate завершить приложение. 
	 * По умолчанию (FALSE) не завершать.
	 */
	public static function r404($terminate=false)
	{
		http_response_code(404);
		if($terminate) Y::end();
	}
	
	/**
	 * Бросить исключение \CHttpException(404)
	 * @throws \CHttpException
	 */
	public static function e404()
	{
		throw new \CHttpException(404);
	}
	
	/**
	 * Бросить исключение \CHttpException(400)
	 * @throws \CHttpException
	 */
	public static function e400()
	{
		throw new \CHttpException(400);
	}
	
	/**
	 * Послать заголовок модификации страницы. 
	 * @link http://last-modified.com/ru/last-modified-if-modified-since-php.html
	 * @param integer|string $time время последней модификации страницы в Unix-формате.
	 * Может быть передано строкой даты TIMESTAMP. 
	 */
	public static function sendLastModified($time)
	{
		if(is_numeric($time)) {
			$LastModified_unix = $time;			
		}
		else {
			$time=new \DateTime($time);
			$LastModified_unix=(int)$time->format('U');
		}
		$LastModified = gmdate("D, d M Y H:i:s \G\M\T", $LastModified_unix);
		$IfModifiedSince = false;
		if (isset($_ENV['HTTP_IF_MODIFIED_SINCE']))
			$IfModifiedSince = strtotime(substr($_ENV['HTTP_IF_MODIFIED_SINCE'], 5));
		if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']))
			$IfModifiedSince = strtotime(substr($_SERVER['HTTP_IF_MODIFIED_SINCE'], 5));
		if ($IfModifiedSince && $IfModifiedSince >= $LastModified_unix) {
			header($_SERVER['SERVER_PROTOCOL'] . ' 304 Not Modified');
			exit;
		}
		header('Last-Modified: '. $LastModified);
	}

	/**
	 * Expression: проверка строгого соотвествия переменных в строке запроса.
	 * @param array|string $names проверяемые переменные. Может быть передано
	 * (string) - только имя одной переменной.
	 * @param boolean $empty строка переменных запроса может быть пустой.
	 * По умолчанию (TRUE) может быть пустой.
	 * @param boolean $invert инвертировать результат. 
	 * По умолчанию (FALSE) не инвертировать.
	 * @return boolean
	 */
	public static function exprGET($names, $empty=true, $invert=false)
	{
		if($empty && ($_SERVER['QUERY_STRING'] === '')) return !$invert;
			
		if(!is_array($names)) $names=[$names];
	
		$result=(substr_count($_SERVER['QUERY_STRING'], '=') === count($names));
		foreach($names as $name) $result=($result && isset($_GET[$name]));
	
		return $invert ? !$result : $result;
	}
}
