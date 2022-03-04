<?php
/* @var $this TimeController */
/* @var $model Time */

$this->breadcrumbs=array(
	'Times'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Time', 'url'=>array('index')),
	array('label'=>'Manage Time', 'url'=>array('admin')),
);
?>

<h1>Create Time</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>