<?php
/* @var $this ButtonController */
/* @var $model Button */

$this->breadcrumbs=array(
	'Кнопки в меню'=>array('index'),
	'Создать',
);

$this->menu=array(
	array('label'=>'List Button', 'url'=>array('index')),
	array('label'=>'Manage Button', 'url'=>array('admin')),
);
?>

<h1>Создать</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>