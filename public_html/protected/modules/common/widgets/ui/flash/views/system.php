<?php
/** @var \common\widgets\ui\flash\Yii $this */
use common\components\helpers\HYii as Y;

if(Y::hasFlash(Y::FLASH_SYSTEM_SUCCESS)):
	?><div class="alert alert-success"><?=Y::getFlash(Y::FLASH_SYSTEM_SUCCESS)?></div><?
endif;

if(Y::hasFlash(Y::FLASH_SYSTEM_ERROR)):
	?><div class="alert alert-danger"><?=Y::getFlash(Y::FLASH_SYSTEM_ERROR)?></div><?
endif;
?>
