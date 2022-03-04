<?php
/** @var \settings\modules\admin\controllers\DefaultController $this */
/** @var string $cid индетификатор настроек CRUD для модели */
/** @var string $crudPagePath путь к настройкам текущей страницы в конфигурации CRUD для текущей модели. */
/** @var \CActiveRecord $model модель */

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use crud\components\helpers\HCrud;
use crud\components\helpers\HCrudForm;
use crud\components\helpers\HCrudView;

$tbtn=Y::ct('CommonModule.btn', 'common');
$tpd=Y::ct('\crud\modules\admin\AdminModule.controllers/default', 'common');
?>
<div class="form"><? 
	$form=$this->beginWidget('\CActiveForm', HCrudForm::getFormProperties($cid, $crudPagePath, [
		'id'=>'crud-form',
		'enableClientValidation'=>true,
		'clientOptions'=>[
			'validateOnSubmit'=>true,
			'validateOnChange'=>false
		],
	])); 
	
	echo $form->errorSummary($model); 

	$this->widget('zii.widgets.jui.CJuiTabs', [
		'tabs'=>HCrudView::getTabs($cid, $crudPagePath, [], ['controller'=>$this, 'form'=>$form, 'model'=>$model]),
		'options'=>[]
	]);
	?>
	
	<div class="row buttons">
	    <div class="left">
	      <?= CHtml::submitButton($model->isNewRecord ? $tbtn('create') : $tbtn('save'), ['class'=>'btn btn-primary']); ?>
	      <?= CHtml::submitButton($model->isNewRecord ? $tbtn('createAndExit') : $tbtn('saveAndExit'), ['class'=>'btn btn-info', 'name'=>'saveout']); ?>
	      <?= CHtml::link(
	      		$tbtn('cancel'), 
	      		HCrud::getConfigUrl($cid, 'crud.index.url', '/crud/admin/default/index', ['cid'=>$cid], 'c'), 
	      		['class'=>'btn btn-default']
	      ); ?>
	    </div>
	
	    <? if(!$model->isNewRecord): ?>
	    <div class="right"><?= CHtml::link('<span>'.$tbtn('remove').'</span>', HCrud::getConfigUrl(
	    	$cid, 'crud.detele.url', '/crud/admin/default/delete', ['cid'=>$cid, 'id'=>$model->id], 'c'
		    ), [
		    	'onclick'=>'return confirm('.$tpd('page.confirm.remove').');',
		    	'class'=>'btn btn-danger delete-b'
		    ]); 
	    ?></div>
	    <? endif; ?>
	
	    <div class="clr"></div>
	</div>
	
	<? $this->endWidget(); ?>
</div>