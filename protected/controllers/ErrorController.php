<?php
class ErrorController extends Controller
{
	public $layout = 'other';

	/**
	 * Отображение ошибки
	 * @param int $code код ошибки
	 */
	public function actionIndex($code)
	{
		http_response_code(404);

		$this->prepareSeo(\Yii::t('error', 'error'));

		$this->render('index', compact('code'));
	}

	/**
	 * Обработка ошибок
	 */
	public function actionError()
	{
		$this->actionIndex(404);
		exit;

		$error=Yii::app()->errorHandler->error;

		if(YII_DEBUG) {
			echo 'IS DEBUG MODE<br/>Error code: '.$error['code'].'<br/>Error message: '.$error['message'];
			\Yii::app()->end();
			die;
		}

		/*if($error && in_array($error['code'], array(400, 403, 404, 500))) {
			$this->redirect('/'.$error['code']);
		}*/

		$this->redirect("/404");
	}
}
