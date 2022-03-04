<?php
/**
 * Поведение для \CFormModel имитирующее поведение \CActiveRecord 
 *
 * 
 */
namespace common\behaviors;

class ARFormModelBehavior extends \CActiveRecordBehavior
{
	/**
	 * (non-PHPDoc)
	 * @see \CActiveRecord::save()
	 */
	public function save($runValidation=true, $attributes=null) 
	{
		if(!$runValidation || $this->owner->validate($attributes))
			$event=new \CModelEvent($this->owner);
			$this->owner->raiseEvent('onBeforeSave', $event);
			if($event->isValid) {
				$result=$this->owner->update($attributes);
				if($result !== false) {
					$this->owner->raiseEvent('onAfterSave', $event);
					return $event->isValid;
				}
			}
		else
			return false;
	}
	
	/**
	 * Update attibutes
	 * @param array $attributes attributes for update as array(attribute=>value).
	 */
	public function update($attributes=[])
	{	
		return true;
	}
	
	/**
	 * (non-PHPDoc)
	 * @see \CActiveRecord::saveAttributes()
	 */
	public function saveAttributes($attributes) 
	{
		return $this->owner->update($attributes);
	}
	
	/**
	 * Delete
	 */
	public function delete()
	{
		$event=new \CModelEvent($this->owner);
		$this->beforeDelete($event);
		$this->afterDelete($event);
	}
}