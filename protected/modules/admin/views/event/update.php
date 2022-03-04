<?php $this->pageTitle = 'Редактирование новости - '. $this->appName; ?>

<h1>Редактирование новости</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
