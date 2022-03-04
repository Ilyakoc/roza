<?php
/**
 * Поведение атрибута активности.
 *
 */
namespace common\ext\active\behaviors;

use common\components\helpers\HYii as Y;
use common\components\helpers\HDb;

class ActiveBehavior extends \CBehavior
{
	/**
	 * @var boolean добавить автоматически в таблицу базы данных поле для хранения данных
	 * данного атрибута. По умолчанию (TRUE) добавить.
	 */
	use \common\traits\models\AddColumn;
	
	/**
	 * @var string имя атрибута
	 * По умолчанию "active".
	 */
	public $attribute = 'active';
	
	/**
	 * @var string имя атрибута идентификатора модели.
	 * По умолчанию "id".
	 */
	public $attributeId='id';
	
	/**
	 * @var string название атрибута.
	 * По умолчанию (NULL) из заданных в данном расширении.
	 */
	public $attributeLabel=null;
	
	/**
	 * @var string имя условия выборки (scope) активных моделей.
	 * По умолчанию "actived". 
	 * Используется только для поддержки старых версий поведения.
	 */
	public $scopeActivedName='actived';
	
	/**
	 * @var string имя условия выборки (scope) активных моделей.
	 * По умолчанию "activly"
	 */
	public $scopeActivlyName='activly';
	
	/**
	 * @var string имя условия выборки (scope) не активных моделей.
	 * По умолчанию "notActivly"
	 */
	public $scopeNotActivlyName='notActivly';
	
	/**
	 * @var array|NULL условия выборки.
	 */
	private $_scopes=null;
	
	/**
	 * (non-PHPdoc)
	 * @see CBehavior::events()
	 */
	public function events()
	{
		return array(
			'onBeforeValidate'=>'beforeValidate'
		);
	}
	
	/**
	 * (non-PHPDoc)
	 * @see CBehavior::attach($owner)
	 */
	public function attach($owner)
	{
		parent::attach($owner);
	
		if($this->attributeLabel === null) {
			$t=Y::ct('\common\ext\active\Messages.common', 'common');
			$this->attributeLabel=$t('default.label');
		}
		
		$this->addColumn($this->owner->tableName(), $this->attribute, 'boolean');
	}
	
	/**
     * (non-PHPdoc)
     * @see CActiveRecord::scopes()
     */
	public function scopes() 
	{
		if($this->_scopes === null) {
			$this->_scopes=[];
			
			foreach(['scopeActivedName'=>'=1', 'scopeActivlyName'=>'=1', 'scopeNotActivlyName'=>'<>1 OR ISNULL('.$this->attribute.')'] as $prop=>$expr) {
				if($this->$prop) $this->_scopes[$this->$prop]=['condition'=>$this->attribute.$expr];
			}
		}
		
		return $this->_scopes;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CModel::rules()
	 */
	public function rules()
	{
		return array(
			array($this->attribute, 'boolean')
		);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CModel::attributeLabels()
	 */
	public function attributeLabels()
	{
		return array(
			$this->attribute=>$this->attributeLabel
		);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CActiveRecordBehavior::beforeValidate()
	 */
	public function beforeValidate()
	{
		$this->owner->{$this->attribute}=(int)$this->owner->{$this->attribute} ? 1 : 0;
		return true;
	}
	
	/**
	 * Проверяет значение атрибута на активность. 
	 * @return boolean активен. TRUE(активен)/FALSE(неактивен)
	 */
	public function isActive()
	{
		return ((int)$this->owner->{$this->attribute} == 1);
	}
	
	/**
	 * Сменить активность 
	 * @param boolean $update сохранить изменения или нет. По умолчанию FALSE(не сохранять).
	 * @return boolean 
	 */
	public function changeActive($update=false)
	{
		$this->owner->{$this->attribute} = (int)$this->owner->{$this->attribute} ? 0 : 1;
		
		if($update) {
			$owner=$this->owner;
			$owner->{$this->attribute}=new \CDbExpression('IF('.HDb::qc($this->attribute).'=1, 0, 1)');
			$owner->update(array($this->attribute));
			
			$owner->raiseEvent('onAfterUpdate', new \CModelEvent($this->owner, ['behavior'=>$this]));
		}
			
		return true;	
	}
}