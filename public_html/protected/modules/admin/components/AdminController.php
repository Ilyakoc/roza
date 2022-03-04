<?php

class AdminController extends CController
{
    public $layout  = 'column2';
    public $appName = 'Админ панель';
    public $breadcrumbs = array();
    private $skin_info = array();
    
    public function init()
    {
        parent::init();

        $site_name = Yii::app()->settings->get('cms_settings', 'sitename');

        if ($site_name)
            Yii::app()->name = $site_name;

        $this->skin_info = require(Yii::getPathOfAlias('admin.skin_info').'.php');

        // Set Error hendler for module
        Yii::app()->errorHandler->errorAction = 'admin/default/error';
    }
    
    public function filters() {
        return array(
            'accessControl',
        );
    }    
    
    public function accessRules()
    {
        return array(
            array('allow',
                'actions'=>array('login', 'extupdate'),
                'users'=>array('?')
            ),
            
            array('deny',
                'users'=>array('?')
            ),
        );
    }

	public function actionError()
	{
        $this->layout = 'column2';
        
	    if($error=Yii::app()->errorHandler->error)
	    {
	    	if(Yii::app()->request->isAjaxRequest)
	    		echo $error['message'];
	    	else
	        	$this->render('error', $error);
	    }
	}

    public function getSkin()
    {
        return isset(Yii::app()->params['skin']) ? Yii::app()->params['skin'] : 'dishman';
    }

    public function skinParam($name)
    {
        $skin = $this->getSkin();

        if (isset($this->skin_info[$skin][$name])) {
            return $this->skin_info[$skin][$name];
        }

        return false;
    }
}
