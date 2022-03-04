<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Alexok
 * Date: 28.11.11
 * Time: 15:28
 */ 
class ShopSettingsForm extends CFormModel
{
    public $cropTop;

    public function rules()
    {
        return array(
            array('cropTop', 'safe')
        );
    }

    public function attributeLabels()
    {
        return array(
            'cropTop'=>'Позиция обрезки фото товара'
        );
    }

    public function saveSettings()
    {
        Yii::app()->settings->set('shop_settings', $this->attributes);
    }

    public function loadSettings()
    {
        foreach($this->attributeNames() as $attr) {
            $this->$attr = Yii::app()->settings->get('shop_settings', $attr);
        }
    }
}
