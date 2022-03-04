<?php

class PayController extends Controller
{
    public function actions()
    {
        return array(
            'robokassa_result'=>array('class'=>'ext.payment.Robokassa.result')
        );
    }

    public function actionClearCart()
    {
        CmsCart::getInstance()->clear();

        if (Yii::app()->request->isAjaxRequest)
            Yii::app()->end();
        else
            $this->redirect(array('index'));
    }

    public function actionPayment()
    {
        $order_id = Yii::app()->user->getState('order_id');

        if (!$order_id)
            $this->redirect(array('order'));

        $order = Order::model()->findByPk((int)$order_id);

        $this->prepareSeo('Оплата');
        $this->render('payment', compact('order'));
    }

    public function actionPayment_success()
    {
        $this->render('payment_success');
    }

    public function actionPayment_fail()
    {
        $this->render('payment_fail');
    }    
}