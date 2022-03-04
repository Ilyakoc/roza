<?php
/**
 * Основной контроллер раздела администрирования модуля "Настройки".
 *
 */
namespace settings\modules\admin\controllers;

use common\components\helpers\HYii as Y;
use settings\modules\admin\components\BaseController;

class DefaultController extends BaseController
{
	/**
	 * (non-PHPDoc)
	 * @see BaseController::$viewPathPrefix;
	 */
	public $viewPathPrefix='settings.modules.admin.views.default.';
	
	/**
	 * Действие главной страницы.
	 * @param string $id идентификатор настроек. 
	 */
	public function actionIndex($id)
	{	
		$model=$this->loadConfigModel($id);
		
		$t=Y::ct('\settings\modules\admin\AdminModule.controllers/default');
		
		$this->save($model, [], null, ['afterSave'=>function() use ($t) {
			Y::setFlash(Y::FLASH_SYSTEM_SUCCESS, $t('success.updated'));
			return true; 
		}]);
		
		$title=$model->getConfigParam('title', $t('page.title'));
		$this->setPageTitle($title);
		$this->breadcrumbs=$model->getConfigParam('breadcrumbs', []);
		$this->breadcrumbs[]=$title;
		
		$this->render($this->viewPathPrefix.'index', compact('model'));
	}
}