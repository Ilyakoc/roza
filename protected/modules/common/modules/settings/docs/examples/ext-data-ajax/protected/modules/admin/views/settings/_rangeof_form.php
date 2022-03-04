<?php
/** @var \settings\modules\admin\controllers\DefaultController $this */
/** @var \RangeofSettings $model */
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;

$tbtn=Y::ct('CommonModule.btn', 'common');
?>
<div class="form"><? 
	$form=$this->beginWidget('\CActiveForm', [
		'id'=>'rangeof-form',
		'enableClientValidation'=>true,
		'clientOptions'=>[
			'validateOnSubmit'=>true,
			'validateOnChange'=>false
		],
		'htmlOptions'=>['enctype'=>'multipart/form-data'],
	]); 
	
	echo $form->errorSummary($model); 
	
	$this->widget('\common\ext\dataAttribute\widgets\DataAttribute', [
		'behavior' => $model->itemsBehavior,
		'header'=>['item'=>'Область применения'],
		'hideActive'=>true,
		'defaultActive'=>true,
		'types'=>[
			'item'=>[
				'type'=>'model',
				'view'=>'admin.views.settings._rangeof_item_form',
				'params'=>[
					'class'=>'\RangeofItemSettings',
					'ajax-tpl-url'=>$this->createUrl('settings/getRangeofItem')					
				]
			],
		]
	]);
	
	?>
	<div class="row buttons">
      <div class="left">
        <?= CHtml::submitButton($tbtn('save'), ['class'=>'btn btn-primary']); ?>
      </div>
      <div class="clr"></div>
    </div>
	<? $this->endWidget(); ?>
</div>