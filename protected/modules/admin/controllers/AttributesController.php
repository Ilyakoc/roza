<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Rick
 * Date: 23.01.12
 * Time: 15:17
 * To change this template use File | Settings | File Templates.
 */
class AttributesController extends AdminController{
	
    public $breadcrumbs = [];
    public $menu = [];

    public function init()
    {
        //$this->_install();
    }

    public function getDbConnection(){
        return Yii::app()->db;
    }

    private function _install()
    {
        // create table
        $query = '
        CREATE TABLE IF NOT EXISTS `eav_attribute` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `name` varchar(255) NOT NULL,
          `type` smallint(6) NOT NULL,
          `fixed` tinyint(1) NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

        CREATE TABLE IF NOT EXISTS `eav_value` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `id_attrs` int(11) NOT NULL,
          `id_product` int(11) NOT NULL,
          `value` varchar(255) NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
        ';

        $this->getDbConnection()->createCommand($query)->execute();
    }

	public function actionIndex()
	{
		$model=new EavAttribute('search');
        $model->unsetAttributes();  // clear any default values
        if(isset($_GET['EavAttribute']))
            $model->attributes=$_GET['EavAttribute'];

        $this->render('index',array(
            'model'=>$model,
        ));
	}

    public function actionAutocomplete()
    {
        $term = Yii::app()->getRequest()->getParam('term');
        if(Yii::app()->request->isAjaxRequest && $term) {

            $criteria = new CDbCriteria;
            $criteria->addSearchCondition('name', $term);
            $attributes = EavAttribute::model()->findAll($criteria);
            // обрабатываем результат
            $result = array();
            foreach($attributes as $attribute) {
                $result[] = array('id'=>$attribute['id'], 'value'=>$attribute['name']);
            }
            echo CJSON::encode($result);
            Yii::app()->end();
        }
    }
	public function actionAdd()
	{
		$model=new EavAttribute;

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if(isset($_POST['EavAttribute']))
        {
            $model->attributes=$_POST['EavAttribute'];
            if($model->save())
                $this->redirect(array('index'));
        }

        $this->render('add',array(
            'model'=>$model,
        ));
	}

	public function actionUpdate($id)
    {
        $model=$this->loadModel($id);

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if(isset($_POST['EavAttribute']))
        {
            $model->attributes=$_POST['EavAttribute'];
            if($model->save())
                $this->redirect(array('index'));
        }

        $this->render('update',array(
            'model'=>$model,
        ));
    }

    public function actionDelete($id)
    {
        $this->loadModel($id)->delete();

        // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
        if(!isset($_GET['ajax']))
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
    }

    public function loadModel($id)
    {
        $model=EavAttribute::model()->findByPk($id);
        if($model===null)
            throw new CHttpException(404,'The requested page does not exist.');
        return $model;
    }

    protected function performAjaxValidation($model)
    {
        if(isset($_POST['ajax']) && $_POST['ajax']==='attributes-form')
        {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }
}