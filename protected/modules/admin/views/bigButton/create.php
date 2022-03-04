<?php
/* @var $this BigButtonController */
/* @var $model BigButton */

$this->breadcrumbs=array(
	'Баннеры на главной'=>array('index'),
	'Создать',
);

$this->menu=array(
	array('label'=>'List BigButton', 'url'=>array('index')),
	array('label'=>'Manage BigButton', 'url'=>array('admin')),
);
?>

<h1>Создать</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>