<?php
/* @var $this OfferController */
/* @var $model Offer */

$this->breadcrumbs=array(
	'Торговые предложения'=>array('index'),
	'Создание',
);
?>

<h1>Создание предложения</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>