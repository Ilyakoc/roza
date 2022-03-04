<?php
/**
 * Действие смены активности
 *
 */
namespace common\ext\active\actions;

use common\components\helpers\HAjax;
use common\components\helpers\HHook;

class AjaxChangeActive extends \CAction
{
    public $className;
    public $behaviorName='activeBehavior';
    public $scenario='insert';
    
    public $onBeforeSave;
    
    public function run($id)
    {
    	$ajax=HAjax::start();
    	
    	$className=$this->className;
        $model=new $className($this->scenario);
		if($model instanceof \CActiveRecord) {
        	if(!($model=$model->findByPk($id))) {
        		$ajax->end();
        	}
        }
		$model->id=$id;
		       
		if(HHook::hexec($this->onBeforeSave, [&$model], true)) {
			$ajax->success=$model->{$this->behaviorName}->changeActive(true);
		}
		$ajax->addErrors($model->getErrors());
		
		$ajax->end();
    }
}