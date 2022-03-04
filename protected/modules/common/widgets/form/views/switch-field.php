<?php
/** @var $this \common\widgets\form\SwitchField */

if($this->wrapperTag) 
	echo \CHtml::openTag($this->wrapperTag, $this->wrapperOptions);
 
if($this->label) {
	echo \CHtml::openTag('label');
	if($this->labelBefore) 
		echo $this->labelEncode ? \CHtml::encode($this->label) : $this->label; 
}

echo \CHtml::checkBox($this->name, $this->checked, $this->htmlOptions); 

if($this->label) {
	if(!$this->labelBefore)
		echo $this->labelEncode ? \CHtml::encode($this->label) : $this->label;
	echo \CHtml::closeTag('label');
}

if($this->wrapperTag) 
	echo \CHtml::closeTag($this->wrapperTag); 
?>