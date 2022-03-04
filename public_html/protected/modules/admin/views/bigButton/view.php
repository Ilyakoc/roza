<?php
/* @var $this BigButtonController */
/* @var $model BigButton */

$this->breadcrumbs=array(
	'Big Buttons'=>array('index'),
	$model->title,
);

$this->menu=array(
	array('label'=>'List BigButton', 'url'=>array('index')),
	array('label'=>'Create BigButton', 'url'=>array('create')),
	array('label'=>'Update BigButton', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete BigButton', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage BigButton', 'url'=>array('admin')),
);
?>

<h1>View BigButton #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'title',
		'preview',
		'link',
		'sort',
		'active',
		'alt',
	),
)); ?>
