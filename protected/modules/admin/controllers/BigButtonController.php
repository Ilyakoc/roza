<?php

class BigButtonController extends AdminController
{
	public $menu = [];
	public $breadcrumbs = [];

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new BigButton;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['BigButton']))
		{
			$model->attributes=$_POST['BigButton'];

            $model->preview = CUploadedFile::getInstance($model,'preview');

			if($model->preview){

				$newPreview = uniqid() . '.' . $model->preview->extensionName;

            	$path=Yii::getPathOfAlias('webroot').'/images/button/'.$newPreview;
            	$model->preview->saveAs($path);

            	$model->preview = $newPreview;
			}

			if($model->save())
				$this->redirect(array('index','id'=>$model->id));
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['BigButton']))
		{
			$oldPreview = $model->preview;

			$model->attributes=$_POST['BigButton'];

			$model->preview = CUploadedFile::getInstance($model,'preview');

			if($model->preview){
				$newPreview = uniqid() . '.' . $model->preview->extensionName;
            	$path=Yii::getPathOfAlias('webroot').'/images/button/'.$newPreview;
            	$model->preview->saveAs($path);
			}

			$model->preview = isset($newPreview) ? $newPreview : $oldPreview;
			
			if($model->save())
				$this->redirect(array('index','id'=>$model->id));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		$this->loadModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	/**
	 * Manages all models.
	 */
	public function actionIndex()
	{
		$model=new BigButton('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['BigButton']))
			$model->attributes=$_GET['BigButton'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return BigButton the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=BigButton::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param BigButton $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='big-button-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
