<?php
/* @var $this BigButtonController */
/* @var $model BigButton */

$this->breadcrumbs=array(
	'Баннеры на главной'=>array('index'),
	$model->title=>array('view','id'=>$model->id),
	'Редактировать',
);

$this->menu=array(
	array('label'=>'List BigButton', 'url'=>array('index')),
	array('label'=>'Create BigButton', 'url'=>array('create')),
	array('label'=>'View BigButton', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage BigButton', 'url'=>array('admin')),
);
?>

<h1>Редактировать <?php echo $model->id; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>