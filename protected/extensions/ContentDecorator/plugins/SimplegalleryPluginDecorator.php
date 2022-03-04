<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Rick
 * Date: 04.10.11
 * Time: 15:38
 */

class SimpleGalleryPluginDecorator extends PluginDecorator
{
    public $point = '{simple_gallery}';

    public function processModel($model, $attribute = 'text')
    {
        $result = $this->checkPoint($model->$attribute);

        if (!$result)
            return;

        $model_name = strtolower(get_class($model));

        $criteria = new CDbCriteria;
        $criteria->condition = 'model = ? AND item_id = ?';
        $criteria->params[]  = $model_name;
        $criteria->params[]  = $model->id;

        $count = CImage::model()->count($criteria);
        $html  = '';

        if ($count) {
            $options = array(
                'model' => $model_name,
                'item_id' => $model->id,
                'view' => 'site'
            );
            
            ob_start();
            Yii::app()->getController()->widget('widget.ItemImages.ItemImages', $options);
            $html = ob_get_contents();
            ob_clean();

            $this->includeJs();
        }

        $model->$attribute = $this->replace($model->$attribute, $html);
    }
}
