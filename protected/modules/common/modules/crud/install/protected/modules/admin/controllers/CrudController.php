<?php 
Yii::setPathOfAlias('crud', Yii::getPathOfAlias('common.modules.crud'));
Yii::import('crud.CrudModule');

class CrudController extends \crud\modules\admin\controllers\DefaultController
{	
}