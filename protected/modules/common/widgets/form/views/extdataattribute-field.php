<?
/** @var \common\widgets\form\ExtDataAttributeField $this */
use common\components\helpers\HArray as A;

echo $this->openTag();
echo $this->labelTag();
if($this->note) {
	echo \CHtml::tag($this->noteTag, $this->noteOptions, $this->note);
}

$this->widget('\common\ext\dataAttribute\widgets\DataAttribute', A::m($this->params, ['behavior'=>$this->behavior]));

echo $this->errorTag();
echo $this->closeTag();
