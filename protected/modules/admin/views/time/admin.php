<?php
/* @var $this TimeController */
/* @var $model Time */

$this->breadcrumbs=array(
	'Times'=>array('index'),
	'Manage',
);

$this->menu=array(
	array('label'=>'List Time', 'url'=>array('index')),
	array('label'=>'Create Time', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#time-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Управление</h1>

<a href="/admin/time/create">Создать</a>


<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'time-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'title',
		'sort',
		array(
			'class'=>'CButtonColumn',
			'template' => '{update} {delete}',
		),
	),
)); ?>
