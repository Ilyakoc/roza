<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Rick
 * Date: 05.03.12
 * Time: 10:27
 * To change this template use File | Settings | File Templates.
 */
class FeedbackController extends AdminController
{

   public function actionAjax() {
     $action = Yii::app()->request->getPost('action');
     $item = Yii::app()->request->getPost('item');
     $return = array();
     if($action !== null && $item !== null) {

	     if ($action == 'published') {
		     $model = Feedback::model()->findByPk((int)$item);
		     $model->published = (int)!(bool)$model->published;
		     $model->save();
		     $return = array("status" => $model->published, "count" => coreHelper::getNotifies('Feedback'));
	     }

         if ($action == 'question') {
             $model = Feedback::model()->findByPk((int)$item);
             $model->question = CHtml::encode(Yii::app()->request->getPost('text', ""));
             $model->save();
             $return = array("text" => $model->question);
         }

         if($action == 'answer') {
             $model = Feedback::model()->findByPk((int)$item);
             $model->answer = CHtml::encode(Yii::app()->request->getPost('text', ""));
             $model->save();
             $return = array("text" => $model->answer, "count" => coreHelper::getNotifies('Feedback'));   
         }
     } else {
         $return = array("status" => "request not valid");
     }
     echo CJSON::encode($return);
   }

    /**
   	 * Lists all models.
   	 */
    public function actionIndex()
    {
        $list = Feedback::model()->findAll(array('order'=>'created DESC'));

        $this->pageTitle = 'Отзывы - '. $this->appName;
        $this->render('index', compact('list'));
    }

   	/**
   	 * Creates a new model.
   	 * If creation is successful, the browser will be redirected to the 'view' page.
   	 */
   	public function actionCreate()
   	{
   		$model = new Feedback();

   		// Uncomment the following line if AJAX validation is needed
   		// $this->performAjaxValidation($model);

   		if (isset($_POST['Feedback'])) {
   			$model->attributes=$_POST['Feedback'];
            $model->created = new CDbExpression('NOW()');

   			if ($model->save())
   				$this->redirect(array('update', 'id'=>$model->id));
   		}

   		$this->render('create', compact('model'));
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

   		if (isset($_POST['Feedback'])) {
   			$model->attributes=$_POST['Feedback'];

            if ($model->save())
   		        $this->refresh();
   		}

   		$this->render('update', compact('model'));
   	}

   	/**
   	 * Deletes a particular model.
   	 * If deletion is successful, the browser will be redirected to the 'admin' page.
   	 * @param integer $id the ID of the model to be deleted
   	 */
   	public function actionDelete($id)
   	{
   		if (Yii::app()->request->isPostRequest) {
   			// we only allow deletion via POST request
   			$this->loadModel($id)->delete();

   			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
   			if(!isset($_POST['ajax']))
   				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
   		} else
   			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
   	}


   	/**
   	 * Returns the data model based on the primary key given in the GET variable.
   	 * If the data model is not found, an HTTP exception will be raised.
   	 * @param integer the ID of the model to be loaded
   	 */
   	public function loadModel($id)
   	{
   		$model = Feedback::model()->findByPk((int)$id);
   		if ($model===null)
   			throw new CHttpException(404,'The requested page does not exist.');
   		return $model;
   	}

   	/**
   	 * Performs the AJAX validation.
   	 * @param CModel the model to be validated
   	 */
   	protected function performAjaxValidation($model)
   	{
   		if(isset($_POST['ajax']) && $_POST['ajax']==='feedback-form')
   		{
   			echo CActiveForm::validate($model);
   			Yii::app()->end();
   		}
   	}
}
