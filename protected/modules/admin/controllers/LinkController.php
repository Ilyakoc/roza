<?php

class LinkController extends AdminController
{
	public function actionCreate()
	{
        $model = new Link();

        if (isset($_POST['Link'])) {
			$model->attributes = $_POST['Link'];

			if ($model->save())
				$this->redirect(array('update', 'id'=>$model->id));
		}

		$this->render('create', compact('model'));
	}

	public function actionDelete($id)
	{
        $model = $this->loadModel($id);
        $model->delete();

        $this->redirect(array('deleteok'));
	}

	public function actionUpdate($id)
	{
        $model = $this->loadModel($id);

        if (isset($_POST['Link'])) {
            $model->attributes = $_POST['Link'];
            $model->save();
            
            $this->refresh();
        }

		$this->render('update', compact('model'));
	}

    public function loadModel($id)
	{
		$model = Link::model()->findByPk((int)$id);
		if ($model === null)
			throw new CHttpException(404, 'Страница не найдена');
		return $model;
	}

    public function actionDeleteOk()
    {
        $this->render('delete_ok');
    }
}
