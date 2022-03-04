<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Rick
 * Date: 28.08.12
 * Time: 11:41
 * To change this template use File | Settings | File Templates.
 */
class SliderController extends AdminController
{
    public function actionOrder()
    {
        $orders = Yii::app()->request->getParam('item');

        $items = Slide::model()->findAll(array('order'=>'ordering'));
        $reset = !$orders || !is_array($orders) ? true : false;
        $i = 1;

        foreach($items as $item) {
            if ($item->ordering > 0) {
                $inx = !$reset ? array_search($item->id, $orders) : $i++ ;
                $item->ordering = $inx + 1;
                $item->save();
            }
        }

        Yii::app()->end();
    }

    public function actionIndex()
    {
        $slides = Slide::model()->findAll(array('order'=>'ordering'));

        $this->render('index', compact('slides'));
    }

    public function actionCreate()
    {
        $model = new Slide();

        if (isset($_POST['Slide'])) {
            $model->attributes = $_POST['Slide'];
            if ($model->save()) {
                $this->redirect(array('slider/index'));
            }
        }

        $this->render('create', compact('model'));
    }

    public function actionUpdate($id)
    {
        $model = Slide::model()->findByPk($id);

        if (isset($_POST['Slide'])) {
            $model->attributes = $_POST['Slide'];
            if ($model->save()) {
                Yii::app()->user->setFlash('slide_update', true);
                $this->refresh();
            }
        }
        $this->render('update', compact('model'));
    }

    public function actionRemove($id)
    {
        $model = Slide::model()->findByPk($id);
        if (!$model)
            throw new CHttpException(404, 'Не найдено!');

        if (!$model->delete())
            throw new CHttpException(500, 'Не удалось удалить');

        $this->redirect(array('slider/index'));
    }
}
