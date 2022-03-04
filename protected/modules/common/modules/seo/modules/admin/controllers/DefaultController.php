<?php
/**
 * Основной контроллер раздела администрирования модуля
 *
 */
namespace seo\modules\admin\controllers;

use common\components\helpers\HYii as Y;
use seo\modules\admin\components\BaseController;

class DefaultController extends BaseController
{
	/**
	 * (non-PHPDoc)
	 * @see BaseController::$viewPathPrefix;
	 */
	public $viewPathPrefix='seo.modules.admin.views.default.';
	
	/**
	 * Action: Главная страница.
	 */
	public function actionIndex()
	{	
		$t=Y::ct('\seo\modules\admin\AdminModule.controllers/default');
		
		$this->setPageTitle($t('page.title'));
		$this->breadcrumbs=[$t('page.title')];
		
		$this->render($this->viewPathPrefix.'index');
	}
}