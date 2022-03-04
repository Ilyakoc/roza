<?php
use common\components\helpers\HYii as Y;
use settings\components\helpers\HSettings;

Yii::setPathOfAlias('settings', Yii::getPathOfAlias('common.modules.settings'));
Yii::import('settings.SettingsModule');

class SettingsController extends \settings\modules\admin\controllers\DefaultController
{	
	public function actionIndex($id)
	{
		if($id=='getRangeofItem') return $this->actionGetRangeofItem();

		parent::actionIndex($id);
	}

	public function actionGetRangeofItem()
	{
		$model=new \RangeofItemSettings();		
		$this->renderPartial('_rangeof_item_form', compact('model'), false, true);
		Y::end();
	}
}
