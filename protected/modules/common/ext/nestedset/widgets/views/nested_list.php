<?php
/** @var $this [namespace common\ext\nestedset\widgets\NestedList] */

$level=0;

foreach($this->models as $model) {
	$itemLevel=$this->getItemLevel($model);
	if($itemLevel==$level)
		echo CHtml::closeTag($this->itemTagName);
	else if($itemLevel > $level)
		echo CHtml::openTag($this->tagName, $this->htmlOptions);
	else {
		echo CHtml::closeTag($this->itemTagName);
		for($i=($level-$itemLevel); $i; $i--) {
			echo CHtml::closeTag($this->tagName);
			echo CHtml::closeTag($this->itemTagName);
		}
	}

	if($this->printDataId) {
		$this->itemHtmlOptions['data-id']=$this->getItemId($model);
	}
	echo CHtml::openTag($this->itemTagName, $this->itemHtmlOptions);
	echo $this->getItemContent($model);

	$level=$itemLevel;
}
for($i=$level; $i>0; $i--) {
	echo CHtml::closeTag($this->itemTagName);
	echo CHtml::closeTag($this->tagName);
}
?>