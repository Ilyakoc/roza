<?php
/* @var $this FooterLinkController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Footer Links',
);

$this->menu=array(
	array('label'=>'Create FooterLink', 'url'=>array('create')),
	array('label'=>'Manage FooterLink', 'url'=>array('admin')),
);
?>

<h1>Footer Links</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
