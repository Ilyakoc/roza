<?php
/** @var \settings\modules\admin\controllers\DefaultController $this */
/** @var object[\settings\components\base\SettingsModel] $model */
use common\components\helpers\HYii as Y;

$t=Y::ct('\settings\modules\admin\AdminModule.controllers/default', 'settings');
$viewForm=$model->getConfigParam('viewForm', '_form');
?>
<h1><?=$model->getConfigParam('title', $t('page.title'))?></h1>
<? $this->renderPartial($viewForm, compact('model')); ?>