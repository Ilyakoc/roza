<?php
/**
 * Yii helper
 * 
 * @version 1.0
 */
#namespace common\components\helpers;

use HArray as A;
use HHash;

class HYii
{
	/**
	 * @var string DIRECTORY_SEPARATOR alias. 
	 */
	const DS = DIRECTORY_SEPARATOR;
	
	const FLASH_SYSTEM_SUCCESS='system-success';
	const FLASH_SYSTEM_ERROR='system-error';
	
	private static $headScripts=[];
	
	public static function registerHeadScriptFile($url)
	{
		self::$headScripts[$url]=$url;
	}
	
	public static function getHeadScripts()
	{
		$html='';
		
		if(!empty(self::$headScripts)) {
			foreach(self::$headScripts as $url) {
				$html.=\CHtml::scriptFile($url);
			}
		}
		
		return $html;
	}
	
	public static function runLoader()
	{
		\Yii::app()->kontur_common_init->runLoader();
	}
	
	/**
	 * Выводит стек вызовов функций в массив
	 * @param integer $limit кол-во вызовов функций, которые будут выведены. 
	 * @param integer $options настройки. По умолчанию (false)
	 * DEBUG_BACKTRACE_IGNORE_ARGS & ~DEBUG_BACKTRACE_PROVIDE_OBJECT
	 * @see \debug_backtrace() 
	 */
	public static function backtrace($limit=10, $options=false)
	{
		if($options === false) {
			$options = DEBUG_BACKTRACE_IGNORE_ARGS & ~DEBUG_BACKTRACE_PROVIDE_OBJECT;
		}
		return debug_backtrace($options, $limit);
	}
	
	/**
	 * Результат выражения
	 * @param mixed|boolean $if значение выражения if
	 * @param mixed $then значение для then
	 * @param mixed $else значения для else
	 * @return mixed return $if ? $then : $else; 
	 */
	public static function c($if, $then, $else=null)
	{
		if($if) return $then;
		else $else;
	}
	
	/**
	 * Get class name
	 * @param object|string $className object or class name.
	 * @param string $withoutNamespace return without namespace.
	 * @return string
	 */
	public static function className($className, $withoutNamespace=false)
	{
		if(is_object($className)) $className = get_class($className);
		
		return $withoutNamespace ? preg_replace('/^.*?([^\\\\]+)$/', '\\1', $className) : $className;
	}
	
	/**
	 * Get path of class name.
	 * @see \Yii::getPathOfAlias()
	 * @param mixed $className object or class name.
	 * @return path to class name
	 */
	public static function getPathOfClassName($className)
	{
		if(is_object($className)) $className = get_class($className);
		
		$alias = preg_replace('/\\\\+/', '.', trim($className, '\\'));
		
		return \Yii::getPathOfAlias(substr($alias, 0, strrpos($alias, '.')));
	}
	
	/**
	 * Registers a piece of javascript code.
	 * @see \CClientScript::registerScript
	 * @param string $id В отличии от основного метода, может принимать NULL, 
	 * в таком случае будет сгенерирован уникальный ID. 
	 */
	public static function registerScript($id, $script, $position=null, $htmlOptions=array())
	{
		if(!$id) $id = HHash::ujs();
		
		return \Yii::app()->clientScript->registerScript($id, $script, $position, $htmlOptions);
	}
	
	/**
	 * (non-PHPDoc)
	 * @see self::registerScript()
	 */
	public static function js($id, $script, $position=null, $htmlOptions=array())
	{
		return self::registerScript($id, $script, $position, $htmlOptions);
	}

	/**
	 * (non-PHPDoc)
	 * @see \CClientScript::registerCoreScript()
	 */
	public static function jsCore($name)
	{
		return static::cs()->registerCoreScript($name);
	}

	/**
	 * Register script files
	 * @see \CClientScript::registerScriptFile()
	 *
	 * @param mixed $files string - файл, array - несколько файлов.
	 * @param integer|false $position позиция размещения. 
	 * @return void
	 */
	public static function registerScriptFiles($files, $position=false)
	{
		return self::_clientScriptRegisterFiles($files, 'registerScriptFile', $position);
	}
	
	/**
	 * (non-PHPDoc)
	 * @see self::registerScriptFiles()
	 */
	public static function jsFile($files, $position=false)
	{
		return self::registerScriptFiles($files, $position);
	}
	
	/**
	 * Register CSS files
	 * @see CClientScript::registerCssFile
	 * @param string|array $files
	 * @return void
	 */
	public static function registerCssFiles($files)
	{
		return self::_clientScriptRegisterFiles($files, 'registerCssFile');
	}
	
	/**
	 * (non-PHPDoc)
	 * @see self::registerCssFiles()
	 */
	public static function cssFile($files)
	{
		return self::registerCssFiles($files);
	}
	
	/**
	 * (non-PHPDoc)
	 * @see \CClientScript::registerCss();
	 * @param string|NULL $css CSS код. Для поддержки старого 
	 * вызова css=>cssFile, если передано NULL, считается, что 
	 * происходит вызов HYii::cssFile()
	 */
	public static function css($id, $css=null, $media='')
	{
		if($css === null) return self::cssFile($id);

		if(!$id) $id=uniqid('css');
		return \Yii::app()->clientScript->registerCss($id, $css, $media);
	}

	/**
	 * Register LESS files
	 * @uses \EAssetManager 
	 * @see https://github.com/Inpassor/yii-EAssetManager
	 * @param mixed $files имя файла или массив файлов.
	 */
	public static function registerLessFiles($files)
	{
		if(empty($files)) return;
		
		if(!is_array($files)) $files = array($files);
			
		$cssFiles = array();
		
		$am = \Yii::app()->assetManager;
		$lessCompiledPath = $am->lessCompiledPath;
		$webroot = \Yii::getPathOfAlias('webroot');
		foreach ($files as $file) {
			if(empty($file)) continue;
			$am->lessCompiledPath = $lessCompiledPath . HYii::DS . HFile::getDir($file);
			HFile::mkDir($am->lessCompiledPath, 0755, true);
			$compiledCSS = $am->lessCompile($webroot . $file);
			$cssFiles[] = HFile::pathToUrl(str_replace($webroot, '', $compiledCSS));
		}
		\Yii::app()->assetManager->lessCompiledPath = $lessCompiledPath;
		
		return self::registerCssFiles($cssFiles);
	}
	
	/**
	 * (non-PHPDoc)
	 * @see self::registerLessFiles()
	 */
	public static function less($files)
	{
		return self::registerLessFiles($files);
	}
	
	/**
	 * Publish assets.
	 * @see \CAssetManager::publish()
	 *
	 * @param string|array $path путь до публикуемого ресурса.
	 * Основное отличие от метода \CAssetManager::publish() в том, что
	 * данный параметр может принимать значение в виде массива вида:
	 * 	array(
	 * 		'path'=> <string>, // путь к ресурсам
	 * 		'js'=> <string|array>, // регистрируемый файл js скрипта, либо массив файлов.
	 * 		'css'=> <string|array> // регистрируемый файл css стиля, либо массив файлов.
	 * 		'less'=> <string|array> // регистрируемый файл less стиля, либо массив файлов.
	 *  )
	 * или упрощенный вариант с подключением одного файла.
	 *  array(path, js, css, less) при этом css и less может быть опущен.
	 *  Если необходимо подключить только css, то вместо js нужно передать NULL.
	 *  Если необходимо подключить только less, то вместо js и css нужно передать NULL.
	 * @param boolean $hashByName см. $hashByName метода CAssetManager::publish(). 
	 * @param integer $level см. $level метода CAssetManager::publish().
	 * @param boolean $forceCopy см. $forceCopy метода CAssetManager::publish().
	 *
	 * @return string an absolute URL to the published asset
	 */
	public static function publish($path, $hashByName=false, $level=-1, $forceCopy=null)
	{
		$data = $path;
	
		if(is_array($data)) {
			$path = A::get($data, 'path', A::get($data, 0));
		}
		else {
			$data = array();
		}
		
		if(is_array($path)) {
			$path=HFile::path($path);
		}
			
		if(realpath($path) === false) return false;
			
		if($forceCopy === null)
			$forceCopy = defined('YII_DEBUG') && (YII_DEBUG === true);
	
		$baseUrl = \Yii::app()->assetManager->publish($path, $hashByName, $level, $forceCopy);
	
		// Регистрируем скрипты и стили
		$register = function($key, $method, $pos) use ($data, $baseUrl) {
			$files = A::toArray(A::get($data, $key, A::get($data, $pos)));			
			array_walk($files, function(&$item, $key) use ($baseUrl) { $item = $item ? "{$baseUrl}/$item" : ''; });
			 	
			return self::$method($files);
		};
	
		$register('js', 'registerScriptFiles', 1);
		$register('css', 'registerCssFiles', 2);
		$register('less', 'registerLessFiles', 3);
			
		return $baseUrl;
	}
	
	/**
	 * Получить URL к опубликованному ресурсу.
	 * @see \CAssetManager::getPublishedUrl()
	 */
	public static function getPublishedUrl($path, $hashByName=false)
	{
		return \Yii::app()->assetManager->getPublishedUrl($path, $hashByName);
	}
	
	/**
	 * Получить абсолютный путь к опубликованному ресурсу.
	 * @see \CAssetManager::getPublishedPath()
	 */
	public static function getPublishedPath($path, $hashByName=false)
	{
		return \Yii::app()->assetManager->getPublishedPath($path, $hashByName);
	}
	
	/**
	 * Проверяет опубликован ли ресурс или нет.
	 * @see \CAssetManager::getPublishedPath()
	 */
	public static function isPublish($path, $hashByName=false)
	{
		return (bool)self::getPublishedPath($path, $hashByName);
	}
	
	/**
	 * Include file
	 * Если файл не найден, то возращается значение по умолчанию.
	 * @param string $filename file name.
	 * @param mixed $default значение, которое возвращать, если файл не найден. По умолчанию NULL.
	 * @return mixed Если файл не найден, возвращется значение переданное в параметре $default.
	 */
	public static function includeFile($filename, $default=null)
	{
		return is_file($filename) ? include($filename) : $default;
	}
	
	/**
	 * Include file by path alias
	 * Если файл не найден, то возращается значение по умолчанию.
	 * @param string $alias path alias.
	 * @param mixed $default значение, которое возвращать, если файл не найден. По умолчанию NULL.
	 * @return mixed Если файл не найден, возвращется значение переданное в параметре $default.
	 */
	public static function includeByAlias($alias, $default=null)
	{
		$filename=\Yii::getPathOfAlias($alias) . '.php';
		return is_file($filename) ? include($filename) : $default;
	}
	
	/**
	 * @see \Yii::app()->getController()
	 * @return \CController
	 */
	public static function controller()
	{
		return \Yii::app()->getController();
	}
	
	/**
	 * Проверяет является ли текущее действие заданным в параметрах.
	 * @param \CController $controller объект проверяемого контроллера.
	 * @param string $controllerID имя контроллера.
	 * @param mixed $actionID имя действия. 
	 *  1. Если передано значение NULL, проверит только на соответствие контроллеру.
	 *  2. Если передан массив, проверит как "один из"
	 * @return boolean
	 */
	public static function isAction($controller, $controllerID, $actionID=null)
	{
		return ($controller->id == $controllerID) 
			&& (is_array($actionID) ? in_array($controller->action->id, $actionID) 
					: ($actionID ? ($controller->action->id == $actionID) : true));
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
	 * Заменяет в строке двойные кавычки на одинарные
	 * @param string $str входная строка
	 * @return string
	 */
	public static function q($str)
	{
		return str_replace('"', "'", $str);
	}
	
	/**
	 * Получить схему таблицы в базе данных.
	 * @see \CDbSchema::getTable()
	 * @param string $tableName
	 * @param \CDbConnection $db db connection object. Если не задан, берется \Yii::app()->db
	 * @throws \CDbException
	 * @return \CDbTableSchema
	 */
	public static function getDbTable($tableName, $refresh=false, $db=null) 
	{
		if($db === null) {
			$db = \Yii::app()->db;
		}
		elseif(!($db instanceof \CDbConnection)) {
			throw new \CDbException('$db not instace of \CDbConnection');
		}
		
		return $db->getSchema()->getTable($tableName);
	}
	
	/**
	 * Для поддержки старых версий
	 * @see HRequest::isAjaxRequest()
	 * @return boolean
	 */
	public static function isAjaxRequest()
	{
		return HRequest::isAjaxRequest();
	}	
	
	/**
	 * Сравненение двух дат.
	 * 
	 * @param string $datetime1 дата 1 
	 * @param string $datetime2 дата 2 
	 * @param boolean $returnInterval если установлен в TRUE, возвращает \DateInterval, если  
	 * установлено в FALSE, возвращает разнице в секундах. По умолчанию FALSE.
	 * @param boolean $absolute используется, чтобы вернуть 
	 * абсолютную разницу при заданном значении $returnInterval в TRUE. По умолчанию FALSE.
	 * @return mixed (integer) количество секунд разницы ($returnInterval=FALSE), 
	 * либо \DateInterval ($returnInterval=TRUE), 
	 * либо FALSE в случае ошибки ($returnInterval=TRUE). 
	 */
	public static function dateTimeDiff($datetime1, $datetime2, $returnInterval=false, $absolute=false)
	{
		$datetime1 = new \DateTime($datetime1);
		$datetime2 = new \DateTime($datetime2);
		
		if($returnInterval) {
			$interval = $datetime1->diff($datetime2, $absolute);
		}
		else {
			$u1 = (int)$datetime1->format('U');
			$u2 = (int)$datetime2->format('U');
			$interval = $u1 - $u2;
		}
		
		return $interval; 
	}
	
	/**
	 * Получение алиса по переданному пути.
	 * @param string $path путь
	 * @param boolean $forcibly принудительно возвращать алиас, если даже не определен
	 * базовый алиас (webroot, application). По умолчанию TRUE.
	 * @param array $baseAliases массив базовых алиасов. По умолчанию array('application', 'webroot').
	 * @return string|null алиас пути, если не определен, возвращается null.
	 */
	public static function getAliasOfPath($path, $forcibly=true, $baseAliases=array('application', 'webroot'))
	{
		$alias = null;
		
		// @var function Проверка алиаса для заданного пути.
		// @param string $baseAlias базовый алиас.
		// @return string|null alias для пути от базового алиаса. 
		// Если путь не принадлежит базовому алиасу возвращается null.
		$funcGetAlias = function($baseAlias) use ($path) {
			$basePath = \Yii::getPathOfAlias($baseAlias);
			return (strpos($path, $basePath) === 0) 
				? ($baseAlias . preg_replace('/[\/\\\\]+/', '.', substr($path, strlen($basePath))))
				: null;
		};
		
		foreach($baseAliases as $baseAlias) 
			if($alias = $funcGetAlias($baseAlias)) break;
		
		if(!$alias && $forcibly) {
			$alias = preg_replace('/[\/\\\\]+/', '.', $path);
		}
		
		return $alias;
	}
	
	/**
	 * Регистрация файлов компонентом \Yii::app()->clientScript
	 * @param mixed $files файл или массив файлов.
	 * @param string $method метод регистрации
	 * @param integer|false $position позиция размещения. 
	 */
	private static function _clientScriptRegisterFiles($files, $method, $position=false)
	{
		if(empty($files)) return;
		
		if(!is_array($files)) $files = array($files);
			
		$cs = \Yii::app()->clientScript;
		foreach ($files as $file) {
			if(!empty($file)) {
				if($position && ($method == 'registerScriptFile')) {
					$cs->registerScriptFile($file, $position);
				}
				else {
					$cs->$method($file);
				}
			}
		}
	
		return;
	}	
    
	/**
	 * Alias for \Yii::app()->clientScript
	 * @return \CClientScript \Yii::app()->clientScript
	 */
	public static function cs()
	{
		return \Yii::app()->clientScript;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \CJSON::encode()
	 */
	public static function json($data)
	{
		return CJSON::encode($data);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Yii::app()->createUrl()
	 */
	public static function createUrl($route, $params=array(), $ampersand='&')
	{
		return self::controller()->createUrl($route, $params, $ampersand);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Yii::app()->createUrl()
	 */
	public static function createAbsoluteUrl($route, $params=array(), $ampersand='&')
	{
		return self::controller()->createAbsoluteUrl($route, $params, $ampersand);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Yii::app()->end()
	 */
	public static function end()
	{
		\Yii::app()->end();
		die;
	}
	
	/**
	 * Get value from \Yii::app()->params
	 * @param mixed $name ключ или ключи массива \Yii::app()->params от ключа верхнего уровня 
	 * до ключа нижнего уровня.
	 * (string) ключи должны передаваться с разделителем указанным в параметре $delimiter.
	 * (array) массив ключей от ключа верхнего уровня до ключа нижнего уровня. 
	 * @param string $default значение по умолчанию. По умолчанию NULL.
	 * @param string $delimiter разделитель для параметра $name. По умолчанию "."(точка).
	 */
	public static function param($name, $default=null, $delimiter='.')
	{
		return A::rget(\Yii::app()->params, $name, $default);
	}
	
	/**
	 * Для поддержки старых версий.
	 * @see HRequest::request()
	 * @return \CHttpRequest
	 */
	public static function request()
	{
		return HRequest::request();
	}
	
	/**
	 * Для поддержки старых версий.
	 * @see HRequest::requestGet()
	 * @return mixed
	 */
	public static function requestGet($name, $default=null, $onlyPost=false)
	{
		return HRequest::requestGet($name, $default, $onlyPost);
	}

	/**
	 * Создает функцию возвращения перевода для переданной категории.
	 * @param string $category категория перевода
	 * @return string - название созданной функции
	 */
	public static function createT($category, $moduleName=null)
	{
		if($moduleName)
			self::module($moduleName);
		
		return create_function('$msg, $params=array()', 'return \Yii::t(\'' . $category . '\', $msg, $params);');
	}
	
	/**
	 * Алиас для HYii::createT()
	 * @see HYii::createT()
	 */
	public static function ct($category, $moduleName=null)
	{
		return self::createT($category, $moduleName);
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
	
	/**
	 * Получить время с русскими названиями месяцев в формате "dd месяц yyyy".
	 * @param integer|string $datetime timestamp время.
	 * @return string
	 */
	public static function formatDateVsRusMonth($datetime)
	{
		$datetime = \Yii::app()->dateFormatter->format('dd.MM.yyyy', $datetime);
	
		$yy = (int) substr($datetime,6,8);
		$mm = (int) substr($datetime,3,5);
		$dd = (int) substr($datetime,0,2);
	
		$monthRu =  array ('января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря');
		$monthEn =  array ('Jan', 'Feb', 'Mar', 'Apr', 'May', 'June', 'July', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec');
	    $month = (\Yii::app()->getLanguage() == 'ru') ? $monthRu : $monthEn;
	
		return ($dd > 0 ? $dd . " " : '') . $month[$mm - 1]." ".$yy;
	}
	
	/**
	 * @see \YiiBase::getPathOfAlias()
	 * @param string $alias алиас
	 * @param boolean $forcy принудиельно возвращать путь. Если передано TRUE и путь не найден,
	 * то будет возвращен $alias. По умолчанию FALSE.
	 * @return mixed 
	 */
	public static function getPathOfAlias($alias, $forcy=false)
	{
		$path=\Yii::getPathOfAlias($alias);
		return ($forcy && !$path) ? $alias : $path;
	}
	
	/**
	 * Заполняет свойства объекта из переданного массива параметров.
	 * @param mixed $object объект.
	 * @param array $compact массив параметров [name=>value].
	 * @return boolean если массив параметров был массивом и не пустым вернет TRUE, иначе FALSE. 
	 */
	public static function oextract(&$object, $compact=[])
	{
		if(!empty($compact) && is_array($compact)) {
			foreach($compact as $name=>$value) 
				$object->$name=$value;
			return true;
		}
		return false;
	}
	
	/**
	 * Разрешить имя класса модели из имени возвращаемое \CHtml::modelName()
	 * @param string $name имя, возвращаемое \CHtml::modelName().
	 * @return string|NULL имя класса модели. Если переданное имя не является строкой возвращается NULL.
	 */
	public static function resolveModelClass($name)
	{
		return is_string($name) ? str_replace('_', '\\', $name) : null;
	}
	
	/**
	 * Подключение модуля
	 */
	public static function loadModule($moduleName)
	{
		return self::module($moduleName);
	}

	/**
	 * (non-PHPdoc)
	 * @see CWebApplication::getModule()
	 * @param string $id может быть передана цепочка модулей через символ разделения.
	 * @param string $delimiter символ разделения в цепочке модулей. По умолчанию "."(точка).
	 */
	public static function module($id, $delimiter='.')
	{
		if(strpos($id, $delimiter) === false) {
			return \Yii::app()->getModule($id);
		}
		
		$module=\Yii::app();
		foreach(explode($delimiter, $id) as $id) {
			$module=$module->getModule($id);
		}
		return $module;
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

	/**
	 * Короткий метод доступа к \Yii::app()->cache
	 * @return \CCache
	 */
	public static function cache()
	{
		return \Yii::app()->cache;
	}
	
	/**
	 * Очистка всего кэша
	 */
	public static function cacheFlush()
	{
		return self::cache()->flush();
	}

	/**
	 * Отправка письма
	 * @param string $message текст сообщения.
	 * @param string $subject тема сообщения.
	 * @param string $to e-mail адрес доставки.
	 */
	public static function send($message, $subject='', $to='')
	{
		$email=\Yii::app()->email;
			
		$email->from='noreply@'. \Yii::app()->request->getServerName();
		$email->to=$to ? $to : self::param('adminEmail');
		$email->subject=$subject ? $subject : \Yii::t('CommonModule.email', 'default.subject', ['{sitename}'=>\Yii::app()->name]);
		$email->message=$message;
			
		return $email->send();
	}
	
	/**
	 * Получить текущий язык приложения.
	 * @return string
	 */
	public static function lang()
	{
		return \Yii::app()->getLanguage();
	}
	
	/**
	 * Проверяет авторизован ли пользователь и соотвествует ли он роли.
	 * @param string $role роль пользователя. По умолчанию (NULL) - любая.
	 * @return boolean
	 */
	public static function isUser($role=null)
	{
		if(\Yii::app()->user->isGuest) return false;
		if($role) return (\Yii::app()->user->role == $role);
		return true; 
	}
	
	/**
	 * Проверяет есть ли у объекта поведение (по имени класса)
	 * @param object $obj объект.
	 * @param string|array $behaviorClass имя класса поведения или массив имен классов.
	 * @param boolean $or если передан массив в $behaviorClass использовать результат 
	 * объединения "или"(один из). По умолчанию (false) использовать "и"(все). 
	 * @return boolean
	 */
	public static function hasBehaviorByClass($obj, $behaviorClass, $or=false)
	{
		$hasBehavior=false;
		
		if(method_exists($obj, 'behaviors')) {
			$behaviorClass = A::toa($behaviorClass);
			
			$founded=[];
			foreach($obj->behaviors() as $name=>$behavior) {
				if(is_array($behavior)) {
					if(in_array(A::get($behavior, 'class'), $behaviorClass, true)) {
						$founded[]=$name;
					}
				}
				elseif(in_array($behavior, $behaviorClass, true)) {
					$founded[]=$name;
				}
			}
			
			if($or) {
				$hasBehavior = (count($founded) > 0);
			}
			else {
				$hasBehavior = (count($founded) === count($behaviorClass));
			}
		}
		
		return $hasBehavior;
	}
}
