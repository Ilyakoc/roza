<?php $this->pageTitle = 'Редактирование - '. $this->appName; ?>

<h1>Редактирование</h1>

<?php if (Yii::app()->user->hasFlash('slide_update')): ?>
    <h3 style="color: green;">Успешно сохранено!</h3>
<?php endif; ?>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
