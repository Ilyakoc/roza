<?php
/**
 * Контроллер
 *
 */
namespace crud\controllers;

use common\components\helpers\HArray as A;
use common\components\helpers\HYii as Y;
use crud\components\BaseController;

class DefaultController extends BaseController
{
	/**
	 * (non-PHPDoc)
	 * @see BaseController::$viewPathPrefix;
	 */
	public $viewPathPrefix='crud.views.default.';
	
	/**
	 * (non-PHPdoc)
	 * @see \CController::filters()
	 */
	public function filters()
	{
		return A::m(parent::filters(), [
		]);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \CController::actions()
	 */
	public function actions()
	{
		return A::m(parent::actions(), [
		]);
	}
	
	/**
	 * Action: Главная страница
	 * @param string $cid индетификатор настроек CRUD для модели.
	 */
	public function actionIndex($cid)
	{
		if($onBeforeLoad=HCrud::param($cid, 'public.index.onBeforeLoad')) $onBeforeLoad();
			
		$model=HCrud::getById($cid, true);
		
		$options=HCrud::param($cid, 'public.index.listView.dataProvider', []);
		$dataProvider=$model->getDataProvider(A::m([
			'pagination'=>[
				'pageVar'=>'p',
				'pageSize'=>Y::request()->getQuery('sz')?:30
			],
			'sort'=>[
				'sortVar'=>'s',
				'descTag'=>'d'
			]
		], $options));
		
		if(Y::isAjaxRequest()) {
			$this->renderPartial($this->viewPathPrefix.'_listview', compact('cid', 'dataProvider'), false, true);
		}
		else {
			$t=Y::ct('\CrudModule.controllers/default');
			$title=HCrud::param($cid, 'public.index.title', $t('page.index.title'));
				
			$this->setPageTitle(HCrud::param($cid, 'public.index.titleBreadcrumb', $title));
			$this->breadcrumbs=HCrud::param($cid, 'public.index.breadcrumbs', HCrud::param($cid, 'public.breadcrumbs', []));
			$this->breadcrumbs[]=HCrud::param($cid, 'public.index.titleBreadcrumb', $title);
				
			$this->render($this->viewPathPrefix.'index', compact('cid', 'dataProvider'));
		}
	}
	
	/**
	 * Action: Детальная страница
	 * @param string $cid индетификатор настроек CRUD для модели.
	 * @param integer $id индетификатор модели
	 */
	public function actionView($cid, $id)
	{
		if($onBeforeLoad=HCrud::param($cid, 'public.view.onBeforeLoad')) $onBeforeLoad();
		
		$model=HCrud::getById($cid, $id);
		
		$this->render('view', compact('model'));
	}
	
	/**
	 * Action: Получить основной заголовок
	 * @return string
	 */
	public function getHomeTitle()
	{
		return \Yii::t('CrudModule.controllers/default', 'title');
	}
}
