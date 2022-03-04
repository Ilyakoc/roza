<?php

class OfferController extends AdminController
{

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model = new Offer;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if (isset($_POST['Offer'])) {
			$model->attributes = $_POST['Offer'];

			if ($model->save()) {
                if(isset($_POST['save_out'])) {
                    $this->redirect(['/admin/shop/productUpdate', 'id' => $model->product_id]);
                } else {
                    $this->redirect(['update', 'id' => $model->id]);
                }
            }
		}

		$this->render('create', array(
			'model' => $model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model = $this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if (isset($_POST['Offer'])) {
			$model->attributes = $_POST['Offer'];

			if ($model->save()) {
                if(isset($_POST['save_out'])) {
                    $this->redirect(['/admin/shop/productUpdate', 'id' => $model->product_id]);
                } else {
                    $this->redirect(['update', 'id' => $model->id]);
                }
            }
		}

		$this->render('update', array(
			'model' => $model,
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
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$model=new Offer('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Offer']))
			$model->attributes=$_GET['Offer'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	public function loadModel($id)
	{
		$model=Offer::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Offer $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='offer-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
