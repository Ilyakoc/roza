<?php
/* @var $this AreaController */
/* @var $model Area */
?>

<h1>Районы</h1>

<a href="/cp/area/create" class="default-button">Добавить район</a>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'area-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'title',
		'price',
		'sort',
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
