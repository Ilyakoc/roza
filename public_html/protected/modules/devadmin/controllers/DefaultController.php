<?php


class DefaultController extends DevadminController
{
	public $layout = "column2";
	
	public function actionIndex()
	{
		$this->render('index');
	}

}
