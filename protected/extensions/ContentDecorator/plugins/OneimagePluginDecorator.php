<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Rick
 * Date: 04.10.11
 * Time: 17:17
 */
 
class OneimagePluginDecorator extends PluginDecorator
{
    public function processModel($model, $attribute = 'text')
    {
        if ($this->checkPoint($model->$attribute)) {
            $this->includeJs();
        }
    }

    protected function checkPoint($text)
    {
        if (strpos($text, 'image-full') !== false) {
            return true;
        }
        return false;
    }
}
