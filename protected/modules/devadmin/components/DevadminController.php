<?php


class DevadminController extends CController
{
    public $layout = "column2";

    public function accessRules()
    {
        return array(
            
            array('deny',
                'users'=>array('?')
            ),
        );
    }

    public function filters() {
        return array(
            'accessControl',
        );
    }    
}
