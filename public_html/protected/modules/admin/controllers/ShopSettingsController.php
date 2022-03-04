<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Rick
 * Date: 28.11.11
 * Time: 15:33
 * To change this template use File | Settings | File Templates.
 */ 
class ShopSettingsController extends AdminController
{
    public function actionIndex()
    {
        $model = new ShopSettingsForm();

        if (isset($_POST['ShopSettingsForm'])) {
            $model->attributes = $_POST['ShopSettingsForm'];

            if ($model->validate()) {
                $model->saveSettings();
                $this->redirect(array('shop/index'));
            }
        }

        $model->loadSettings();
        $this->render('index', compact('model'));
    }

}
