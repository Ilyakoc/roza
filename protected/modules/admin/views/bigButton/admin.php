<?php
/* @var $this BigButtonController */
/* @var $model BigButton */

$this->breadcrumbs=array(
	'Баннеры на главной'=>array('index'),
	'Управление',
);

$this->menu=array(
	array('label'=>'List BigButton', 'url'=>array('index')),
	array('label'=>'Create BigButton', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#big-button-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Управление</h1>

<a href="/admin/bigButton/create" class="default-button">Создать</a>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'big-button-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'title',
		// 'preview',
		'link',
		// 'sort',
		'active',
		/*
		'alt',
		*/
		array(
			'class'=>'CButtonColumn',
			'template' => '{update} {delete}',
		),
	),
)); ?>
