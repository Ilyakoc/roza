<?php $this->pageTitle = 'Новый пост - '. $this->appName; ?>

<h1>Добавление</h1>

<?php echo $this->renderPartial('/page/_form', compact('model')); ?>
