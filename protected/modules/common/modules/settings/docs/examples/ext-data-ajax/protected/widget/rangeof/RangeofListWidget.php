<?php
use settings\components\helpers\HSettings;

class RangeofListWidget extends CWidget 
{
    public function run()
    {
    	$settings=HSettings::getById('rangeof'); 
    	
    	$dataProvider=$settings->itemsBehavior->getDataProvider(true);

    	$this->render('rangeof_list', compact('dataProvider'));
    }

}