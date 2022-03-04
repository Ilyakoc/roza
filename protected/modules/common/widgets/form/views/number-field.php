<?
/** @var \common\widgets\form\NumberField $this */

echo $this->openTag();
echo $this->labelTag();

echo $this->form->numberField($this->model, $this->attribute, $this->htmlOptions);
if(!empty($this->unit)) {
	echo \CHtml::tag($this->unitTag, $this->unitOptions, $this->unit);
}

echo $this->errorTag();

if($this->note) {
	echo \CHtml::tag($this->noteTag, $this->noteOptions, $this->note);
}

echo $this->closeTag();
