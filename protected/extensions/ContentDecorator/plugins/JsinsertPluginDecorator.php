<?php
/**
 * Created by JetBrains PhpStorm.
 * User: AlexOk
 * Date: 27.07.12
 * Time: 23:55
 * To change this template use File | Settings | File Templates.
 */
class JsinsertPluginDecorator extends PluginDecorator
{
    public $point = '';
    private $js_content;

    public function processModel($model, $attribute = 'text')
    {
        if (!$this->checkPoint($model->$attribute))
            return;

        $replace = CHtml::decode($this->js_content);
        $replace = $this->strip_selected_tags($replace, '<p><br>');

        $model->$attribute = $this->replace($model->$attribute, $replace);
    }

    protected function checkPoint($text)
    {
        if (preg_match('#<p>{js}</p>(.+)<p>{/js}</p>#is', $text, $values)) {
            $this->point = $values[0];
            $this->js_content = $values[1];
        }
        return true;
    }

    private function strip_selected_tags($str, $tags = "", $stripContent = false)
    {
        preg_match_all("/<([^>]+)>/i",$tags,$allTags,PREG_PATTERN_ORDER);
        foreach ($allTags[1] as $tag){
            if ($stripContent) {
                $str = preg_replace("/<".$tag."[^>]*>.*<\/".$tag.">/iU","",$str);
            }
            $str = preg_replace("/<\/?".$tag."[^>]*>/iU","",$str);
        }
        return $str;
    }
}
