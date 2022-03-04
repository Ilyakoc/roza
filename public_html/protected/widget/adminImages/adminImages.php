<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Rick
 * Date: 02.08.11
 * Time: 12:03
 * To change this template use File | Settings | File Templates.
 */
 
class adminImages extends CWidget
{
    public $model;
    public $form;
    public $viewImages = 'images';
    public $viewForm   = 'form';

    public function run()
    {
        if ($this->form != null)
            $this->render($this->viewForm, array('form'=>$this->form, 'model'=>$this->model));

        $model_name = strtolower(get_class($this->model));

        $criteria = new CDbCriteria();
        $criteria->condition = 'model = ? AND item_id = ?';
        $criteria->params[] = $model_name;
        $criteria->params[] = $this->model->id;
        $criteria->order = 'ordering';

        $images = CImage::model()->findAll($criteria);

        if ($images) {
            $this->render($this->viewImages, array('images'=>$images, 'model'=>$this->model, 'model_name'=>$model_name));
        }
    }
}
