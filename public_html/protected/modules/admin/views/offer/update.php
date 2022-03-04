<?php
/* @var $this OfferController */
/* @var $model Offer */

$this->breadcrumbs=array(
	'Торговые предложения'=>array('index'),
	$model->title . ' - Обновление',
);

?>

<h1>Обновление предложения <?php echo $model->id; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>