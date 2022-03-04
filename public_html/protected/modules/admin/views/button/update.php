<?php
/* @var $this ButtonController */
/* @var $model Button */

$this->breadcrumbs=array(
	'Кнопки в меню'=>array('index'),
	$model->title=>array('view','id'=>$model->id),
	'Редактировать',
);

$this->menu=array(
	array('label'=>'List Button', 'url'=>array('index')),
	array('label'=>'Create Button', 'url'=>array('create')),
	array('label'=>'View Button', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Button', 'url'=>array('admin')),
);
?>

<h1>Редактировать <?php echo $model->id; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>