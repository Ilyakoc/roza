<?php $this->pageTitle = 'Редактирование товар - '. $this->appName; ?>

<h1>Редактирование товара</h1>
<?php echo $this->renderPartial('_form_product', compact('model', 'categoryList', 'relatedCategories', 'fixAttributes')); ?>

<?php Yii::app()->clientscript->registerScriptFile($this->module->assetsUrl.'/js/admin_shop.js'); ?>

