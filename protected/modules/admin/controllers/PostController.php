<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Rick
 * Date: 18.01.12
 * Time: 14:52
 * To change this template use File | Settings | File Templates.
 */
class PostController extends AdminController
{
    public function actionCreate($blog_id = 0)
    {
        if (!$blog_id)
            throw new CHttpException(404, 'Не выбран блог');

        $model = new Page();

        if (isset($_POST['Page'])) {
			$model->attributes = $_POST['Page'];

			if ($model->save()){
				$this->redirect(array('blog/index', 'id'=>$blog_id));
            }
		}

        $model->blog_id = $blog_id;

		$this->render('create', compact('model'));
    }

    public function actionUpdate($id)
    {

    }

    public function actionDelete()
    {

    }

    public function loadModel($id)
	{
		$model = Page::model()->findByPk((int)$id);
		if ($model === null)
			throw new CHttpException(404, 'Пост не найдена');
		return $model;
	}
}
