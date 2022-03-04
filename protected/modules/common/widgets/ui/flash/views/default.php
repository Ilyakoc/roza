<?php
/** @var \common\widgets\ui\flash\Yii $this */
use common\components\helpers\HYii as Y;

if(Y::hasFlash($this->id)) {
	echo \CHtml::tag('div', $this->htmlOptions, Y::getFlash($this->id)); 
}
