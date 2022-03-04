<?php
namespace common\components\base;

abstract class FormModel extends \CFormModel
{
	use \common\traits\Model;
	use \common\traits\Singleton;

    /**
     * Returns the static model
     */
    public static function model()
    {
        return static::getInstance();
    }
	
	public function behaviors()
	{
		return [
			'arFormModelBehavior'=>'\common\behaviors\ARFormModelBehavior'
		];
	}
	
	public function onBeforeSave($event)
	{
		$this->owner->raiseEvent('onBeforeSave', $event);
	}
	
	public function onAfterSave($event)
	{
		$this->owner->raiseEvent('onAfterSave', $event);
	}
	
	public function onBeforeDetele($event)
	{
		$this->owner->raiseEvent('onBeforeDetele', $event);
	}
	
	public function onAfterDelete($event)
	{
		$this->owner->raiseEvent('onAfterDelete', $event);
	}
}