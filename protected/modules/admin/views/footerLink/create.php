<?php
/* @var $this FooterLinkController */
/* @var $model FooterLink */

$this->breadcrumbs=array(
	'Footer Links'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List FooterLink', 'url'=>array('index')),
	array('label'=>'Manage FooterLink', 'url'=>array('admin')),
);
?>

<h1>Создать</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>