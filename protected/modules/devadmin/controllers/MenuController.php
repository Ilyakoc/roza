<?php

/**
 * 
 */
class MenuController extends DevadminController
{
	public $layout = "column2";


	public function actionIndex()
	{
		$this->render('index', compact('model'));	
	}

    public function actionEdit()
    {
    	$model = Devmenu::model()->findAll();
   		$this->render('edit', compact('model'));
    }

    public function actionChangeName($id, $newname)
    {
    	$model = Devmenu::model()->findByPk($id);	
    	$model->title = htmlspecialchars($newname);
    	$model->save();
    }

    public function actionToggleHidden($id)
    {
    	$model = Devmenu::model()->findByPk($id);
    	$model->hidden = $model->hidden ? 0 : 1;
    	$model->save();
    }

	public function actionToggleDefault($id)
    {
    	Devmenu::model()->updateAll(array('default' => 0), '`default` = 1');
    	Devmenu::model()->updateByPk($id, array('default' => 1));

    }    

	public function actionError()
	{
        $this->layout = 'column2';
        
	    if($error=Yii::app()->errorHandler->error)
	    {
	    	if(Yii::app()->request->isAjaxRequest)
	    		echo $error['message'];
	    	else
	        	$this->render('error', $error);
	    }
	}

}
