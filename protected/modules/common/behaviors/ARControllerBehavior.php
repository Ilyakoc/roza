<?php
namespace common\behaviors;

use common\components\helpers\HYii as Y;
use common\components\helpers\HDb;
use common\components\helpers\HModel;
use common\components\helpers\HHook;
use common\components\helpers\HAjax;

class ARControllerBehavior extends \CBehavior
{
	/**
	 * Сохранение модели
	 * @param \CActiveRecord $model модель
	 * @param array $attributes атрибуты, значения которых будут возвращены при ajax запросе.
	 * @param string $formId id формы (для ajax валидации).
	 * @param array $handlers массив обработчиков (name=>function).
	 * Доступны следующие обработчики:
	 * "beforeSave" обработчик который будет вызван до сохранения модели. По умолчанию действия произведено не будет.
	 * "save" обработчик который будет вызван для сохранения модели. По умолчанию будет вызван $model->save().
	 * "afterSave" будет вызван после сохранения (если запрос не является ajax-запросом). По умолчанию будет вызвран метод refresh().
	 * @return boolean
	 */
	protected function save($model, $attributes=[], $formId=null, $handlers=null)
	{
		if(HModel::isFormRequest($model)) {
			$model=HDb::massiveAssignment($model);
	
			HHook::exec($handlers, 'beforeSave', [$model]);
			$hSave=HHook::get($handlers, 'save', function() use ($model) { return $model->save(); });
			if(Y::request()->isAjaxRequest) {
				HModel::performAjaxValidation($model, $formId);
	
				if($success=HHook::hexec($hSave, [$model])) {
					HHook::exec($handlers, 'afterSave', [$model]);
				}
				HAjax::end(
					$success, 
					['isNewRecord'=>$model->isNewRecord, 'attributes'=>$model->getAttributes($attributes)], 
					$model->getErrors()
				);
			}
			elseif(HHook::hexec($hSave, [$model])) {
				HHook::exec($handlers, 'afterSave', [$model], function() { $this->refresh(); });
			}
		}
	
		return false;
	}
	
	/**
	 * Загрузка модели
	 * Для поддержки старый версий.
	 * @see \common\components\helpers\HModel::loadByPk()
	 */
	public function loadModel($className, $id, $exception=true, $criteria=null)
	{
		return HModel::loadByPk($className, $id, $exception, $criteria);
	}
} 