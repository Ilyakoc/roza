<?php
/* @var $this FooterLinkController */
/* @var $model FooterLink */

$this->breadcrumbs=array(
	'Footer Links'=>array('index'),
	$model->title=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List FooterLink', 'url'=>array('index')),
	array('label'=>'Create FooterLink', 'url'=>array('create')),
	array('label'=>'View FooterLink', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage FooterLink', 'url'=>array('admin')),
);
?>

<h1>Редактировать</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>