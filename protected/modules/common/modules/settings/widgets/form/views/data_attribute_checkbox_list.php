<?php
/** @var \settings\widgets\form\DataAttributeCheckBoxList $this */
/** @var \CArrayDataProvider $dataProvider */
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use settings\components\helpers\HSettings;

$tbtn=Y::ct('CommonModule.btn', 'common');

?><div class="panel panel-default">
	<div class="panel-heading"><?= $this->form->labelEx($this->model, $this->attribute); ?></div>
	<div class="panel-body"><? 
	if($this->getDataProvider()->totalItemCount) {
		echo $this->form->checkBoxList($this->model, $this->attribute, $this->getListData(), ['labelOptions'=>['style'=>'display:inline !important']]); 
	} else { 
		if($this->emptyText) echo CHtml::tag('p', [], $this->emptyText);
		if($this->settingsId) echo CHtml::link($tbtn('add'), ['settings/'.$this->settingsId], ['class'=>'btn btn-default btn-xs']);
	}
	?></div>
</div>