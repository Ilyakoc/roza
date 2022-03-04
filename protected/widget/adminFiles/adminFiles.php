<?php

class adminFiles extends CWidget
{
    public $model;
    public $form;

    public function run()
    {
        $this->render('form', array('form'=>$this->form, 'model'=>$this->model));

        $criteria = new CDbCriteria();
        $criteria->condition = 'model = ? AND item_id = ?';
        $criteria->params[] = $this->model->tableName();
        $criteria->params[] = $this->model->id;

        $files = File::model()->findAll($criteria);

        if ($files) {
            $model_name = strtolower(get_class($this->model));
            $this->render('list', array(
                'files'=>$files,
                'model'=>$this->model,
                'form'=>$this->form,
                'model_name'=>$model_name
            ));
        }
    }
}
