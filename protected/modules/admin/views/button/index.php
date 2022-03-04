<?php
/* @var $this ButtonController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Buttons',
);

$this->menu=array(
	array('label'=>'Create Button', 'url'=>array('create')),
	array('label'=>'Manage Button', 'url'=>array('admin')),
);
?>

<h1>Buttons</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
