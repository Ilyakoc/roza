<?
/** @var \common\widgets\form\DateField $this */

echo $this->openTag();
echo $this->labelTag();

$this->widget('zii.widgets.jui.CJuiDatePicker', [
	'language'=>$this->language,
	'model'=>$this->model,
	'attribute'=>$this->attribute,
	'options'=>$this->options,
	'htmlOptions'=>$this->htmlOptions
]);

echo $this->errorTag();
echo $this->closeTag();