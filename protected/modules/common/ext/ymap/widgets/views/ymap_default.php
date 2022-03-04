<?php
/** @var \common\ext\ymap\widgets\YMap $this */

echo \CHtml::openTag('div', $this->htmlOptions);
if($this->label) {
	echo \CHtml::tag(
		'div', 
		$this->labelOptions, 
		\CHtml::tag('p', [], $this->label)
	);
}
echo \Chtml::closeTag('div');
?>