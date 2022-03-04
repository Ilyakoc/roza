<?php
/**
 * SEO helper class
 */
namespace seo\components\helpers;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HTools;
use common\components\helpers\HRequest;

class HSeo
{
	/**
	 * @var string|NULL заголовок H1. 
	 */
	public static $h1=null;
	
	/**
	 * @var string|NULL заголовок браузера. 
	 */
	public static $title=null;
	
	/**
	 * @var string|NULL ключевые слова. 
	 */
	public static $keywords=null;
	
	/**
	 * @var string|NULL мета-описание. 
	 */
	public static $desc=null;
	
	/**
	 * Проверяет, установлены ли основные переменные тэгов.
	 * HSeo::$h1, HSeo::$title, HSeo::$keywords, HSeo::$desc.  
	 */
	public static function has()
	{
		return !((self::$h1 === null) && (self::$title === null) && (self::$keywords === null) && (self::$desc === null));
	}
	
	/**
	 * Установка основных значений SEO. 
	 * H1, TITLE, KEYWORDS, DESCRIPTION.
	 * @param \CActiveRecord|array $model объект с поведением
	 * \seo\behaviors\SeoBehavior, или массив
	 * вида array('h1'=>h1, 'title'=>title, 'keywords'=>keywords, 'desc'=>desc)
	 */
	public static function seo($model)
	{
		if(is_array($model)) {
			self::$h1=A::get($model, 'h1');
			self::$title=A::get($model, 'title');
			self::$keywords=A::get($model, 'keywords');
			self::$desc=A::get($model, 'desc');
		}
		else {
			try {
				self::$h1=$model->getSeoH1();
				self::$title=$model->getSeoMetaTitle();
				self::$keywords=$model->getSeoMetaKeywords();
				self::$desc=$model->getSeoMetaDesc();
			}
			catch(\Exception $e) {}
		}
	}
	
	/**
	 * Публикация мета-тэгов.
	 * @param array $options массив дополнительных параметров.
	 * Параметры: array(
	 * 	title=>true, 
	 *  keywords=>true, 
	 *  desc=>true, 
	 *  robots=>true, 
	 *  charset=>true, 
	 *  canonical=>false, 
	 *  noskype=>true
	 * )
	 * Для отключения одного или нескольких параметров необходимо передать 
	 * напр., array(noskype=>false, robots=>false).
	 */
	public static function publish($options=[])
	{
		if(A::get($options, 'title', true)) echo self::title();
		if(A::get($options, 'keywords', true)) self::keywords();
		if(A::get($options, 'desc', true)) self::desc();
		if(A::get($options, 'robots', true)) self::robots();
		if(A::get($options, 'charset', true)) self::charset();
		if(A::get($options, 'canonical', false)) self::canonical();
		if(A::get($options, 'noskype', true)) self::noskype();
	}
	
	/**
	 * Регистрация тэга H1.
	 * @param array $htmlOptions дополнительные опции. По умолчанию пустой массив.
	 * @return string
	 */
	public static function h1($htmlOptions=[])
	{
		return \CHtml::tag('h1', $htmlOptions, self::$h1);
	}
	
	/** 
	 * Регистрация заголовка браузера <TITLE>.
	 * @param string|NULL $title заголовок браузера. 
	 * По умолчанию (NULL) будет взят HSeo::$title.
	 * @return string
	 */
	public static function title($title=null)
	{
		if($title === null) $title=self::$title;
		
		return \CHtml::tag('title', [], $title);
	}
	
	/** 
	 * Регистрация мета тэга ключевых слов <meta name="keywords">.
	 * @param string|NULL $keywords ключевые слова.
	 * По умолчанию (NULL) будет взят HSeo::$keywords.
	 * @param array $options дополнительные параметры array(name=>value).
	 * @return void
	 */
	public static function keywords($keywords=null, $options=[])
	{
		if($keywords === null) $keywords=self::$keywords;
		
		self::setOptionsLang($options);
		
		Y::cs()->registerMetaTag($keywords, 'keywords', null, $options);
	}
	
	/** 
	 * Регистрация мета тэга описания <meta name="description">.
	 * @param string|NULL $desc описание.
	 * По умолчанию (NULL) будет взят HSeo::$desc.
	 * @param array $options дополнительные параметры array(name=>value).
	 * @return void
	 */
	public static function desc($description=null)
	{
		if($description === null) $description=self::$desc;
		
		self::setOptionsLang($options);
		
		Y::cs()->registerMetaTag($description, 'description', null, $options);
	}
	
	/** 
	 * Регистрация мета тэга robots <meta name="robots">.
	 * @param string $content содержимое атрибута "content". По умолчанию "index, follow".
	 * @return void
	 */
	public static function robots($content='index, follow')
	{
		Y::cs()->registerMetaTag('index, follow', 'robots');
	}
	
	/**
	 * Регистрация мета тэга "charset"
	 * @param string $charset кодировка страницы. По умолчанию "utf-8".
	 */
	public static function charset($charset='utf-8')
	{
		Y::cs()->registerMetaTag(null, null, null, ['charset'=>$charset]);
	}
	
	/**
	 * Регистрация тэга "canonical"
	 * @param callable|boolean|array $register функция, которая возвращает результат (boolean) 
	 * регистрировать тэг "canonical" или нет. 
	 * Может быть передано сразу BOOLEAN значение. 
	 * Может быть передан массив из callable|boolean элементов. В таком случае будет использован 
	 * параметр $operator.
	 * Может быть передан сокращенное выражение array(["e:GET"=>params]) для метода HSeo::exprGET()
	 * По умолчанию (TRUE) - регистрировать.
	 * @param array $params массив параметров, которые добавлять в строку запроса. 
	 * По умолчанию пустой массив.
	 * @param string|array $operator оператор объединения выражений. По умолчанию "|" (или).
	 * Может также принимать "&" (и). Если оператор не определен, будет использован "|" (или).
	 * Если в $register передан массив, то в $operator также может быть передан массив, с 
	 * требуемыми операторами на соответсвующих позициях. 
	 * @param string $defaultOperator оператор объединения выражений по умолчанию. Используется, 
	 * если в $register и $operator переданы массивы, но кол-во элементов в $operator меньше, чем
	 * в $register. По умолчанию "|" (или). Может также принимать "&" (и). 
	 * Если оператор не определен, будет использован "|" (или). 
	 */
	public static function canonical($register=true, $params=[], $operator='|', $defaultOperator='|')
	{
		self::prepareExpression($register);
		if(HTools::evaluate($register, $operator, $defaultOperator)) {
			Y::cs()->registerLinkTag('canonical', null, HRequest::absoluteUrl($params));
		}
	}
	
	/**
	 * Регистрация тэга "canonical" с указанной ссылкой. 
	 * @param string $route маршрут.
	 * @param array $params параметры маршрута. По умолчанию пустой массив.
	 */
	public static function canonicalByUrl($route, $params=[])
	{
		Y::cs()->registerLinkTag('canonical', null, Y::createUrl($route, $params));
	}
	
	/**
	 * Регистрация тэга "canonical" по параметрам исключения GET.
	 * @param array|mixed $params массив параметров исключения вида 
	 * array(name1, name2=>mixed, name3=>false). Если передано 
	 * name=>false, данный параметр не будет добавлен в каноническую ссылку.
	 * Если передано name=>true, данный параметр будет добавлен в каноническую ссылку
	 * с текущим значением из запроса.
	 * При передаче значения параметров, не являющимся массивом, будет 
	 * зарегистрирован тэг "canonical" текущией ссылки без параметров.
	 * По умолчанию пустой массив. 
	 * @param array $except массив исключений для значений параметров вида
	 * array(name=>[value1, value2], name2=>value3). Если значение параметра 
	 * будет одним из значений иключений, то тэг "canonical" будет опубликован
	 * без добавления данного параметра к ссылке.   
	 */
	public static function canonicalByGET($params=[], $except=[])
	{
		$register=[];
		
		if(is_array($params)) {
			foreach($params as $name=>$value) {
				if($value === false) unset($params[$name]);
				elseif(A::existsKey($except, $name)) {
					$excepts=A::get($except, $name, []);
					if(!is_array($excepts)) $excepts=[$excepts];
					if($value === true) $exceptValue=HRequest::request()->getQuery($name);
					else $exceptValue=$value;
					if(in_array($exceptValue, $excepts)) unset($params[$name]);
				}
				if(!is_bool($value)) $name=$value;
				$register[]=['e:GET'=>[$name,1,1]];
			}
			if(empty($register)) $register=true;
			self::canonical($register, $params);
		}
		else {
			self::canonical();
		}
	}
	
	/**
	 * Регистрация тэга запрета Skype Toolbar.
	 */
	public static function noskype()
	{
		Y::cs()->registerMetaTag('SKYPE_TOOLBAR_PARSER_COMPATIBLE', 'SKYPE_TOOLBAR');
	}
	
	/**
	 * Предварительная обработка массива выражений
	 * Поддерживает: 
	 * сокращенное выражение array(["e:GET"=>params]) для метода 
	 * \common\components\helpers\HRequest::exprGET()
	 * @param array|mixed &$expressions обрабатываются только массивы. 
	 */
	public static function prepareExpression(&$expressions)
	{
		if(is_array($expressions)) {
			foreach($expressions as $key=>$expr) {
				if(is_array($expr)) {
					$k=key($expr);
				 	if($k === 'e:GET') {
						$expressions[$key]=[
							'\common\components\helpers\HRequest::exprGET', 
							'params'=>is_array($expr[$k]) ? $expr[$k] : [$expr[$k]]
						];
				 	}
				}
			}
		}
	}
	
	/**
	 * Регистрация мета-тэгов постраничной навигации по рекомендации Google.
	 * @link https://support.google.com/webmasters/answer/1663744?hl=ru
	 * @param integer|\CDataProvider $pageCount общее кол-во страниц.
	 * Может быть передан объект \CDataProvider, в этом случае кол-во страниц
	 * и переменная навигации будет определена автоматически.
	 * @param string $baseUrl базовая ссылка
	 * @param array $params дополнительные параметры для генерации ссылки.
	 * По умолчанию пустой массив.
	 * @param string $pageVar имя переменной постраничной навигации.
	 * По умолчанию "p".
	 */
	public static function registerLinkTagNav($pageCount, $baseUrl, $params=[], $pageVar='p')
	{
		if($pageCount instanceof \CDataProvider) {
			$pageVar=$pageCount->pagination->pageVar;
			$pageCount=ceil($pageCount->totalItemCount / $pageCount->pagination->pageSize);
		}
	
		if($pageCount > 1)  {
			$p=(int)Y::request()->getQuery($pageVar, 1);
			$rel='prev';
			foreach(range(1, $pageCount) as $n) {
				if(($n == $p-1) || ($n == $p+1)) {
					if($n > $p) {
						$rel='next';
					}
					if($n == 1) {
						if(isset($params[$pageVar])) {
							unset($params[$pageVar]);
						}
					}
					else {
						$params[$pageVar]=$n;
					}
					Y::cs()->registerLinkTag($rel, null, Y::controller()->createUrl($baseUrl, $params));
				}
			}
		}
	}
	
	/**
	 * Установить язык приложения в массив дополнительных параметров.
	 * @param array &$options
	 */
	protected static function setOptionsLang(&$options)
	{
		if(!A::get($options,'lang')) {
			$options['lang']=Y::lang();
		}
	}
}
