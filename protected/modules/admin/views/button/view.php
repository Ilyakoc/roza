<?php
/* @var $this ButtonController */
/* @var $model Button */

$this->breadcrumbs=array(
	'Buttons'=>array('index'),
	$model->title,
);

$this->menu=array(
	array('label'=>'List Button', 'url'=>array('index')),
	array('label'=>'Create Button', 'url'=>array('create')),
	array('label'=>'Update Button', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Button', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Button', 'url'=>array('admin')),
);
?>

<h1>View Button #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'title',
		'preview',
		'link',
		'active',
		'image_class',
	),
)); ?>
