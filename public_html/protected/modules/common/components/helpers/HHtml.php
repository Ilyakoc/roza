<?php
namespace common\components\helpers;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;

class HHtml
{
	/**
	 * Get http://placehold.it image url.  
	 * @param array $options опции.
	 * Список опций:
	 * [
	 * 	"width" ширина
	 *  "w" короткий псевдоним для ширины
	 * 	"height" высота
	 *  "h" короткий псевдоним для высоты 
	 * 	"color" цвет текста
	 * 	"c" короткий псевдоним для цвета текста 
	 * 	"bgColor" цвет фона
	 *  "bg" короткий псевдоним для цвета фона 
	 * 	"text" текст надписи
	 *  "t" короткий псевдоним для текста надписи
	 * 	"textSize" размер надписи
	 *  "sz" короткий псевдоним для размера надписи
	 * 	"textTrack" межсимвольный пробел
	 *  "track" короткий псевдоним для межсимвольного пробела 
	 * ]
	 */
	public static function phSrc($options)
	{
		$params=[];
		$set=function($param, $name, $short, $default=null) use (&$params, $options) {
			if($value=A::get($options, $name, A::get($options, $short, $default))) {
				$params[]=$param.'='.$value;
			}
		};
		
		$set('w','width', 'w');
		$set('h','height', 'h');
		$set('txt','text', 't');
		$set('txtsize','textSize', 'sz');
		$set('txtcolor','color', 'c');
		$set('bg','bgColor', 'bg');
		$set('txttrack','textTrack', 'track', 1);
		
		return 'https://placeholdit.imgix.net/~text?'.implode('&', $params);
	}
	
	/**
	 * Получить тэг изображения для сервиса http://placehold.it
	 * @param array $options параметры для получения ссылки на 
	 * изображение с сервиса http://placehold.it
	 * @see HHtml::phSrc()
	 * @param string $alt the alternative text display. По умолчанию пустая строка.
	 * @param array $htmlOptions additional HTML attributes. По умолчанию пустой массив.
	 * @see \CHtml::image()
	 * @return string
	 */
	public static function phImg($options, $alt='', $htmlOptions=[])
	{
		return \CHtml::image(self::phSrc($options), $alt, $htmlOptions);
	}

	/**
	 * Для поддержки старых версий
	 * @see HHtml::phSrc()
	 */
	public static function pImage($options)
	{
		return self::phSrc($options);
	}

	/**
     * Ссылка на предыдущую страницу
	 * @param string $text текст ссылки
	 * @param string $defaultBackUrl ссылка возврата по умолчанию, 
	 * если переход был по прямой ссылке.
	 * @param string $path путь. возврат идет на урл, если referrer, содержит этот путь.
	 * @param array $htmlOptions link html options. Default empty array.
	 * @return string
	 */
	public static function linkBack($text='Back', $defaultBackUrl='/', $path=null, $htmlOptions=[]) 
	{ 
		$link = \Yii::app()->request->urlReferrer;

		if($path === null)
			$path=$defaultBackUrl;

		if(!preg_match('/^[^\/]+:\/\/'.\Yii::app()->request->serverName.($path?str_replace('/', '\/',$path):'').'(.*)$/i', $link)) {
			$link = $defaultBackUrl;
		} 
		
		return \CHtml::link($text, $link, $htmlOptions);
	}

	/**
	 * Получить анонс текста
	 * @param string $text основной текст.
	 * @param int $length длина анонса.
	 * @param string $detailLink ссылка подробнее
	 * @return string
	 */
	public static function intro($text, $length=300, $detailLink='...')
	{
		if(!is_numeric($length)) $length = 300;
		
		$subLimit = floor($length / 10);
		$text = strip_tags($text);
		if(mb_strlen($text) > $length) {
			$text = preg_replace('/ +/', ' ', mb_substr($text, 0, $length));
			$chunks = explode(' ', $text);
			$count  = count($chunks);
			$chunks = array_slice($chunks, 0, ($count <= $subLimit) ? ($count - 1) : $subLimit);
			$text = implode(' ', $chunks) . $detailLink;
		}
		
		return $text;
	}
	
	/**
	 * Для поддержки старых версий
	 * @see HHtml::intro()
	 */
	public static function getIntro($text, $length=300, $detailLink='...')
	{
		return self::intro($text, $length, $detailLink);
	}

	/**
	 * Вывод отформатированной цены
	 * @param sting $price цена.
	 * @return string
	 */
	public static function price($price)
	{
		if((int)$price < (float)$price) {
			$price=(float)$price;
			$decimal=2;
		}
		else {
			$price=(int)$price;
			$decimal=0;
		}
		return number_format($price, $decimal, '.', ' ');
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
	 * (non-PHPDoc)
	 * @see htmlspecialchars()
	 */
	public static function hsc($str)
	{
		return htmlspecialchars($str);
	}	

	/**
	 * Получить только цифровой номер телефона.
	 * @param string $phone
	 * @return string
	 */
	public static function callto($phone)
	{
		return preg_replace('/^[^0-9+]+$/', '', $phone);
	}

	/**
	 * Получить ссылку вызова номера
	 * @param string $phone
	 * @param array $htmlOptions
	 * @return string
	 */
	public static function linkCallto($phone, $htmlOptions=[])
	{
		return \CHtml::link($phone, 'callto:'.static::callto($phone), $htmlOptions);
	}
	
	/**
	 * Получить HTML код списка элементов
	 * @param array $data массив вида array(value=>text)
	 * @param array $htmlOptions дополнительные атрибуты для тэга.
	 * Дополнительные параметры:
	 * "tagContainer" - имя тэга контейнера, по умолчанию "ul";
	 * "tagItem" - имя тэга элемента, по умолчанию "li";
	 * "itemOptions" - дополнительные HTML атрибуты для тэга элемента;
	 * 	- "attributes" имена атрибутов, которые будут добавлены для элемента в дата-атрибут
	 * 	вида array(name=>attribute). Атрибут может задаватся в формате @attribute.
	 * "valueName" - имя атрибута значения, по умолчанию "data-id";
	 * "except" - значения, которые необходимо исключить из отображения;
	 * "exceptHide" - значения, которые необходимо исключить из отображения просто не отображать.
	 * "attribute" - атрибут из которого брать текст;
	 * @return string|false
	 */
	public static function tagList($data, $htmlOptions=[])
	{
		if(!is_array($data)) {
			return false;			
		}
		
		$options=static::prepareHtmlOptions($htmlOptions, [
			'tagContainer'=>'ul', 
			'tagItem'=>'li', 
			'itemOptions'=>[],
			'valueName'=>'data-id',
			'except'=>[],
			'exceptHide'=>false,
			'attribute'=>false
		]);
		$options['except']=A::toa($options['except']);
		$itemOptions=static::prepareHtmlOptions($options['itemOptions'], [
			'attributes'=>[]
		]);
		
		$html.=\CHtml::openTag($options['tagContainer'], $htmlOptions);
		foreach($data as $value=>$text) {
			$_itemOptions=$options['itemOptions'];
			if(in_array($value, $options['except'])) {
				if($options['exceptHide']) {
					$_itemOptions['style']=A::get($_itemOptions, 'style', '').';display:none';
				}
				else {
					continue;
				}
			}
			
			$itemData=$text;
			$text=static::getAttributeValue($text, $options['attribute'], $text);
			foreach($itemOptions['attributes'] as $itemAttributeName=>$itemAttribute) {
				$_itemOptions['data-'.$itemAttributeName]=static::getAttributeValue($itemData, $itemAttribute);
			}
			$_itemOptions[$options['valueName']]=$value;
			$html.=\CHtml::tag($options['tagItem'], $_itemOptions, $text);
		}
		$html.=\CHtml::closeTag($options['tagContainer']);
		
		return $html;		
	}
	
	/**
	 * Подготовить массив параметров DOM-элемента
	 * @param &array $htmlOptions массив параметров
	 * @param array|false $options массив специальных параметров, 
	 * которые могут содержатся в основном массиве вида array(param=>default).
	 * По умолчанию (false) - не задан.
	 * @return array выделенный массив специальных параметров вида array(param=>value) 
	 */
	public static function prepareHtmlOptions(&$htmlOptions, $options=false)
	{
		if(!is_array($options)) {
			return [];
		}
		
		$_options=[];
		foreach($options as $param=>$default) {
			$_options[$param]=A::get($htmlOptions, $param, $default);
			if(isset($htmlOptions[$param])) {
				unset($htmlOptions[$param]);
			}
		}
		
		return $_options;
	}
	
	/**
	 * Получить значение атрибута
	 * @param unknown_type $data
	 * @param unknown_type $attribute
	 * @param unknown_type $default
	 */
	public static function getAttributeValue($data, $attribute, $default=null)
	{
		if($attribute) {
			if(strpos($attribute, '@') === 0) {
				return A::rget($data, ltrim($attribute, '@'));
			}
			else {
				return $data->$attribute;	
			}
		}
		
		return $default;
	}
}
