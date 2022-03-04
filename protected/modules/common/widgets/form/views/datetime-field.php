<?
/** @var \common\widgets\form\DateTimeField $this */

echo $this->openTag();
echo $this->labelTag();

$this->widget('common.vendors.EJuiTimePicker.EJuiTimePicker', [
	'language'=>$this->language,
	'mode'=>$this->mode,
	'model'=>$this->model,
	'attribute'=>$this->attribute,
	'options'=>$this->options,
	'htmlOptions'=>$this->htmlOptions
]);

echo $this->errorTag();
echo $this->closeTag();