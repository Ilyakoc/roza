<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Rick
 * Date: 19.01.12
 * Time: 11:37
 */
 
class LinkRoute
{
    public function route($menu_item, & $itemOptions, & $linkOptions)
    {
        $route = '#';
        $model = Link::model()->findByPk($menu_item->options['id']);

        if ($model) {
            $route = $model->url;

            if (preg_match('/^http:\/\//', $route)) {
                if ($linkOptions !== null) {
                    $linkOptions['target'] = '_blank';
                }
            } else {
                if ($route == '/'. Yii::app()->request->pathInfo && $itemOptions !== null)
                    $itemOptions['class'] = 'active';
            }
        }

        return $route;
    }
}
