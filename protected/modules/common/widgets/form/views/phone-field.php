<?
/** @var \common\widgets\form\TextField $this */

echo $this->openTag();
echo $this->labelTag();

$this->owner->widget('\CMaskedTextField', [
	'model'=>$this->model,
    'attribute'=>$this->attribute,
    'mask'=>$this->mask,
	'htmlOptions'=>$this->htmlOptions
]);

echo $this->errorTag();

if($this->note) {
    echo \CHtml::tag($this->noteTag, $this->noteOptions, $this->note);
}

echo $this->closeTag();
