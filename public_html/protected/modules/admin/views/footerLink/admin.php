<?php
/* @var $this FooterLinkController */
/* @var $model FooterLink */

$this->breadcrumbs=array(
	'Footer Links'=>array('index'),
	'Manage',
);

$this->menu=array(
	array('label'=>'List FooterLink', 'url'=>array('index')),
	array('label'=>'Create FooterLink', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#footer-link-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Ссылки над футером</h1>

<a href="/cp/footerLink/create" class="default-button">Создать</a>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'footer-link-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'title',
		'category_id',
		'link',
		'sort',
		// 'column_id',
		array(
			'class'=>'CButtonColumn',
			'template' => '{update} {delete}',
		),
	),
)); ?>
