<?php
/**
 * Модель
 * 
 * При режиме регионов используется поведение
 * \extend\modules\regions\behaviors\RegionAttributeBehavior
 */
namespace seo\models;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HHash;

class Seo extends \common\components\base\ActiveRecord
{
	/**
	 * @access private
	 * @var boolean режим регионов. 
	 * По умолчанию (false) выключен.
	 */
	private static $_enabledRegionsMode = false;
	
	/**
	 * Установить значение режима регионов.
	 * @param boolean $enable значение. 
	 * По умолчанию (false) выключить.
	 */
	public static function setRegionsMode($enable=false)
	{
		static::$_enabledRegionsMode = (bool)$enable;
	}
	
	/**
	 * Генерация хэша.
	 * Для прямого SQL запроса можно использовать выражение:
	 * hash=CRC32(CONCAT(model_name, "__", model_id)))
	 * @param string $modelName имя класса модели
	 * @param integer|NULL $modelId идентификатор модели. 
	 * По умолчанию (NULL) - не задан.
	 * @return string
	 */
	public static function generateHash($modelName, $modelId=null)
	{
		if(!$modelId) $modelId='0';
		if(is_object($modelName)) $modelName=get_class($modelName);
		
		return HHash::ucrc32($modelName . '__' . $modelId);
	}
	
	/**
	 * Краткий псевдоним для generateHash()
	 * @see Seo::generateHash()
	 */
	public static function gh($modelName, $modelId) 
	{
		return self::generateHash($modelName, $modelId);
	}
	 
	/**
	 * (non-PHPdoc)
	 * @see \CActiveRecord::tableName()
	 */
	public function tableName()
	{
		return 'seo_seo';
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CModel::behaviors()
	 */
	public function behaviors()
	{
		$t=Y::ct('\SeoModule.models/seo', 'extend.seo');
		$behaviors=A::m(parent::behaviors(), [
			'updateTimeBehavior'=>[
				'class'=>'\common\ext\updateTime\behaviors\UpdateTimeBehavior',
				'attributeLabel'=>$t('label.update_time'),
				'addColumn'=>false
			],
		]);
		
		if(static::$_enabledRegionsMode) {
			$behaviors['regionsBehavior']=[
				'class'=>'\extend\modules\regions\behaviors\RegionAttributeBehavior',
				'attributes'=>'model_id, model_name, h1, meta_title, meta_keywords, meta_description, link_title'
			];
		}
			
		return $behaviors;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CActiveRecord::scopes()
	 */
	public function scopes()
	{
		return $this->getScopes([
				
		]);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CActiveRecord::relations()
	 */
	public function relations()
	{
		return $this->getRelations([
				
		]);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CModel::rules()
	 */
	public function rules()
	{
		return $this->getRules([
			['hash, model_id', 'numerical', 'integerOnly'=>true],
			['model_name, h1, meta_title, meta_keywords, meta_description, link_title', 'safe']
		]);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CModel::attributeLabels()
	 */
	public function attributeLabels()
	{
		$t=Y::ct('\SeoModule.models/seo', 'extend.seo');
		return $this->getAttributeLabels([
			'hash'=>$t('label.hash'),
			'model_name'=>$t('label.model_name'),
			'model_id'=>$t('label.model_id'),
			'h1'=>$t('label.h1'),
			'meta_title'=>$t('label.meta_title'),
			'meta_keywords'=>$t('label.meta_keywords'),
			'meta_description'=>$t('label.meta_description'),
			'link_title'=>$t('label.link_title'),
		]);
	}
	
	/**
	 * Получить перегенерированный хэш модели.
	 * @return string
	 */
	public function resolveHash()
	{
		return self::generateHash($this->model_name, $this->model_id);
	}
	
	/**
	 * Обновить значение атрибута хэша модели.
	 * @return void
	 */
	public function updateHash()
	{
		$this->hash=$this->resolveHash();
	}	
}