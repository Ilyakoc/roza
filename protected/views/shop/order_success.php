<h1>Оформление заказа</h1>

<?php if (Yii::app()->user->hasFlash('order')): ?>
    <div class="flash-success">
        <?php echo Yii::app()->user->getFlash('order'); ?>
    </div>
<?php else: ?>
    <p>Спасибо, Ваш заказ отправлен! В ближайшее время наш менеджер с Вами свяжется, если этого не произошло или у Вас возникли вопросы, позвоните по телефону 239-66-75 или 8-983-310-66-75!</p>
<?php endif; ?>
