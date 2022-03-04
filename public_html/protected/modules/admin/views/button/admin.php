<?php
/* @var $this ButtonController */
/* @var $model Button */

$this->breadcrumbs=array(
	'Кнопки в меню'=>array('index'),
	'Управление',
);

$this->menu=array(
	array('label'=>'List Button', 'url'=>array('index')),
	array('label'=>'Create Button', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#button-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Управление</h1>

<a href="/admin/button/create" class="default-button">Создать</a>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'button-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'title',
		// 'preview',
		'link',
		'active',
		// 'image_class',
		array(
			'class'=>'CButtonColumn',
			'template' => '{update} {delete}',
		),
	),
)); ?>
