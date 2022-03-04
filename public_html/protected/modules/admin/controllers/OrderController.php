<?php
/**
 * File: OrderController.php
 * User: Mobyman
 * Date: 28.01.13
 * Time: 12:19
 */

class OrderController extends AdminController {

    public function actionAjax(){
        $action = Yii::app()->request->getPost('action');
        $item = Yii::app()->request->getPost('item');
        $return = array();
        if($action !== null && $item !== null) {
            
            if ($action == 'completed') {
                $model = Order::model()->findByPk((int)$item);
                $model->completed = (int)!(bool)$model->completed;
                $model->save();
                $return = array("status" => $model->completed, "count" => coreHelper::getNotifies('order'));
            }

            if($action == 'comment') {
                $model = Order::model()->findByPk((int)$item);
                $model->comment = CHtml::encode(Yii::app()->request->getPost('comment', ""));
                $model->save();
                $return = array("status" => $model->comment);   
            }
        } else {
            $return = array("status" => "request not valid");
        }
        echo CJSON::encode($return);
    }

    public function actionIndex() {
        $c = new CDbCriteria();
        $c->order = "id DESC";
        $count = Order::model()->count($c);
        $pages = new CPagination($count);
        $pages->pageSize = 30;
        $pages->applyLimit($c);

        $model = Order::model()->findAll($c);

        $this->pageTitle = 'Заказы - '.$this->appName;

        if($model)
            $this->render('index', compact('model', 'pages'));
        else
            $this->render('index_empty');
    }



}
