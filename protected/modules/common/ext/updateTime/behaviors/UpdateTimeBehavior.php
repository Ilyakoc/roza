<?php
/**
 * Поведение атрибута даты обновления записи.
 * 
 */
namespace common\ext\updateTime\behaviors;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HDb;
use common\components\helpers\HRequest;

class UpdateTimeBehavior extends \CBehavior
{
	/**
	 * @var boolean добавлять поле в таблицу модели, если такого не существует.
	 * По умолчанию (TRUE) добавить.
	 */
	use \common\traits\models\AddColumn;
	
	/**
	 * @var string имя атрибута.
	 */
	public $attribute='update_time';
	
	/**
	 * @var string подпись атрибута.
	 */
	public $attributeLabel=null;
	
	/**
	 * @var string имя атрибута идентификатора модели.
	 * По умолчанию "id".
	 */
	public $attributeId='id';
	
	/**
	 * @var boolean автоматически регистрировать заголовки HTTP 304.
	 * По умолчанию (FALSE).
	 * Данный параметр может быть задан через настройки основной конфигурации.
	 * Имя параметра настройки UpdateTimeBehavior::$paramAutoSendLastModified.
	 */
	public $autoSendLastModified=true;
    public static $staticAutoSendLastModified=null;
	
	/**
	 * @var string имя параметра настройки автоматической регистрации заголовка HTTP 304
	 * в основной конфигурации приложения.
	 * По умолчанию:
	 * "common.ext.updateTime.behaviors.UpdateTimeBehavior.autoSendLastModified"
	 */
	public $paramAutoSendLastModified='common.ext.updateTime.behaviors.UpdateTimeBehavior.autoSendLastModified';
	
	/**
	 * @access protected
	 * @var array последнее время всех моделей использующих во время 
	 * выпонения скрипта данное поведение при использовании 
	 * метода UpdateTimeBehavior::sendLastModified(). 
	 */
	protected static $lastModifieds=[];
	
	/**
	 * @access protected
	 * @var boolean обработчик отправки даты последней модификации страницы
	 * при завершении скрипта зарегистрирован. 
	 * По умолчанию (FALSE) не зарегистрирован.
	 */
	protected static $lastModifiedShutdownRegistered=false;
	
	/**
	 * (non-PHPdoc)
	 * @see \CBehavior::events()
	 */
	public function events()
	{
		$events=[
			'onBeforeSave'=>'beforeSave',
			'onAfterUpdate'=>'afterUpdate'
		];
		
		if($this->isAutoSendLastModified()) {
			$events['onAfterFind']='afterFind';
		}
		
		return $events;
		
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \CBehavior::attach()
	 */
	public function attach($owner)
	{
		parent::attach($owner);
		
		if($this->attributeLabel === null) {
			$t=Y::ct('\common\ext\updateTime\Messages.common', 'common');
			$this->attributeLabel=$t('default.label');
		}
		
		if($this->addColumn($this->owner->tableName(), $this->attribute, 'TIMESTAMP')) {
			HDb::execute('UPDATE '.HDb::qt($this->owner->tableName()) . ' SET '.HDb::qc($this->attribute).'=NOW()');
		}
	}
    
    public function isAutoSendLastModified()
    {
        if(static::$staticAutoSendLastModified !== null) {
            return (bool)static::$staticAutoSendLastModified;
        }
        
        if($this->autoSendLastModified === null) {
            return Y::param($this->paramAutoSendLastModified, $this->autoSendLastModified);
        }
        
        return (bool)$this->autoSendLastModified;
    }
    
    public static function setStaticAutoSendLastModified($status)
    {
        static::$staticAutoSendLastModified=$status;
    }
	
	/**
	 * Установка кэширования.
	 * @param integer $cacheTime время кэширования в секундах. По умолчанию 60 секунд.
	 * @param \CDbCriteria|array|NULL|FALSE $criteria объект или параметры критерия 
	 * выборки для зависимости кэша. По умолчанию (NULL) будет использован текущий 
	 * критерий модели. Может быть передано FALSE если требуется не использовать критерий.
	 * @return \common\components\base\ActiveRecord
	 */
	public function utcache($cacheTime=60, $criteria=null)
	{
		if($criteria === null) {
			$criteria=$this->owner->getDbCriteria();
		}
		
		return $this->owner->cache($cacheTime, $this->getDbCacheDependency($criteria));		
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \CActiveRecord::rules()
	 */
	public function rules()
	{
		return [
			[$this->attribute, 'safe']
		];
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \CActiveRecord::attributeLabels()
	 */
	public function attributeLabels()
	{
		return [
			$this->attribute=>$this->attributeLabel
		];
	}
	
	/**
	 * Получить последнее время обновления в таблице модели. 
	 * @param string|\CDbCriteria|array $condition дополнительное условие выборки. 
	 * По умолчанию пустая строка. Может быть передан объект или параметры критерия.
	 * @todo для объекта критерия, на данный момент, используется только параметры 
	 * "condition" и "params".  
	 * @param array $params параметры для условия выборки. По умолчанию пустой массив.
	 * @param string $returnSql возвратить SQL строку запроса. 
	 * По умолчанию (FALSE) - возвратить результат запроса.
	 * В этом случае НЕ 
	 * @return timestamp|string
	 */
	public function getLastUpdateTime($condition='', $params=[], $returnSql=false)
	{
		$query='SELECT MAX(' . HDb::qc($this->attribute) . ') FROM ' . HDb::qt($this->owner->tableName());
		
		if(is_array($condition)) {
			$condition=new \CDbCriteria($condition);
		}

		$where=false;
		if($condition instanceof \CDbCriteria) {
			$params=A::m($condition->params, $params);
			$where=$condition->condition;
		}
		elseif(is_string($condition)) {
			$where=$condition;
		}
		
		// @FIXME При некоторых настройках PHP $where становится объектом \CDbCriteria или массивом.
		if($where) {
			if(($where instanceof \CDbCriteria)) {
				if($where->condition) {
					$query.=' WHERE ' . $where->condition;
				}
			}
			elseif(is_array($where)) {
				if(is_array($condition->condition)) {
					$query.=' WHERE ' . A::get($condition->condition, 'condition');
					$params=A::get($condition->condition, 'params', []);
				}
				elseif($condition->condition) {
					$query.=' WHERE ' . $condition->condition;
				}
			}
			else $query.=' WHERE ' . $where;
		}
	
		if($returnSql) {
			foreach($params as $name=>$value) $params[$name]=HDb::qv($value);
			return strtr($query, $params);
		}
		
		return HDb::queryScalar($query, $params);
	}
	
	/**
	 * Получить объект зависимости.
	 * @param string|\CDbCriteria|array $condition дополнительное условие выборки. По умолчанию пустая строка.
	 * Может быть передан объект или параметры критерия.
	 * @param array $params параметры для условия выборки. По умолчанию пустой массив.
	 * @return \CDbCacheDependency
	 */
	public function getDbCacheDependency($condition='', $params=[])
	{
		if(is_string($condition)) {
			$criteria=HDb::criteria(['condition'=>$condition]);			
		}
		else {
			$criteria=HDb::criteria($condition);
		}

		if($id=$this->owner->{$this->attributeId}) {
			$criteria->addColumnCondition(['id'=>$id]);
		}
		
		return new \CDbCacheDependency($this->getLastUpdateTime($criteria, $params, true));
	}
	
	/**
	 * Event: onAfterFind
	 * @return boolean
	 */
	public function afterFind()
	{
		if($this->isAutoSendLastModified()) {
			$this->sendLastModified(true);
		}
	
		return true;
	}
	
	/**
	 * Event: onBeforeSave
	 * @return boolean
	 */
	public function beforeSave()
	{
		$this->owner->{$this->attribute}=new \CDbExpression('NOW()');
		
		return true;
	}

	/**
	 * Event: onAfterUpdate
	 * @return boolean
	 */
	public function afterUpdate()
	{
		$this->owner->{$this->attribute}=new \CDbExpression('NOW()');
		$this->owner->update(array($this->attribute));
		
		return true;
	}
	
	/**
	 * Послать заголовок последней модификации страницы Last-Modified
	 * @var boolean $late отложить отправку заголовка до завершения 
	 * выполнения приложения. По умолчанию (FALSE) - не откладывать.
	 * Параметр $late будет проигнорирован, если 
	 * UpdateTimeBehavior::$autoSendLastModified=TRUE
	 */
	public function sendLastModified($late=false)
	{
		if($this->owner->hasAttribute($this->attribute) && $this->owner->{$this->attribute}) {
			if($this->isAutoSendLastModified() || $late) {
				$date=new \DateTime($this->owner->{$this->attribute});
				$time=$date->format('U');
				if(!isset(self::$lastModifieds[(string)$time])) {
					self::$lastModifieds[(string)$time]=$time;
					if(!self::$lastModifiedShutdownRegistered) {
						\Yii::app()->attachEventHandler('onEndRequest', function() {
							self::hLastModifiedShutdown(); 
						});
						self::$lastModifiedShutdownRegistered=true;
					}
				}
			}
			else {
				HRequest::sendLastModified($this->owner->{$this->attribute});
			}
		}
	}
	
	/**
	 * Обработчик отправки даты последней модификации страницы
	 * при завершении скрипта. 
	 */
	protected static function hLastModifiedShutdown()
	{
		if(count(self::$lastModifieds)) {
			$last=0;
			foreach(self::$lastModifieds as $time) {
				if($time > $last) $last=$time;
			}
			HRequest::sendLastModified($last);
		}
	}
}
