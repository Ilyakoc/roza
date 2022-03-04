<?php
Yii::setPathOfAlias('settings', Yii::getPathOfAlias('common.modules.settings'));
Yii::import('settings.SettingsModule');

class SettingsController extends \settings\modules\admin\controllers\DefaultController
{	
}