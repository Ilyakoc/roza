<?php
/**
 * Поведение адаптера для механизма кэширования работающий с базой данных.
 * 
 */
namespace common\ext\cache\behaviors\adapters;

use common\components\helpers\HArray as A;
use common\components\helpers\HDb;
use common\ext\cache\interfaces\IAdapter;

class DbCache extends \CBehavior implements IAdapter
{
	/**
	 * @var имя таблицы
	 */
	public $tableName='cache';
	
	/**
	 * @var integer number of seconds that the data can remain in cache. Defaults to 60 seconds.
	 * If it is 0, existing cached content would be removed from the cache.
	 * If it is a negative value, the cache will be disabled (any existing cached content will
	 * remain in the cache.)
	 *
	 * Note, if cache dependency changes or cache space is limited,
	 * the data may be purged out of cache earlier.
	 */
	public $duration=60;
	
	/**
	 * Выполнить запрос создания триггеров.
	 * По умолчанию (FALSE) не запускать.
	 * @var boolean
	 */
	public $executeTriggersSQL=false;
	
	/**
	 * @var boolean показать SQL код триггеров, вместо выполнения.
	 * Требуется для добавления вручную, когда не хватает прав доступа к базе данных.
	 * Применяется только при условии DbCache::$executeTriggersSQL=TRUE.
	 */
	public $showTriggersSQL=false;
	
	/**
	 * @var array имена таблиц для которых будут созданы триггеры при инициализации.
	 * Элемент может быть передан как [tableName=>attribute]. 
	 * По умолчанию attribute="id". 
	 */
	public $tables=[];
	
	/**
	 * @var array имена таблиц для которых будут созданы триггеры после инициализации.
	 * Элемент может быть передан как [tableName=>attribute]. 
	 * По умолчанию attribute="id".
	 * 
	 * Может быть использовано при последущем добавлении нужных таблиц. 
	 * РЕКОМЕНДУЕТСЯ после создания триггеров перенести имена таблиц в 
	 * конфигурацию DbCache::$tables. 
	 */
	public $alterTables=[];
	
	/**
	 * @var array имена таблиц для которых будут удалены триггеры.
	 * РЕКОМЕНДУЕТСЯ после удаления триггеров удалить или закомментировать 
	 * данный параметр в конфигурации. 
	 */
	public $dropTables=[];
	
	/**
	 * @var string id кэша хранения даты последней очистки.
	 * По умолчанию "MnB78ZFjGhDZ".
	 */
	public $cacheClearId='MnB78ZFjGhDZ';
	
	/**
	 * @var integer периодичность очистки старых записей кэша (в секундах).
	 * По умолчанию 2592000 (30 дней)
	 */
	public $cacheClearTime=2592000;
	
	/**
	 * @var array записи кэширования. Используется при жадной загрузке.
	 */
	protected $cached=[];
	
	/**
	 * (non-PHPdoc)
	 * @see \common\ext\cache\interfaces\IAdapter::init()
	 */
	public function attach($owner)
	{
		parent::attach($owner);
		
		$this->init();	
	}
	
	/**
	 * Инициализация
	 */
	public function init()
	{
		if(!HDb::getTable($this->tableName)) {
			$dbName=HDb::getDbName();
			$query='CREATE TABLE '. HDb::qt($this->tableName). ' ('
				. '`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,'
				. '`model_id` INT(11) NOT NULL,'
				. '`model_table` VARCHAR(255) NOT NULL,'
				. '`key` VARCHAR(255) NOT NULL DEFAULT \'\','
				. '`update_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,'
				. 'KEY `model_id` (`model_id`),'
				. 'UNIQUE KEY `idx_model` (`model_id`, `model_table`(100), `key`(100)));';
			
			if($this->executeTriggersSQL) {
				$query.=$this->getTriggersSQL($this->tables, $dbName);
			}
			
			HDb::execute($query);
		}
		
		if($this->executeTriggersSQL && ($query=$this->getTriggersSQL($this->alterTables))) {
			HDb::execute($query);
		}
		
		if($this->executeTriggersSQL && ($query=$this->getDropTriggersSQL($this->dropTables))) {
			HDb::execute($query);
		}
		
		$this->clearCached();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \common\ext\cache\interfaces\IAdapter::beginCacheModel()
	 */
	public function beginCacheModel($model, $attribute='id', $hash='', $dependency=null)
	{
		$properties=['duration'=>$this->duration];
		
		if(!empty($dependency)) {
			$properties['dependency']=$dependency;
		}
		else {
			if(!empty($this->cached[$model->$attribute])) {
				$properties['dependency']=new \CExpressionDependency("'".$this->cached[$model->$attribute]."'");
			}
			else {
				// при одинаковом модель-ид и переданом хэше эта зависимость все равно сбивается
				// так как для него(хэша) нет старого значения.
				$sql='SELECT IFNULL((SELECT `update_time` FROM ' . HDb::qt($this->tableName) 
					. ' WHERE `key`=\'\' AND `model_id`=' . (int)$model->{$attribute} 
					. ' AND `model_table`=' . HDb::qv(trim(HDb::qt($model->tableName()), '`'))
					. '), DATE_FORMAT(NOW(), \'%Y-%m-01 00:00:00\'))';
					
				$properties['dependency']=new \CDbCacheDependency($sql);
			}
		}
		
		return \Yii::app()->getController()->beginCache(
			$this->getCacheId($model, $attribute, $hash), 
			$properties
		);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \common\ext\cache\interfaces\IAdapter::endCacheModel()
	 */
	public function endCacheModel()
	{
		\Yii::app()->getController()->endCache();
		
	}
	
	/**
	 * Предварительная подготовка к кэшированию фрагментов HTML кода отображения моделей.
	 * Применяется для увеличения быстродействия. 
	 * Производится жадная загрузка данных кэширования.
	 * @param array[\CModel] $models массив моделей. 
	 * @param string|NULL $attribute название атрибута, который будет 
	 * использован в качестве id модели. Может быть передано NULL, что означает, 
	 * что фрагментирование по id не требуется. По умолчанию "id".
	 */
	public function prependCacheModel($models, $attribute='id')
	{
		if(!empty($models)) {
			$ids=[];
			foreach($models as $model) {
				$ids[]=(int)$model->{$attribute};
			}
			$records=HDb::queryAll(
				'SELECT `model_id`, `update_time` FROM '.HDb::qt($this->tableName).' WHERE `model_id` IN ('.implode(',', $ids).')'
			);
			if(!empty($records)) {
				foreach($records as $record) {
					$this->cached[$record[0]]=$record[1];
				}
			}
		}
	}
	
	/**
	 * Финальные действия после вывода кэшированных фрагментов.
	 * РЕКОМЕНДУЕТСЯ применять при использовании DbCache::prependCacheModel()
	 */
	public function finalCacheModel()
	{
		$this->clearCached();
	}
	
	/**
	 * Очистка старых записей в таблице кэша
	 * @param boolean $forcy принудительно очистить. 
	 * По умолчанию (FALSE) будет очищено согласно указаннным параметрам:
	 * DbCache::$cacheClearId и DbCache::$cacheClearTime.  
	 * @param boolean $clearAll очистить все записи. 
	 * По умолчанию (FALSE) только старые.
	 */
	public function clearCache($forcy=false, $clearAll=false)
	{
		$runClear=$forcy;
		if(!\Yii::app()->cache->get($this->cacheClearId)) {
			\Yii::app()->cache->set($this->cacheClearId, 1, $this->cacheClearTime);
			$runClear=true;
		}
			
		if($runClear) {
			if($clearAll) {
				$sql='DELETE FROM '.HDb::qt($this->tableName);
			}
			else {
				$sql='DELETE FROM '.HDb::qt($this->tableName)
					. ' WHERE UNIX_TIMESTAMP(`update_time`) < UNIX_TIMESTAMP() - ' . (int)$this->cacheClearTime;
			}
			HDb::execute($sql);	
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \common\ext\cache\interfaces\IAdapter::update()
	 * 
	 * Опции обновления: array(
	 * 		"id"=>id модели (обязательный)
	 * 		"model"=>\CActiveRecird модель. (необязательый, если передан параметр "table")
	 * 		"table"=>имя таблицы модели (необязательный, если передан параметр "model")
	 * 		"key"=>дополнительный ключ (необязательный)
	 * )
	 */
	public function update($options=[])
	{
		if(!($id=A::get($options, 'id'))) {
			return false;
		}
		if(!($tableName=A::get($options, 'table'))) { 
			if(!($model=A::get($options, 'model'))) {
				return false;
			}
			if(!($model instanceof \CActiveRecord)) {
				return false;
			}
			$tableName=$model->tableName();
		}
		$key=A::get($options, 'key', '');
		
		$query='REPLACE ' . HDb::qt($this->tableName) 
			. ' (`model_id`, `model_table`, `key`, `update_time`) VALUES (:id, :table, :key, NOW())';
		
		return HDb::execute($query, [
			':id'=>$id,
			':table'=>trim(HDb::qt($tableName), '`'),
			':key'=>$key
		]);					
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \common\ext\cache\interfaces\IAdapter::delete()
	 * 
	 * Опции обновления: array(
	 * 		"id"=>id модели (обязательный)
	 * 		"model"=>\CActiveRecird модель. (необязательый, если передан параметр "table")
	 * 		"table"=>имя таблицы модели (необязательный, если передан параметр "model")
	 * 		"key"=>дополнительный ключ (необязательный). При удалении всех записей необходимо 
	 * 		передать строгое значение FALSE. 
	 * 		"all"=>удалить все записи, по переданному условию (необязательный).
	 * 		Учитывается только строгое значение TRUE.
	 * )
	 */
	public function delete($options=[])
	{
		if(!($id=A::get($options, 'id'))) {
			return false;
		}
		if(!($tableName=A::get($options, 'table'))) { 
			if(!($model=A::get($options, 'model'))) {
				return false;
			}
			if(!($model instanceof \CActiveRecord)) {
				return false;
			}
			$tableName=$model->tableName();
		}
		$key=A::get($options, 'key', '');
		
		if(A::get($options, 'all') === true) {
			$query='DELETE FROM ' . HDb::qt($this->tableName) .' WHERE `model_id`=:id AND `model_table`=:table';
			$params=[':id'=>$id, ':table'=>trim(HDb::qt($tableName), '`')];
			
			if($key !== false) {
				$query.=' AND `key`=:key';
				$params[':key']=$key;
			}
			
			HDb::execute($query, $params);
		}
		else {
			$query='DELETE FROM ' . HDb::qt($this->tableName) .' WHERE `model_id`=:id AND `model_table`=:table AND `key`=:key';
			
			return HDb::execute($query, [
				':id'=>$id,
				':table'=>trim(HDb::qt($tableName), '`'),
				':key'=>$key
			]);
		}					
	}
	
	/**
	 * Получить id кэша.
 	 * @param \CModel $model объект модели.
	 * @param string|NULL $attribute название атрибута, который будет 
	 * использован в качестве id модели. Может быть передано NULL, что означает, 
	 * что фрагментирование по id не требуется. По умолчанию "id".
	 * @param string $hash дополнительная хэш-строка для генерации идентификатора.
	 */
	protected function getCacheId($model, $attribute='id', $hash='')
	{
		//return \CHtml::modelName($model) . ($attribute ? '_'.$model->{$attribute} : '') . $hash;
		return 'c'.crc32(get_class($model) . $model->{$attribute} . $hash);
	}
	
	/**
	 * Очистка кэширования результатов. 
	 */
	protected function clearCached()
	{
		$this->cached=[];
	}
	
	/**
	 * Получить SQL триггера вставки записи.
	 * @param string $tableName имя таблицы, для которой 
	 * будет сгенерирован SQL код триггера.
	 * @param string $attribute имя атрибута id модели. По умолчанию "id". 
	 * @param string $dbName имя базы данных. 
	 * По умолчанию (NULL) будет получено методом DbCache::setDbName().
	 */
	protected function getInsertTriggerSQL($tableName, $attribute='id', $dbName=null)
	{
		$this->setDbName($dbName);
		
		$name="`{$tableName}_cache_insert`";
		$tn="`{$dbName}`.".HDb::qt($tableName);
		$tnValue=trim(HDb::qt($tableName), '`');
		$tnCache="`{$dbName}`.".HDb::qt($this->tableName);
		
		$sql="
DELIMITER |
CREATE TRIGGER {$name}
	AFTER INSERT ON {$tn} FOR EACH ROW
	BEGIN
		DECLARE hasrecord INT;
		SET hasrecord = (SELECT COUNT(*) FROM {$tnCache} WHERE `model_id`=NEW.{$attribute} AND `model_table`='{$tnValue}' AND `key`='');
		IF hasrecord > 0 
			THEN UPDATE {$tnCache} SET `update_time`=NOW() WHERE `model_id`=NEW.{$attribute} AND `model_table`='{$tnValue}' AND `key`='';
			ELSE INSERT INTO {$tnCache} (`model_id`, `model_table`, `update_time`) VALUES (NEW.{$attribute}, '{$tnValue}', NOW());
		END IF; 
	END; |
DELIMITER ;";
		
		return $sql;
	}
	
	/**
	 * Получить SQL триггера обновления записи.
	 * @param string $tableName имя таблицы, для которой
	 * будет сгенерирован SQL код триггера.
	 * @param string $attribute имя атрибута id модели. По умолчанию "id". 
	 * @param string $dbName имя базы данных.
	 * По умолчанию (NULL) будет получено методом DbCache::setDbName().
	 */
	protected function getUpdateTriggerSQL($tableName, $attribute='id', $dbName=null)
	{
		$this->setDbName($dbName);
		
		$name="`{$tableName}_cache_update`";
		$tn="`{$dbName}`.".HDb::qt($tableName);
		$tnValue=trim(HDb::qt($tableName), '`');
		$tnCache="`{$dbName}`.".HDb::qt($this->tableName);
		
		$sql="
DELIMITER |
CREATE TRIGGER {$name}
	AFTER UPDATE ON {$tn} FOR EACH ROW
	BEGIN
		DECLARE hasrecord INT;
		SET hasrecord = (SELECT COUNT(*) FROM {$tnCache} WHERE `model_id`=NEW.{$attribute} AND `model_table`='{$tnValue}' AND `key`='');
		IF hasrecord > 0 
			THEN UPDATE {$tnCache} SET `update_time`=NOW() WHERE `model_id`=NEW.{$attribute} AND `model_table`='{$tnValue}' AND `key`='';
			ELSE INSERT INTO {$tnCache} (`model_id`, `model_table`, `update_time`) VALUES (NEW.{$attribute}, '{$tnValue}', NOW());
		END IF; 
	END; |
DELIMITER ;";
		
		return $sql;
	}
	
	/**
	 * Получить SQL триггера удаления записи.
	 * @param string $tableName имя таблицы, для которой
	 * будет сгенерирован SQL код триггера.
	 * @param string $attribute имя атрибута id модели. По умолчанию "id". 
	 * @param string $dbName имя базы данных.
	 * По умолчанию (NULL) будет получено методом DbCache::setDbName().
	 */
	protected function getDeleteTriggerSQL($tableName, $attribute='id', $dbName=null)
	{
		$this->setDbName($dbName);
		
		$name="`{$tableName}_cache_delete`";
		$tn="`{$dbName}`.".HDb::qt($tableName);
		$tnValue=trim(HDb::qt($tableName), '`');
		$tnCache="`{$dbName}`.".HDb::qt($this->tableName);
		
		$sql="
DELIMITER |
CREATE TRIGGER {$name}
	AFTER DELETE ON {$tn} FOR EACH ROW
	BEGIN
		DELETE FROM {$tnCache} WHERE `model_id`=OLD.{$attribute} AND `model_table`='{$tnValue}' AND `key`='';
	END; |
DELIMITER ;";
		
		return $sql;
	}
	
	/**
	 * Получить SQL триггеров для таблиц.
	 * @param array $tables имена таблиц. Элемент может быть 
	 * передан как [tableName=>attribute]. По умолчанию attribute="id". 
	 * @param string $dbName имя базы данных.
	 * По умолчанию (NULL) будет получено методом DbCache::setDbName().
	 * @return string
	 */
	protected function getTriggersSQL($tables, $dbName=null)
	{
		$sql='';
		
		if(!empty($tables)) {
			$this->setDbName($dbName);
			foreach($tables as $tableName=>$attribute) {
				if(!is_string($tableName)) {
					$tableName=$attribute;
					$attribute='id'; 
				}
				$sql.=$this->getInsertTriggerSQL($tableName, $attribute, $dbName);
				$sql.=$this->getUpdateTriggerSQL($tableName, $attribute, $dbName);
				$sql.=$this->getDeleteTriggerSQL($tableName, $attribute, $dbName);
			}
			
			if($this->showTriggersSQL) {
				$this->printSQL($sql);
				$sql='';
			}
		}
		
		return $sql;
	}
	
	/**
	 * Получить SQL удаления триггеров.
	 * @param array $tables имена таблиц. 
	 * @param string $dbName имя базы данных.
	 * По умолчанию (NULL) будет получено методом DbCache::setDbName().
	 * @return string
	 */
	protected function getDropTriggersSQL($tables, $dbName=null)
	{
		$this->setDbName($dbName);
		
		$sql='';
		foreach($tables as $tableName) {
			$sql.='DROP TRIGGER `'.$dbName.$tableName.'_cache_insert`;';
			$sql.='DROP TRIGGER `'.$dbName.$tableName.'_cache_update`;';
			$sql.='DROP TRIGGER `'.$dbName.$tableName.'_cache_delete`;';
		}

		if($this->showTriggersSQL) {
			$this->printSQL($sql);
			$sql='';
		}
		
		return $sql;
	}
	
	/**
	 * Установить имя базы данных.
	 * @param string|NULL &$dbName переменная хранения имени базы данных.
	 * Для значения (NULL) будет получено установлено из метода HDb::getDbName().
	 */
	protected function setDbName(&$dbName)
	{
		if(!$dbName) $dbName=HDb::getDbName();
	}

	/**
	 * Вывод SQL. 
	 * @param string $sql SQL запрос. 
	 */
	protected function printSQL($sql)
	{
		echo "<pre>{$sql}</pre>";
	}
}
