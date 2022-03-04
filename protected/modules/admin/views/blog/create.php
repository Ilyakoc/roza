<?php $this->pageTitle = 'Новый блог - '. $this->appName; ?>

<h1 class="with-select">Добавление</h1>
<?php $this->widget('admin.widget.MenuTypes.MenuTypes'); ?>

<?php echo $this->renderPartial('_form', compact('model')); ?>
