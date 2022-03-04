<?php
/**
 * Настройки областей применения. 
 *
 */
use common\components\helpers\HArray as A;

class RangeofSettings extends \settings\components\base\SettingsModel 
{
	/**
	 * @var string элементы. 
	 */ 
	public $items;

	/**
	 * @var boolean для совместимости со старым виджетом 
	 * редактора admin.widget.EditWidget.TinyMCE
	 */
	public $isNewRecord=false;
	
	/**
	 * Для совместимости со старым виджетом 
	 * редактора admin.widget.EditWidget.TinyMCE
	 */
	public function tableName()
	{
		return 'rangeof_settings';
	}
		
	/**
	 * (non-PHPdoc)
	 * @see \settings\components\base\SettingsModel::behaviors()
	 */
	public function behaviors()
	{
		return A::m(parent::behaviors(), [
			'itemsBehavior'=>[
				'class'=>'\common\ext\dataAttribute\behaviors\DataAttributeBehavior',
				'attribute'=>'items',
				'attributeLabel'=>'Области применения',
				'addColumn'=>false
			]
		]);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \settings\components\base\SettingsModel::rules()
	 */
	public function rules()
	{
		return $this->getRules([
		]);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \settings\components\base\SettingsModel::attributeLabels()
	 */
	public function attributeLabels()
	{
		return $this->getAttributeLabels([
		]);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \settings\components\base\SettingsModel::save()
	 */
	public function save()
	{	
		$itemModelName=\CHtml::modelName('\RangeofItemSettings');
		if(isset($_POST[$itemModelName])) {
			$items=[];
			foreach($_POST[$itemModelName] as $id=>$attributes) {
				$item=new \RangeofItemSettings;
				$item->attributes=$attributes;
				$item->imageBehavior->attributeFile=$id;
				$item->imageBehavior->multiFiles=true;
				$item->save();
				$items[]=['active'=>true, 'item'=>$item];
			}
			$this->items=$items;
		}
		
		return parent::save();
	}
}