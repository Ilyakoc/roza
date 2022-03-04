<?php
/**
 * Поведение SEO
 */
namespace seo\behaviors;

use seo\models\Seo;

class SeoBehavior extends \CBehavior
{
	/**
	 * @var string имя атрибута идентификатора модели
	 */
	public $attributeId='id';
	
	/**
	 * @var string имя атрибута модели для значения H1 по умолчанию
	 */
	public $attributeH1Default='title';
	
	/**
	 * @var string имя атрибута модели для значения 
	 * заголовка браузера (TITLE) по умолчанию.
	 */
	public $attributeMetaTitleDefault='title';
	
	/**
	 * @var boolean активировать режим регионов.
	 * По умолчанию (false) режим регионов выключен. 
	 */
	public $enableRegionsMode = false;
	
	/**
	 * @var array seo-атрибуты поведения. Массив вида:
	 * array(attribute=>seo-attribute), где 
	 * "attribute" - имя атрибута поведения
	 * "seo-attribute" - имя атрибута связи "seo"
	 */
	protected $attributes = [
		'seo_h1'=>'h1', 
		'seo_meta_title'=>'meta_title',
		'seo_meta_keywords'=>'meta_keywords',
		'seo_meta_desc'=>'meta_description',
		'seo_link_title'=>'link_title'
	];
	
	/**
	 * @var NULL|0|\seo\models\Seo seо модель
	 * 0(нуль) - переменная не инициализирована.
	 */
	protected $_seo = false;
	
	/**
	 * (non-PHPdoc)
	 * @see \common\traits\Model::__get()
	 */
	public function __handlerGet($name)
	{
		return function ($name) {
			if(isset($this->attributes[$name])) {
				if($seo=$this->getSeo()) {
					return $seo->{$this->attributes[$name]};
				}
				return null;
			}
			throw new \common\components\exceptions\PropertyNotFound;
		};
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \common\traits\Model::__set()
	 */
	public function __handlerSet($name, $value)
	{
		return function($name, $value) {
			if(isset($this->attributes[$name])) {
				if($this->getSeo()) {
					$this->_seo->{$this->attributes[$name]}=$value;
				}
			}
			else {
				throw new \common\components\exceptions\PropertyNotFound;
			}
		};
	}
	
	/**
	 * {@inheritDoc}
	 * @see CBehavior::attach()
	 */
	public function attach($owner)
	{
		parent::attach($owner);
		
		if($this->enableRegionsMode) {
			Seo::setRegionsMode(true);
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \CBehavior::events()
	 */
	public function events()
	{
		return [
			'onAfterSave'=>'afterSave',
		];
	}
	
	/**
	 * Model relations.
	 * @return array
	 */
	public function relations()
	{
		if($this->owner->isAttaching()) {
			$hash=false;
		}
		else {
			$hash=Seo::gh(get_class($this->owner), $this->owner->{$this->attributeId});
		}
		
		return [
			'seo'=>[
				\CActiveRecord::HAS_ONE, 
				'\seo\models\Seo',
				'', 
				'condition'=>'hash=:hash',
				'params'=>[':hash'=>$hash]
			]
		];
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \CModel::rules()
	 */
	public function rules()
	{
		return[
			[implode(',', array_keys($this->attributes)), 'safe']
		];
	}
	
	/**
	 * Model attribute labels.
	 * @return array
	 */
	public function attributeLabels()
	{
		$seoAttributeLabels=Seo::model()->attributeLabels();
		return array_map(function($seoAttribute) use ($seoAttributeLabels) { 
			return $seoAttributeLabels[$seoAttribute]; 
		}, $this->attributes);
	}
	
	/**
	 * Получить сео модель.
	 * @param string $refresh обновить данные.
	 * @return \seo\models\Seo
	 */
	public function getSeo($refresh=false)
	{
		if($refresh || ($this->_seo === false)) {
			$this->_seo=$this->owner->seo([
				'params'=>['hash'=>Seo::gh($this->owner, $this->owner->{$this->attributeId})]
			]);
		}
		
		if(!($this->_seo instanceof Seo)) {
			$this->_seo=new Seo;
		}
		
		return $this->_seo;
	}
	
	/**
	 * Получить значение заголовока H1
	 * @return string
	 */
	public function getSeoH1()
	{
		if($this->getSeo()->h1) {
			return $this->getSeo()->h1;
		}
		return $this->owner->{$this->attributeH1Default}; 
	}
	
	/**
	 * Получить значение заголовока браузера
	 * @return string
	 */
	public function getSeoMetaTitle()
	{
		if($this->getSeo()->meta_title) {
			return $this->getSeo()->meta_title;
		}
		return $this->owner->{$this->attributeMetaTitleDefault}; 
	}
	
	/**
	 * Получить значение ключевых слов
	 * @return string
	 */
	public function getSeoMetaKeywords()
	{
		return $this->getSeo()->meta_keywords;
	}
	
	/**
	 * Получить значение ключевых слов
	 * @return string
	 */
	public function getSeoMetaDesc()
	{
		return $this->getSeo()->meta_description;
	}
	
	/**
	 * Event: onAfterSave
	 */
	public function afterSave()
	{
		$seo=$this->getSeo();
		
		$seo->model_name=get_class($this->owner);
		$seo->model_id=$this->owner->{$this->attributeId};
		$seo->updateHash();
		
		return $seo->save();
	}
}