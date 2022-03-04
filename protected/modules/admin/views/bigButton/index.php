<?php
/* @var $this BigButtonController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Big Buttons',
);

$this->menu=array(
	array('label'=>'Create BigButton', 'url'=>array('create')),
	array('label'=>'Manage BigButton', 'url'=>array('admin')),
);
?>

<h1>Big Buttons</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
