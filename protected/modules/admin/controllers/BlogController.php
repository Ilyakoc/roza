<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Rick
 * Date: 16.01.12
 * Time: 13:32
 * To change this template use File | Settings | File Templates.
 */
class BlogController extends AdminController
{
    public function actionIndex($id = null)
    {
        if ($id) {
            $model = $this->loadModel($id);
            $this->render('blog', compact('model'));
        } else {
            $this->render('index');
        }
    }

    public function actionCreate()
    {
        $model = new Blog();

        if (isset($_POST['Blog'])) {
			$model->attributes = $_POST['Blog'];

			if ($model->save())
				$this->redirect(array('index', 'id'=>$model->id));
		}

		$this->render('create', compact('model'));
    }

    public function actionUpdate($id)
    {
        $model = $this->loadModel($id);

        if (isset($_POST['Blog'])) {
            $model->attributes = $_POST['Blog'];
            $model->save();

            $this->refresh();
        }

		$this->render('update', compact('model'));

    }

    public function actionDelete($id)
    {
        $model = $this->loadModel($id);
        $model->delete();

        $this->redirect(array('deleteok'));
    }

    public function loadModel($id)
	{
		$model = Blog::model()->findByPk((int)$id);
		if ($model === null)
			throw new CHttpException(404, 'Блог не найдена');
		return $model;
	}
}
