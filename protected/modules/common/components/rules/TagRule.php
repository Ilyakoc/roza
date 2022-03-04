<?php
/**
 * Правило маршрутизации для фильтра
 * 
 * Ссылка вида: /<baseUrl>/<tagName>/param1/value1/param2/value2;value3
 */
namespace common\components\rules;

use common\components\helpers\HArray as A;

class TagRule extends \common\components\base\Rule
{
	/**
	 * @var string имя контроллера фильтра.
	 */
	public $controllerName;
	
	/**
 	 * @var string имя действия фильтра.
	 */
	public $controllerAction='filter';
	
	/**
	 * @var string базовый ЧПУ.
	 */
	public $baseUrl='filter';
	
	/**
	 * @var string|NULL псевдоним для ссылки фильтрации. 
	 * По умолчанию (NULL) не задан.
	 */
	public $baseAlias=null;
	
	/**
	 * @var string|NULL имя атрибута. 
	 * Если задано, правило используется только для данного атрибута, а также,
	 * при заданном TagRule::$baseAlias имя атрибута не будет включено в путь. 
	 * По умолчанию (NULL) - не задано.
	 */
	public $attribute=null;
	
	/**
	 * @var string|NULL значение атрибута. При заданном значении, и установленных
	 * TagRule::$baseAlias и TagRule::$attribute имя атрибута и значения не будет 
	 * включено в путь.
	 * Используется только при установленном TagRule::$attribute.
	 */
	public $attributeValue=null;
	
	/**
	 * @var string ключевое cлово фильтра.
	 */
	public $tagName='tag';

	/**
	 * @var string имя параметра в $_GET (или $_POST, 
	 * если задан DShopTagRule::$post=true).
	 */
	public $paramName='filter';
	
	/**
	 * @var boolean помещать переменные в $_POST.
	 * Если установлено FALSE переменные помещаются в $_GET. 
	 */
	public $post=true;
	
	/**
	 * @var string cимвол объединения/разделения множеcтвенных значений параметра.
	 */
	public $paramDelimiter=';';
	
	/**
	 * @var string значение пустого параметра.
	 * Если не задан, параметр будет проигнорирован.
	 */
	public $emptyValue='-';
	
	/**
	 * @var string имя модуля CMS. Если задано, будет проверено
	 * подключение данного модуля CMS. По умолчанию NULL.
	 * Использует \Yii::app()->d->isActive($moduleName).
	 */
	public $moduleName=null;
	
	/**
	 * (non-PHPdoc)
	 * @see CBaseUrlRule::createUrl()
	 * Параметры фильтра должны передаваться в перменной $params[TagRule::$paramName]
	 */
	public function createUrl($manager, $route, $params, $ampersand)
	{
		if($this->moduleName && !\Yii::app()->d->isActive($this->moduleName)) 
			return false;
		
		if($this->attribute && !isset($params[$this->paramName][$this->attribute])) {
			return false;
		}
		
		if($route == ($this->controllerName . '/' . $this->controllerAction)) {
			if($this->baseAlias) $path=[$this->baseAlias];
			else $path=[$this->baseUrl, $this->tagName];
			
			if(isset($params[$this->paramName])) {
				foreach($params[$this->paramName] as $name=>$value) {
					if(($value === '') || ($value === null)) {
						if($this->emptyValue) $value=$this->emptyValue;
						else continue;
					}
					if($this->attribute && $this->attributeValue) continue;
					if(!$this->baseAlias || !$this->attribute) $path[]=$name;
					if(is_array($value)) {
						$path[]=implode($this->paramDelimiter, $value);
					}
					else {
						$path[]=$value;
					}
				}
				unset($params[$this->paramName]);
			}
			return $this->getUrl($path) . $this->createPathInfo($manager, $params, $ampersand);
		}
		
		return false;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CBaseUrlRule::parseUrl()
	 */
	public function parseUrl($manager, $request, $pathInfo, $rawPathInfo)
	{
		if($this->moduleName && !\Yii::app()->d->isActive($this->moduleName))
			return false;
		
		$pathInfo=trim($pathInfo, '/');
		if(preg_match('/^'.$this->baseUrl.'\/'.$this->tagName.'$/i', $pathInfo)) {
			if($this->attribute && $this->attributeValue) {
				if($this->post) $_POST[$this->paramName][$this->attribute]=[$this->attributeValue];
				else $_GET[$this->paramName][$this->attribute]=[$this->attributeValue];
			}
			return $this->controllerName . '/' . $this->controllerAction;
		}
		elseif(preg_match('/^'.$this->baseUrl.'\/'.$this->tagName.'\/(.*)$/i', $pathInfo, $matches)
			|| ($this->baseAlias && preg_match('/^'.$this->baseAlias.'\/(.*)$/i', $pathInfo, $matches))) 
		{
			if($this->attribute) {
				if($this->attributeValue) $params=[[$this->attribute, $this->attributeValue]];
				else $params=[[$this->attribute, $matches[1]]];
			}
			else {
				$params=array_chunk(explode('/',$matches[1]), 2);
			}
						
			foreach($params as $param) {
				list($name, $values) = $param;
				if(!$this->emptyValue || ($this->emptyValue && ($values !== $this->emptyValue))) {
					if($this->post) $_POST[$this->paramName][$name]=explode($this->paramDelimiter, $values);
					else $_GET[$this->paramName][$name]=explode($this->paramDelimiter, $values);
				}
			}
				
			return $this->controllerName . '/' . $this->controllerAction;
		}

		return false;
	}
}
