<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Rick
 * Date: 10.12.12
 * Time: 14:11
 * To change this template use File | Settings | File Templates.
 */ 
class result extends CAction
{
    public function run()
    {
        Yii::import('ext.payment.Robokassa.Robokassa');

        $password2 = Yii::app()->params['shopPayment']['robokassa']['password2'];

        $OutSum = Yii::app()->request->getPost('OutSum');
        $InvId  = Yii::app()->request->getPost('InvId');
        $SignatureValue = Yii::app()->request->getPost('SignatureValue');

        if (!Robokassa::compareSignature($SignatureValue, $OutSum, $InvId, $password2))
            $this->sendError('Not valid signature');

        /* @var Order $order */
        $order = Order::model()->findByPk((int)$InvId);

        if (!$order)
            $this->sendError('Payment not found');

        if ($order->summaryPrice != $OutSum)
            $this->sendError('Wrong price');

        $order->payment_complete = true;
        $order->save(false);

        CmsCore::sendMail('Поступила оплата заказа #'.$order->id);

        echo 'ok'.$order->id;
        Yii::app()->end();
    }

    private function sendError($text)
    {
        echo $text;
        Yii::app()->end();
    }
}
