<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Rick
 * Date: 05.03.12
 * Time: 11:49
 * To change this template use File | Settings | File Templates.
 */
class QuestionController extends Controller
{
    public function actionIndex()
    {
        $model = new Question();

        if (isset($_POST['Question'])) {
            $model->attributes = $_POST['Question'];

            if ($model->save())
                echo 'ok';
            else
                echo 'error';

            Yii::app()->end();
        }

        $list = Question::model()->findAll(array('order'=>'created DESC'));

        $this->prepareSeo('Вопрос-ответ');
		$this->render('index', compact('list', 'model'));
    }
}
