<?php
/* @var Order $order */

Yii::import('ext.payment.Robokassa.Robokassa');

$params = Yii::app()->params['shopPayment']['robokassa'];

$login = $params['login'];
$password1 = $params['password1'];
//$password2 = $params['password2'];
$url = $params['url'];

?>
<h1>Оплата</h1>

<div class="form">
    <p>Сейчас вы будете перенаправлены на сайт сервиса <a target="_blank" href="http://robokassa.ru">Робокасса</a> для выбора способа оплаты</p>
    <form action="<?php echo $url; ?>" method="post">
        <input type="hidden" name="Encoding" value="utf-8" />
        <input type="hidden" name="Culture" value="ru" />
        <input type="hidden" name="MrchLogin" value="<?php echo $login; ?>" />
        <input type="hidden" name="OutSum" value="<?php echo $order->summaryPrice; ?>" />
        <input type="hidden" name="InvId" value="<?php echo $order->id; ?>" />
        <input type="hidden" name="Desc" value="Оплата заказа #<?php echo $order->id ?>" />
        <input type="hidden" name="SignatureValue" value="<?php echo Robokassa::generateSignature($order->summaryPrice, $order->id, $login, $password1); ?>" />
        <div class="row buttons1">
            <p>Сумма к оплате: <span style="font-weight: bold"><?php echo $order->summaryPrice; ?> руб</span></p>
            <input type="submit" value="Перейти к оплате" class="payment-button" />
        </div>
    </form>
</div>
