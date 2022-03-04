<?php
/**
 * Действие удаления картинки
 *
 */
namespace common\ext\file\actions;

class RemoveFileAction extends \CAction
{
	/**
	 * @var string имя класса модели.
	 */
	public $modelName;
	
	/**
	 * @var string имя поведения \common\ext\file\behaviors\FileBehavior;
	 * По умолчанию "fileBehavior" 
	 */
	public $behaviorName='fileBehavior';
	
	/**
	 * @var boolean действие вызывается через ajax-запрос. По умолчанию (FALSE) нет. 
	 */
	public $ajaxMode=false;
	
	/**
	 * @var string URL перенаправления после удаления. Используется при режиме RemoveFileAction::$ajax=FALSE. 
	 * По умолчаню array('index').
	 */
	public $redirectUrl=['index'];
	
	/**
	 * Run
	 * @param integer $id id модели. Не обязательный, если в поведении не задан атрибут id модели.
	 */
	public function run($id=null)
	{
		$modelName=$this->modelName;
		$model=$modelName::model();
		
		if($model->{$this->behaviorName}->attributeId) {
			if(empty($id)) {
				throw new \CHttpException(404);
			}
			$model->{$model->{$this->behaviorName}->attributeId}=$id;
		}
		$model->{$this->behaviorName}->delete(true);
		
		if($this->ajaxMode) {
			\Yii::app()->end();
			die;
		}
		else {
			$this->getController()->redirect($this->redirectUrl);
		}
	}
}