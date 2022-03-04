<?php
/**
 * Виджет Nestable для модели с поведением \NestedSetBehavior
 * Сделано для hasManyRoots=TRUE и необходимо дополнительное поле у модели "ordering".
 * Виджет редактирования вложенной структуры
 * @see https://github.com/dbushell/Nestable
 * @see https://github.com/yiiext/nested-set-behavior/blob/master/readme_ru.md#%D0%9F%D0%BE%D0%BB%D0%B5%D0%B7%D0%BD%D1%8B%D0%B9-%D0%BA%D0%BE%D0%B4
 */
namespace common\ext\nestedset\widgets;

class NestedSet extends BaseNestable
{
	/**
	 * @var string|\CActiveRecord модель или имя класса модели с поведением NestedSetBehavior
	 * Если имя класса или объект модели передан, то на основании него
	 * будет перезаписан BaseNestable::$dataProvider.
	 * @see https://github.com/yiiext/nested-set-behavior
	 */
	public $model;
	
	/**
	 * @var mixed объект дополнительного критерия (\CDbCriteria).
	 * Учитывается, если задан параметр NestedSet::$model. 
	 * По умолчанию NULL.
	 */
	public $criteria=null;
	
	/**
	 * @var string имя атрибута корневого элемента NestedSet модели.
	 */
	public $attributeRoot='root';
	
	/**
	 * @var string имя атрибута левого элемента NestedSet модели.
	 */
	public $attributeLeft='lft';
	
	/**
	 * @var boolean использовать скин dd3
	 */
	public $skinDd3=false;
	
	/**
	 * (non-PHPdoc)
	 * @see \common\widgets\nestable\BaseNestable::init()
	 */
	public function init()
	{
		if(!isset($this->htmlOptions['id'])) 
			$this->htmlOptions['id']=$this->id;
		
		if(!isset($this->htmlOptions['class'])) {
			$this->htmlOptions['class']='dd';
		}
		elseif(!preg_match('/\bdd\b/i', $this->htmlOptions['class'])) {
			$this->htmlOptions['class'].=' dd';
		}
		
		parent::init();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CWidget::run()
	 */
	public function run()
	{
		if(!empty($this->model)) {
			$this->dataProvider=new \CActiveDataProvider($this->model, [
				'criteria'=>$this->criteria,
				'pagination'=>false,
				'sort'=>[
					'defaultOrder'=>\ARHelper::dbQC($this->attributeRoot) . ',' . \ARHelper::dbQC($this->attributeLeft)		
				]
			]);
		}
		elseif(!($this->dataProvider instanceof \IDataProvider)) {
			\Yii::trace('$dataProvider not instanceof \IDataProvider.');
			return false;
		}
		
		$this->render('common.ext.nestedset.widgets.views.' . ($this->skinDd3 ? 'dd3' : 'default'));
	}
}