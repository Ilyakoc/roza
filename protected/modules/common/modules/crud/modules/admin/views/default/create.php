<?php
/** @var \crud\modules\admin\controllers\DefaultController $this */
/** @var string $cid индетификатор настроек CRUD для модели. */
/** @var \CActiveRecord $model модель */
/** @var string $formView имя шаблона формы */
use common\components\helpers\HYii as Y;
use crud\components\helpers\HCrud;

$t=Y::ct('\crud\modules\admin\AdminModule.controllers/default', 'common.crud');
$tbtn=Y::ct('\CommonModule.btn', 'common');
?><h1><?=HCrud::param($cid, 'crud.create.title')?></h1><?
$this->renderPartial($formView, [
	'cid'=>$cid, 
	'model'=>$model, 
	'crudPagePath'=>'crud.create'
]);
?>