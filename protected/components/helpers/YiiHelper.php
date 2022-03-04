<?php
/**
 * Yii Helper
 * 
 * @version 1.06
 * 
 * @history:
 * 1.01: Add attributeExists() method.
 * 1.02: Object type now is available for $className parameter of slash2_() method.
 * 1.03: Add formatDate() method.
 * 1.04: Add arraySort() method.
 * 1.05: Add isDebugMode() method.
 * 1.06: Add isAction() method.
 */
class YiiHelper extends \CComponent
{
	const DS=DIRECTORY_SEPARATOR;
	
	const FLASH_SYSTEM_SUCCESS='system-success';
	const FLASH_SYSTEM_ERROR='system-error';
	
	/**
	 * Replace namespace "\" char to "_".
	 * Полезна для форм моделей с пространством имен.
	 * @param string|object $className
	 * @return mixed
	 */
	public static function slash2_($className) 
	{
		if(is_object($className)) $className = get_class($className);
		
		return preg_replace('/\\\\+/', '_', trim($className, '\\'));
	}
	
	/**
	 * Вырезать namespace
	 * @param string $className class name.
	 * @return string
	 */
	public static function cutNamespace($className)
	{
		return preg_replace('/.*?([^\\\\]+)$/', '\\1', $className);
	}
	
	/**
	 * Get class name
	 * @param object|string $className object or class name.
	 * @param string $withoutNamespace return without namespace.
	 * @return string
	 */
	public static function getClassName($className, $withoutNamespace=false)
	{
		$pattern = '/^' . ($withoutNamespace ? '.*?(' : '(.*?') . '[^\\\\]+)$/';
		return preg_replace($pattern, '\\1', (is_object($className) ? get_class($className) : $className));
	}
	
	/**
	 * Проверка существования аттрибута у модели.
	 * Актуально для моделей \CActiveRecord, т.к. property_exists возвращает 
	 * false на явно не объявленные свойства. 
	 * @param object $model модель.
	 * @param string $attribute аттрибут.
	 * @return boolean
	 */
	public static function attributeExists($model, $attribute)
	{
		try {
			$value = $model->$attribute;
			return true;
		}
		catch(\Exception $e) {
			return false;
		}
	}
	
	/**
	 * Получить время
	 * @param integer|string $datetime timestamp время.
	 * @param string $pattern шаблон отображения.
	 * @return string
	 */
	public static function formatDate($datetime, $pattern='dd.MM.yyyy HH:mm')
	{
		return \Yii::app()->dateFormatter->format($pattern, $datetime);
	}
	
	public static function formatDateVsRusMonth($datetime) 
	{
		$datetime = Yii::app()->dateFormatter->format('dd.MM.yyyy', $datetime);
		
		$yy = (int) substr($datetime,6,8);
		$mm = (int) substr($datetime,3,5);
		$dd = (int) substr($datetime,0,2);
		
		$monthRu =  array ('января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря');
		$monthEn =  array ('Jan', 'Feb', 'Mar', 'Apr', 'May', 'June', 'July', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec');
	    $month = (\Yii::app()->getLanguage() == 'ru') ? $monthRu : $monthEn;
		
		return ($dd > 0 ? $dd . " " : '') . $month[$mm - 1]." ".$yy;
	}
	
	/**
	 * Сортировка массива
	 * Сортировка массива по указанному порядку в массиве ключей
	 * Не возвращаются все ключи не входящие в $orderedKeys 
	 * @param array $array сортируемый массив
	 * @param array $orderedKeys массив упорядоченных ключей для сортировки 
	 * @return array отсортированный и отфильтрованный массив.
	 */
	public static function arraySort($array, $orderedKeys=array())
	{
		$result = array();
		foreach($orderedKeys as $key) {
			if(isset($array[$key])) $result[$key] = $array[$key];
		}
		return $result;
	}
	
	/**
	 * Проверяет установлен ли режим отладки для Yii или нет.
	 * @return boolean
	 */
	public static function isDebugMode()
	{
		return defined(YII_DEBUG) && (YII_DEBUG === true);
	}
	
	/**
	 * Проверяет является ли текущее действие заданным в параметрах.
	 * @param CController $controller объект проверяемого контроллера.
	 * @param string $controllerID имя контроллера.
	 * @param string $actionID имя действия.
	 * @return boolean
	 */
	public static function isAction($controller, $controllerID, $actionID)
	{
		return ($controller->id == $controllerID) && ($controller->action->id == $actionID);
	}
	
	public static function isController($controller, $controllerID)
	{
		return ($controller->id == $controllerID);
	}

	/**
	 * (non-PHPDoc)
	 * @see \Yii::app()->clientScript
	 */
	public static function cs()
	{
		return \Yii::app()->clientScript;
	}	

	/**
	 * (non-PHPDoc)
	 * @see \CClientScript::registerScript()
	 */
	public static function csJs($id, $script, $position=null, $htmlOptions=array())
	{
		return \Yii::app()->clientScript->registerScript($id, $script, $position, $htmlOptions);
	}
	
	/**
	 * (non-PHPDoc)
	 * @see \CClientScript::registerCoreScript()
	 */
	public static function csJsCore($name)
	{
		return \Yii::app()->clientScript->registerCoreScript($name);
	}
	
	/**
	 * (non-PHPDoc)
	 * @see \CClientScript::registerScriptFile()
	 */
	public static function csJsFile($url, $position=null, $htmlOptions=array())
	{
		return \Yii::app()->clientScript->registerScriptFile($url, $position, $htmlOptions);
	}
	
	/**
	 * (non-PHPDoc)
	 * @see \CClientScript::registerCss()
	 */
	public static function csCss($id, $css, $media='')
	{
		return \Yii::app()->clientScript->registerCss($id, $css, $media);
	}
	
	/**
	 * (non-PHPDoc)
	 * @see \CClientScript::registerCssFile()
	 */
	public static function csCssFile($url, $media='')
	{
		return \Yii::app()->clientScript->registerCssFile($url, $media);
	}

	/**
	 * (non-PHPDoc)
	 * @see \Yii::app()->request
	 */
	public static function request()
	{
		return \Yii::app()->request;
	}
	
	/**
	 * Завершить приложение
	 * @param boolean $die выполнить после завершения команду die или нет. По умолчанию TRUE - выполнить.
	 */
	public static function end($die=true)
	{
		\Yii::app()->end();
		if($die) die;
	}
	
	/**
	 * Создает функцию возвращения перевода для переданной категории.
	 * @param string $category категория перевода
	 * @return function
	 */
	public static function createT($category, $params=array())
	{
		return create_function('$msg, $params=array()', 'return \Yii::t(\''.$category.'\', $msg, $params);');
	}
	
	public static function setFlash($key, $value, $defaultValue=null)
	{
		\Yii::app()->user->setFlash($key, $value, $defaultValue);
	}
	
	public static function hasFlash($key)
	{
		return \Yii::app()->user->hasFlash($key);
	}
	
	public static function getFlash($key, $defaultValue=null, $delete=true)
	{
		return \Yii::app()->user->getFlash($key, $defaultValue, $delete);
	}
	
	public static function getMarkCssClass($model, $attributes) 
    { 
        return ($attribute=array_shift($attributes)) ? ($model->$attribute ? " {$attribute}" : self::getMarkCssClass($model, $attributes)) : ''; 
    }
}