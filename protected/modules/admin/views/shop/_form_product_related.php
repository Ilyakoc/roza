<?php
/**
 * @var $model Product
 * @var $form CActiveForm
 */
$criteria = new CDbCriteria();
$criteria->order = 'title';

$relatedProducts = [];

if (!$model->getIsNewRecord()) {
	$criteria->addCondition('`t`.`id` != ' . $model->id);

	$relatedProducts = array_keys($model->relatedProducts);
}

$productsList = Product::model()->findAll($criteria);

$model->setRelatedProducts = true;
echo $form->hiddenField($model, 'setRelatedProducts');
?>

<div class="row chosen">
	<label>Выберите сопутствующие товары</label>
	<?php
		$this->widget('ext.chosen.Chosen',array(
			'value' => $relatedProducts,
		   	'name' => 'related', // input name
		   	'multiple' => true,
		   	'placeholderMultiple' => 'Выберите сопутствующие товары',
		   	'data' => CHtml::listData($productsList, 'id', 'title'),
		));
	?>
</div>
