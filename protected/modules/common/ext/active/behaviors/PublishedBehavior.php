<?php
/**
 * Поведение атрибута опубликованности.
 *
 */
namespace common\ext\active\behaviors;

use common\components\helpers\HYii as Y;

class PublishedBehavior extends ActiveBehavior
{
	/**
	 * @var string имя атрибута
	 * По умолчанию "published".
	 */
	public $attribute = 'published';
	
	/**
	 * @var string название атрибута.
	 * По умолчанию (NULL) из заданных в данном расширении.
	 */
	public $attributeLabel=null;
	
	/**
	 * @var string имя условия выборки (scope) активных моделей.
	 * По умолчанию "published".
	 * Используется только для поддержки старых версий поведения.
	 */
	public $scopeActivedName=false;
	
	/**
	 * @var string имя условия выборки (scope) активных моделей.
	 * По умолчанию "published"
	 */
	public $scopeActivlyName='published';
	
	/**
	 * @var string имя условия выборки (scope) не активных моделей.
	 * По умолчанию "unpublished"
	 */
	public $scopeNotActivlyName='unpublished';
	
	/**
	 * (non-PHPDoc)
	 * @see ActiveBehavior::attach($owner)
	 */
	public function attach($owner)
	{
		if($this->attributeLabel === null) {
			$t=Y::ct('\common\ext\active\Messages.common', 'common');
			$this->attributeLabel=$t('label.published');
		}
		
		parent::attach($owner);
	}
}