<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Rick
 * Date: 23.01.12
 * Time: 15:17
 * To change this template use File | Settings | File Templates.
 */
class AjaxController extends AdminController
{
    public function actionMenuOrder()
    {
        $items = Yii::app()->request->getParam('item');
        MenuHelper::getInstance()->reorder($items);

        echo 'ok';
        Yii::app()->end();
    }

    public function actionImageOrder()
    {
        $orders = Yii::app()->request->getParam('image');
        $images = CImage::model()->findAllByPk($orders);

        foreach($images as $img) {
            $img->ordering = array_search($img->id, $orders) + 1;
            $img->save();
        }

        echo 'ok';
        Yii::app()->end();
    }

    public function actionUpload()
    {
        $fieldName = Yii::app()->request->getPost('fieldName');
        $modelName = Yii::app()->request->getPost('modelName');
        $id        = Yii::app()->request->getPost('id');
        $type      = Yii::app()->request->getPost('type', 'file');

        $file = CUploadedFile::getInstanceByName($fieldName);

        if ($file instanceof CUploadedFile) {
            $model  = array('model'=>$modelName, 'id'=>$id);

            $upload = new UploadHelper;
            $upload->add($file, $model, $type);
            $upload->runUpload();

            $item = $upload->uploadedModels[0];
        }

        if ($type === 'image') {
            $this->renderPartial('admin.widget.ajaxUploader.views._item_image', compact('item'));
        } else {
            $this->renderPartial('admin.widget.ajaxUploader.views._item_file', compact('item'));
        }

        Yii::app()->end();
    }

    public function actionRemoveFile($id)
    {
        $model = File::model()->findByPk($id);

        if ($model === null)
            throw new CHttpException(404, 'Файл не найден');

        if (!$model->delete())
            throw new CHttpException(500, 'Ошибка удаления файла');

        if (Yii::app()->request->isAjaxRequest)
            echo 'ok';
        else
            $this->redirect(array('/admin/'. $model->model .'/update', 'id'=>$model->item_id));
    }
}
