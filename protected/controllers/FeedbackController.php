<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Rick
 * Date: 05.03.12
 * Time: 11:49
 * To change this template use File | Settings | File Templates.
 */
class FeedbackController extends Controller
{
    public function actionIndex()
    {
        $model = new Feedback();

        if (isset($_POST['Feedback'])) {
            $model->attributes = $_POST['Feedback'];

            if ($model->save())
                echo 'ok';
            else
                echo 'error';

            Yii::app()->end();
        }

	    $dataProvider = new CActiveDataProvider('Feedback', array('criteria' => array('condition' => 'published=1', 'order' => 'created DESC'), 'pagination'=>array(
	    'pageSize'=>20)));

	    $this->prepareSeo('Отзывы');
		$this->render('index', compact('dataProvider', 'model'));
    }
}
