<?php
/**
 * Класс-помощник для модуля "CRUD"
 */
namespace crud\components\helpers;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HFile;

class HCrud
{
	/**
	 * @var NULL|array подготовленная конфигурация.
	 * По умолчанию (NULL) - не подготовлена.
	 */
	protected static $configPrepared=null;
	
	/**
	 * Получить объект модели.
	 * @param string $id идентифкатор настроек CRUD для модели.
	 * @param integer|TRUE|NULL $pk идентификатор модели. По умолчанию NULL, 
	 * будет возвращена новая модель. 
	 * Может быть передано строгое TRUE, в этом случае будет возвращен 
	 * результат вызова статического метода \CActiveRecord::model().
	 * @param array|\CDbCriteria|NULL $criteria объект или конфигурация 
	 * дополнительных параметров для выборки модели. 
	 * По умолчанию NULL не задана.
	 * @param boolean $http404 если модель не найдена бросать исключение HTTP 404.
	 * По умолчанию (TRUE) - бросать исключение.
	 * @param string $scenario имя сценария новой модели. По умолчанию "insert".
	 * @return \CActiveRecord объект модели, либо NULL, если 
	 * возникли ошибки в конфигурации.
	 */
	public static function getById($id, $pk=null, $criteria=null, $http404=true, $scenario='insert')
	{
		if($className=self::param($id, 'class')) {
			if(is_array($className)) {
				$scenario=A::get($className, 1, $scenario);
				$className=A::get($className, 0);
			} 
			if($pk === true) $model=$className::model();
			elseif($pk === null) $model=new $className($scenario);
			else {
				if($criteria === null) $criteria='';
				$model=$className::model()->findByPk($pk, $criteria);
				
				if(!$model && $http404) throw new \CHttpException(404);
			}
			return $model;
		}
		
		if($http404) throw new \CHttpException(404);
		
		return null;
	}
	
	/**
	 * Получить конфигурацию настроек.
	 * @param integer $id идентифкатор настроек CRUD для модели. 
	 * По умолчанию (NULL) - получить все настройки.  
	 * @return array|NULL
	 */
	public static function config($id=null)
	{
		if($module=Y::module('common.crud')) {
			$config=self::prepareConfig($module->config);
			if($id) {
				return A::get($config, $id);
			}
			return $config; 
		}
		
		return null;
	} 
	
	/**
	 * Получить значение параметра конфигурации.
	 * @param string $id идентифкатор настроек CRUD для модели.
	 * @param string|array $name имя параметра или массив значений параметра.
	 * @param mixed $default значение по умолчанию. По умолчанию NULL.
	 * @param boolean $use проверять наличие параметра "use" и обработать его. 
	 * По умолчанию (FALSE) - не проверять.
	 * @return mixed
	 */
	public static function param($id, $name, $default=null, $use=false)
	{
		if($config=self::config($id)) {
			if(is_array($name)) {
				$value=empty($name) ? $default : $name;
			}
			else {
				$value=A::rget($config, $name, $default);
			}
			
			if(!empty($value) && $use) {
				if(is_string($name)) $valueUse=self::getParamUse($id, $name.'.use', $default);
				elseif(is_array($name)) $valueUse=self::getUse($id, A::get($name, 'use'), $default);
				if(isset($value['use'])) unset($value['use']);
				if(empty($value)) $value=[];
				
				return empty($valueUse) ? $value : A::m($valueUse, $value);
			}
			return $value;
		}
		
		return $default;
	}
	
	/**
	 * Получить данные для ссылки конфигурации.
	 * @param string $id идентифкатор настроек CRUD для модели.
	 * @param string $path путь к ссылке в конфигурации. Напр. "crud.update.url".
	 * @param string $default ссылка по умолчанию. По умолчанию "#".
	 * @param array $params дополнительные параметры для ссылки. По умолчанию пустой массив.
	 * @param string|NULL $mode режим преобразования. По умолчанию (NULL) без преобразования.
	 * Режимы:
	 * "toString" (или "s") - преобразовывать параметры в строку PHP кода массива.
	 * "byCreateUrl" (или "с") - получить массив для использования создания ссылки, напр.,
	 * методом CController::createUrl().  
	 * @return array массив вида [link, params] 
	 */
	public static function getConfigUrl($id, $path, $default='#', $params=[], $mode=null)
	{
		$url=HCrud::param($id, $path, $default);
		if(is_array($url)) {
			$link=array_shift($url);
			$params=A::m($params, $url);
		}
		else $link=$url;
		
		if(($mode == "byCreateUrl") || ($mode == "c")) {
			return A::m([$link], $params);
		}
		if(($mode == "toString") || ($mode == "s")) {
			$params=A::toPHPString($params, true, true);
		}
		
		return [$link, $params];
	}
	
	/**
	 * Получить значение параметра "use".
	 * Дополнительный параметр конфигурации "use" может содержать значение
	 * 1) (string) "путь внутри текущей конфигурации"
	 * 2) (array) ["пусть к файлу конфигурации", "путь к значению внтури массива конфигурации"]
	 * @param string $id идентифкатор настроек CRUD для модели.
	 * @param string $path путь к параметру "use", включая сам "use". Напр., "crud.create.form.use"
	 * @param mixed $default значение по умолчанию. По умолчанию NULL.
	 */
	public static function getParamUse($id, $path, $default=null)
	{
		return self::getUse($id, A::rget(self::config($id), $path), $default);
	}
	
	/**
	 * Получить значение параметра "use".
	 * @param string|array $id идентифкатор настроек CRUD для модели. Может быть 
	 * передан массив конфигурации.
	 * @param string|array $use идентифкатор настроек CRUD для модели.
	 * Дополнительный параметр конфигурации "use" может содержать значение
	 * 1) (string) "путь внутри текущей конфигурации"
	 * 2) (array) ["пусть к файлу конфигурации", "путь к значению внтури массива конфигурации"]
	 * @param mixed $default значение по умолчанию. По умолчанию NULL.
	 * @return mixed
	 */
	public static function getUse($id, $use, $default=null)
	{
		if(is_array($id)) $config=$id;
		else $config=self::config($id);
		
		$value=$default;
		if(is_array($use)) {
			if((count($use) == 2) && ($cfg=HFile::includeByAlias($use[0]))) {
				if($use[1] === null) $value=$cfg;
				else {
					$value=A::rget($cfg, $use[1], $default);
				}
			}
		}
		elseif(is_string($use)) {
			$value=A::rget($config, $use, $default);
		}
		
		return $value;
	}
	
	/**
	 * Получить конфигурацию параметра "sortable" (сортировки).
	 * @param string $id идентифкатор настроек CRUD для модели.
	 * @param string $path путь к параметру "sortable". По умолчанию "crud.index.gridView".
	 * @param boolean $checkDisabled проверять параметр "disabled" или нет. Если установлено
	 * (TRUE) то при наличии параметра "disabled"=>true, будет возвращено NULL.
	 * По умолчанию (FALSE) - не проверять.
	 * @return array|NULL массив конфигурации сортировки.
	 */
	public static function getSortable($id, $path='crud.index.gridView', $checkDisabled=false)
	{
		if($sortable=self::param($id, $path.'.sortable', null, true)) {
			if(A::get($sortable, 'category')) return $sortable;
		}
		return null;
	}
	
	/**
	 * Получить пункты меню для виджета zii.widgets.CMenu (для раздела администрирования).
	 * @param CController $controller объект контроллера, 
	 * который будет использован для создания ссылки.
	 * @param string|array|NULL $id идетификатор настроек модели 
	 * для которых возвращать пункт меню. Может быть передан массив идентфикаторов.
	 * По умолчанию (NULL) - возвращать все пункты меню. 
	 * @param string $baseUrl базовая ссылка для пунктов меню.
	 * @param boolean $returnItem возвращать только конфигурацию одного пункта меню. 
	 * По умолчанию (FALSE) - если результат будут содеражать только один пункт меню, 
	 * возвратиться как массив из одного пункта меню.
	 * @return multitype:multitype:\common\components\helpers\mixed NULL
	 */
	public static function getMenuItems($controller, $id=null, $baseUrl='/crud/admin/default/index', $returnItem=false)
	{
		$items=[];
		
		$module=Y::module('common.crud.admin');
		
		if($config=self::config()) {
			// @var callable получить пункт меню.
			$fAddItem=function($id, $params) use (&$items, $controller, $baseUrl, $config) {
				if(!A::rget($params, 'menu.backend.disabled', false)) {
					if($label=A::rget($params, 'menu.backend.label', A::rget($params, 'crud.index.title'))) {
						$items[]=[
							'label'=>$label,
							'url'=>[$baseUrl, 'cid'=>$id],
							'active'=>isset($_REQUEST['cid']) && ($_REQUEST['cid'] == $id)
						];
					}
				}
			};
			
			if(is_string($id)) {
				$fAddItem($id, A::get($config, $id));
			}
			else {
				if(is_array($id)) {
					$config=array_intersect_key($config, array_flip($id));
				}
				
				foreach($config as $id=>$params) {
					$fAddItem($id, $params);
				}
			}
		}
		
		if($returnItem) return array_pop($items);
		else return $items;
	}
	
	/**
	 * Подготовка конфигурации
	 * @param array|string $config конфигурация
	 * @return array
	 */
	protected static function prepareConfig($config, $loadUses=true)
	{
		if(self::$configPrepared === null) {
			$prepared=[];
				
			if(is_string($config)) {
				$config=HFile::includeByAlias($config);
			}
	
			if(is_array($config)) {
				foreach($config as $id=>$cfg) {
					if(is_string($id)) {
						if(is_string($cfg) && ($cfg=HFile::includeByAlias($cfg))) {
							$prepared[$id]=$cfg;
						}
					}
					elseif(is_string($cfg)) {
						$mainConfig=HFile::includeByAlias($cfg);
						if(is_array($mainConfig)) {
							foreach($mainConfig as $id2=>$cfg2) {
								if(is_string($id2) && is_string($cfg2) && ($cfg2=HFile::includeByAlias($cfg2))) {
									$prepared[$id2]=$cfg2;
								}
							}
						}
					}
				}
			}
			
			if($loadUses) {
				$prevLevel=0;
				A::rwalk($prepared, function(&$cfg, $key, &$path, $level) use (&$prepared, &$prevLevel) {
					if(($key === 'use') && is_array($cfg)) {
						$cfg=self::getUse([], $cfg);
						if(!empty($path)) {
							$spath=implode('.', array_slice($path, 0, $level));
							A::runset($prepared, $spath.'.use');
							A::rset($prepared, $spath, $cfg, true, -1);
						}
					}
					else {
						if($level === 0) $path=[];
						elseif($level < $prevLevel) {
							if(!empty($path) && $level) array_splice($path, $level);
							else $path=[];
						}
						elseif($level == $prevLevel) {
							if(!empty($path)) array_pop($path);
						}
						$path[]=$key;				
						$prevLevel=$level;
					}
				}, []);
			}
				
			self::$configPrepared=$prepared;
		}
	
		return self::$configPrepared;
	}	
}
