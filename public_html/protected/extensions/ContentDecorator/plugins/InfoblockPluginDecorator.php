<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Rick
 * Date: 04.10.11
 * Time: 11:54
 */

class InfoblockPluginDecorator extends PluginDecorator
{
    public function processModel($model, $attribute = 'text')
    {
        $result = $this->checkPoint($model->$attribute);
        if (!$result) return;

        $content    = '<div class="text">'. $result['content'] .'</div>';
        $info_block = str_replace($result['content'], $content, $result['full']);
        $model->$attribute = str_replace($result['full'], $info_block, $model->$attribute);
    }

    protected function checkPoint($text)
    {
        //if (preg_match('#<(div).*?class="user_info_block"[^>]*>(.*)</\1>#is', $text, $values)) {
        if (preg_match('#<div\s+[^>]*?class\s*=\s*"user_info_block"[^>]*>((?:(?!</div>).)*)</div>#is', $text, $values)) {
            return array(
                'full'    => $values[0],
                'content' => $values[1]
            );
        }
        return false;
    }
}
 
