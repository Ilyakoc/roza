<?php
/**
 * Created by JetBrains PhpStorm.
 * User: AlexOk
 * Date: 16.11.11
 * Time: 10:09
 * To change this template use File | Settings | File Templates.
 */ 
class bannerChanger extends CWidget
{
    public $imgId = 'info_img';
    public $scriptSuffix = '';

    public function run()
    {
        $dir = Yii::getPathOfAlias('webroot.images.slider');
        if (!$dir)
            return;

        $files = array();

        $exclude = array('.', '..');
        $images = scandir($dir);
        foreach($images as $img) {
            if (in_array($img, $exclude))
                continue;

            $files[] = '/images/slider/'.$img;
        }

        if (!$files)
            return;

        $js = $this->render('default', compact('files'), true);
        CmsHtml::js('/js/jquery.bgImageTween.cc'. $this->scriptSuffix .'.js');
        Yii::app()->clientScript->registerScript('bgImageTween', $js);
    }
}
