<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Rick
 * Date: 26.01.12
 * Time: 14:00
 * To change this template use File | Settings | File Templates.
 */
class ajaxUploader extends CWidget
{
    public $fieldNamePrefix = 'upload_field_';
    public $fieldName  = 'name';
    public $fieldLabel = 'Загрузка';
    public $model      = null;
    public $fileType   = 'file';

    public static $types = array(
        'image'=>'CImage',
        'file'=>'File'
    );

    public function run()
    {
        $this->publishAssets();

        if ($this->fileType == 'image') {
            $items = $this->getImages();
        } else {
            $items = $this->getFiles();
        }

        $this->render('default', compact('items'));
    }

    public function publishAssets()
    {
        $assets = dirname(__FILE__) . '/assets';
        $baseUrl = Yii::app()->assetManager->publish($assets);
        if (is_dir($assets)) {
            CmsHtml::js($baseUrl .'/vendor/jquery.ui.widget.js');
            CmsHtml::js($baseUrl .'/jquery.iframe-transport.js');
            CmsHtml::js($baseUrl .'/jquery.fileupload.js');
        } else {
            throw new CHttpException(500, 'ajaxUploader - Error: Couldn\'t find assets to publish.');
        }
    }

    public function createFieldName()
    {
        return $this->fieldNamePrefix . $this->fieldName;
    }

    public function getSendParams()
    {
        $result = array(
            'fieldName'=>$this->createFieldName(),
            'modelName'=>get_class($this->model),
            'id'=>$this->model->id
        );

        if (!empty($this->fileType)) {
            $result['type'] = $this->fileType;
        }

        return CJSON::encode($result);
    }

    private function getFiles()
    {
        $criteria = new CDbCriteria();
        $criteria->condition = 'model = ? AND item_id = ?';
        $criteria->params[] = $this->model->tableName();
        $criteria->params[] = $this->model->id;

        return File::model()->findAll($criteria);
    }

    private function getImages()
    {
        $criteria = new CDbCriteria();
        $criteria->condition = 'model = ? AND item_id = ?';
        $criteria->params[] = $this->model->tableName();
        $criteria->params[] = $this->model->id;
        $criteria->order = 'ordering';

        return CImage::model()->findAll($criteria);
    }
}
