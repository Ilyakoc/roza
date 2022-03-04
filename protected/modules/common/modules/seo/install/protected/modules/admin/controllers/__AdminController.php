<?php
Yii::setPathOfAlias('seo', Yii::getPathOfAlias('application.modules.seo'));
Yii::import('seo.SeoModule');

class SeoController extends \seo\modules\admin\controllers\DefaultController
{	
}