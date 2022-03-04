<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Rick
 * Date: 11.11.11
 * Time: 9:29
 * To change this template use File | Settings | File Templates.
 */ 
class CommentsPluginDecorator extends PluginDecorator
{
    public $point = '{comments}';

    public function processModel($model, $attribute = 'text')
    {
        $result = $this->checkPoint($model->$attribute);

        if (!$result)
            return;

        $code = Yii::app()->settings->get('cms_settings', 'comments');

        if (empty($code))
            return;

        $request = substr_count($code, '%url%') ? Yii::app()->request->requestUri : '';

        if (!empty($request)) {
            $request = substr_replace($request, '', 0, 1);
        }

        $code    = str_replace('%url%', $request, $code);

        $model->$attribute = $this->replace($model->$attribute, $code);
    }
}
