<?php
/**
 * Основной контроллер раздела администрирования модуля
 *
 */
namespace crud\modules\admin\controllers;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use crud\modules\admin\components\BaseController;
use crud\components\helpers\HCrud;
use crud\components\helpers\HCrudForm;

class DefaultController extends BaseController
{
	/**
	 * (non-PHPDoc)
	 * @see BaseController::$viewPathPrefix;
	 */
	public $viewPathPrefix='crud.modules.admin.views.default.';
	
	/**
	 * (non-PHPdoc)
	 * @see \CController::actions()
	 */
	public function actions()
	{
		return A::m(parent::actions(), [
			'changeActive'=>[
				'class'=>'\common\ext\active\actions\AjaxChangeActive',
				'className'=>HCrud::param(Y::requestGet('cid'), 'class'),
				'behaviorName'=>Y::requestGet('b', 'activeBehavior')
			],				
			'removeImage'=>[
				'class'=>'\common\ext\file\actions\RemoveFileAction',
				'modelName'=>HCrud::param(Y::requestGet('cid'), 'class'),
				'behaviorName'=>Y::requestGet('b', 'imageBehavior'),
				'ajaxMode'=>true
			],
			'sortableSave'=>[
				'class'=>'\common\ext\sort\actions\SaveAction',
				'categories'=>[Y::requestGet('category')],
			]
		]);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see AdminController::filters()
	 */
	public function filters()
	{
		return A::m(parent::filters(), [
			'ajaxOnly +changeActive, removeImage'	
		]);
	}
	
	/**
	 * Action: Главная страница.
	 * @param string $cid индетификатор настроек CRUD для модели.
	 */
	public function actionIndex($cid)
	{	
		if($onBeforeLoad=HCrud::param($cid, 'crud.index.onBeforeLoad')) $onBeforeLoad();
		 
		$model=HCrud::getById($cid, true, null, true, HCrud::param($cid, 'crud.index.scenario', 'view'));
		
		$options=HCrud::param($cid, 'crud.index.gridView.dataProvider', []);
		$dataProviderOptions=A::m([
			'pagination'=>[
				'pageVar'=>'p',	
				'pageSize'=>Y::request()->getQuery('gridSize')?:30
			], 
			'sort'=>[
				'sortVar'=>'s', 
				'descTag'=>'d'
			]
		], $options);
		
		$sort=Y::requestGet(A::rget($dataProviderOptions, 'sort.sortVar'));
		if(Y::requestGet('usort')) {
			$sort='usort';
		}
		elseif(Y::requestGet('usortd')) {
			$sort='usortd';
		}
		elseif(!$sort) {
			$sort='usort'; 
		}
		
		if((($sort == 'usort') || ($sort == 'usortd'))
			&& ($sortable=HCrud::param($cid, 'crud.index.gridView.sortable')) 
			&& ($sortableCategory=A::get($sortable, 'category'))) 
		{
			$model=$model->scopeSort($sortableCategory, A::get($sortable, 'key'), ((int)Y::requestGet('usortd')===1));
		}
		$dataProvider=$model->getDataProvider($dataProviderOptions);
		
		if(Y::isAjaxRequest()) {
			$this->renderPartial($this->viewPathPrefix.'_gridview', compact('cid', 'dataProvider'), false, true);
		}
		else {
			$t=Y::ct('\crud\modules\admin\AdminModule.controllers/default');
			$title=HCrud::param($cid, 'crud.index.title', $t('page.index.title'));
			
			$this->setPageTitle(HCrud::param($cid, 'crud.index.titleBreadcrumb', $title));
			$this->breadcrumbs=HCrud::param($cid, 'crud.index.breadcrumbs', HCrud::param($cid, 'crud.breadcrumbs', [])); 
			$this->breadcrumbs[]=HCrud::param($cid, 'crud.index.titleBreadcrumb', $title);
			
			$this->render($this->viewPathPrefix.'index', compact('cid', 'dataProvider'));
		}
	}
	
	/**
	 * Action: Создание модели.
	 * @param string $cid индетификатор настроек CRUD для модели.
	 */
	public function actionCreate($cid)
	{	
		if($onBeforeLoad=HCrud::param($cid, 'crud.create.onBeforeLoad')) $onBeforeLoad();
		
		$model=HCrud::getById($cid, null, null, true, HCrud::param($cid, 'crud.create.scenario', 'insert'));		
		$formView=$this->getFormView($cid);
		
		$formProperties=HCrudForm::getFormProperties($cid, 'crud.create');
		$this->save($model, [], A::get($formProperties, 'id', 'crud-form'), [
			'afterSave'=>function() use ($cid, $model) {
				$t=Y::ct('\crud\modules\admin\AdminModule.controllers/default');
				if(isset($_POST['saveout'])) {
					Y::setFlash(Y::FLASH_SYSTEM_SUCCESS, $t('success.created', ['{id}'=>$model->id]));
					$this->redirect(HCrud::getConfigUrl($cid, 'crud.index.url', '/crud/admin/default/index', ['cid'=>$cid], 'c'));
				}
				else {
					$this->redirect(HCrud::getConfigUrl($cid, 'crud.update.url', '/crud/admin/default/update', ['cid'=>$cid, 'id'=>$model->id], 'c'));
				}
			}]
		);
		
		$t=Y::ct('\crud\modules\admin\AdminModule.controllers/default');
		$title=HCrud::param($cid, 'crud.create.title', $t('page.create.title'));
		$this->setPageTitle(HCrud::param($cid, 'crud.create.titleBreadcrumb', $title));
		
		$this->breadcrumbs=HCrud::param($cid, 'crud.create.breadcrumbs', HCrud::param($cid, 'crud.breadcrumbs', []));
		$indexBreadcrumb=HCrud::param($cid, 'crud.index.titleBreadcrumb', HCrud::param($cid, 'crud.index.title'));
		$this->breadcrumbs[$indexBreadcrumb]=HCrud::getConfigUrl($cid, 'crud.index.url', '/crud/admin/default/index', ['cid'=>$cid], 'c');
		$this->breadcrumbs[]=HCrud::param($cid, 'crud.create.titleBreadcrumb', $title);
		
		$this->render($this->viewPathPrefix.'create', compact('cid', 'model', 'formView'));
	}
	
	/**
	 * Action: Редатирование модели.
	 * @param string $cid индетификатор настроек CRUD для модели.
	 * @param integer $id индетификатор модели
	 */
	public function actionUpdate($cid, $id)
	{
		if($onBeforeLoad=HCrud::param($cid, 'crud.update.onBeforeLoad')) $onBeforeLoad();
		
		$model=HCrud::getById($cid, $id, null, true, HCrud::param($cid, 'crud.index.scenario', 'update'));
		$formView=$this->getFormView($cid);
		
		$formProperties=HCrudForm::getFormProperties($cid, 'crud.update');
		$this->save($model, [], A::get($formProperties, 'id', 'crud-form'), [
			'afterSave'=>function() use ($cid, $model) {
				$t=Y::ct('\crud\modules\admin\AdminModule.controllers/default');
				if(isset($_POST['saveout'])) {
					Y::setFlash(Y::FLASH_SYSTEM_SUCCESS, $t('success.updated', ['{id}'=>$model->id]));
					$this->redirect(HCrud::getConfigUrl($cid, 'crud.index.url', '/crud/admin/default/index', ['cid'=>$cid], 'c'));
				}
				else {
					$this->redirect(HCrud::getConfigUrl($cid, 'crud.update.url', '/crud/admin/default/update', ['cid'=>$cid, 'id'=>$model->id], 'c'));
				}
			}]
		);
		
		$t=Y::ct('\crud\modules\admin\AdminModule.controllers/default');
		$title=HCrud::param($cid, 'crud.update.title', $t('page.update.title'));
		$this->setPageTitle(HCrud::param($cid, 'crud.update.titleBreadcrumb', $title));
		
		$this->breadcrumbs=HCrud::param($cid, 'crud.update.breadcrumbs', HCrud::param($cid, 'crud.breadcrumbs', []));
		$indexBreadcrumb=HCrud::param($cid, 'crud.index.titleBreadcrumb', HCrud::param($cid, 'crud.index.title'));
		$this->breadcrumbs[$indexBreadcrumb]=HCrud::getConfigUrl($cid, 'crud.index.url', '/crud/admin/default/index', ['cid'=>$cid], 'c');
		$this->breadcrumbs[]=HCrud::param($cid, 'crud.update.titleBreadcrumb', $title);
		
		$this->render($this->viewPathPrefix.'update', compact('cid', 'model', 'formView'));
	}
	
	/**
	 * Action: Удаление модели.
	 * @param string $cid индетификатор настроек CRUD для модели.
	 * @param integer $id индетификатор модели
	 */
	public function actionDelete($cid, $id)
	{
		if($onBeforeLoad=HCrud::param($cid, 'crud.delete.onBeforeLoad')) $onBeforeLoad();
		
		$model=HCrud::getById($cid, $id, null, true, HCrud::param($cid, 'crud.index.scenario', 'delete'));
		
		$model->delete();
		
		if(Y::request()->isAjaxRequest) {
			Y::end();
		}
		
		$this->redirect([HCrud::param($cid, 'crud.index.url', '/crud/admin/default/index'), 'cid'=>$cid]);
	}
	
	/**
	 * Получить имя шаблона формы
	 * @param string $cid идентификатор конфигурации CRUD для модели.
	 * @throws string|\CHttpException
	 */
	protected function getFormView($cid)
	{
		if($tabsConfig=HCrud::param($cid, 'crud.tabs')) {
			return A::get($tabsConfig, 'view', $this->viewPathPrefix.'_tabs');
		}
		elseif($formConfig=HCrud::param($cid, 'crud.form')) {
			return A::get($formConfig, 'view', $this->viewPathPrefix.'_form');
		}
		else {
			throw new \CHttpException(404);
		}
	}
}