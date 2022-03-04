<?php
/* @var $this FooterLinkController */
/* @var $model FooterLink */

$this->breadcrumbs=array(
	'Footer Links'=>array('index'),
	$model->title,
);

$this->menu=array(
	array('label'=>'List FooterLink', 'url'=>array('index')),
	array('label'=>'Create FooterLink', 'url'=>array('create')),
	array('label'=>'Update FooterLink', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete FooterLink', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage FooterLink', 'url'=>array('admin')),
);
?>

<h1>View FooterLink #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'title',
		'category_id',
		'link',
		'sort',
		'column_id',
	),
)); ?>
