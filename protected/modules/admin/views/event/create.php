<?php $this->pageTitle = 'Новое событие - '. $this->appName; ?>

<h1>Новое событие</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
