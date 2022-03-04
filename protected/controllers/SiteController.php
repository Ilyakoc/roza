<?php

class SiteController extends Controller
{
	public function beforeaction($action){
		$urlAbsoluteSite = $_SERVER['REQUEST_URI'];
		$findme = '/buket-na-Den-Rozhdeniia';
		if(stripos($urlAbsoluteSite, $findme)!== false && $urlAbsoluteSite !== $findme){
			$scriptUrl = Yii::app()->request->scriptUrl;
			$new_url = str_replace($scriptUrl, "", $findme);		
			$this->redirect($new_url);
		}
		return true;
	}
	
    public $subcontent = null;

	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			)
		);
	}

	public function actionIndex()
	{
        $this->layout = 'front';
		$sliders = Category::model()->findAllByAttributes(array('front' => 1), ['order' => 'sort_slider']);

        $menuItem = CmsMenu::getInstance()->getDefault();

        if (!$menuItem)
            throw new CHttpException('404', 'Не найдена страница по умолчанию');

        if ($menuItem->options['model'] == 'page') {
            $page = Page::model()->findByPk($menuItem->options['id']);

            if (!$page)
                throw new CHttpException('404', 'Не найдена главная страница');

            $this->prepareSeo($page->title);
            $this->seoTags($page);
            ContentDecorator::decorate($page);

            $this->render('page', compact('page','sliders'));
        } elseif ($menuItem->options['model']=='shop') {
            $this->forward('shop/index');
        } elseif ($menuItem->options['model']=='event') {
            if (isset($menuItem->options['id'])) {
                $_GET['id'] = $menuItem->options['id'];
                $this->forward('site/event');
            } else
                $this->forward('site/events');
        } elseif ($menuItem->options['model']=='blog') {
            $_GET['id'] = $menuItem->options['id'];
            $this->forward('site/blog');
        } else {
            throw new CHttpException(404, 'Страница не определена');
        }
	}
	
    public function actionPage($id)
    {
        $this->layout = 'other';

        $page = Page::model()->findByPk($id);

        if (!$page) {
            throw new CHttpException('404', 'Страница не найдена');
        }

        $this->prepareSeo($page->title);
        $this->seoTags($page);

        ContentDecorator::decorate($page);

        $this->render('page_page', compact('page'));
    }

    public function actionEvent($id)
    {
		$this->redirect('/');
        $this->layout = 'other';

        $event = Event::model()->findByAttributes(array('id'=>$id,'type'=>'news'));

        if (!$event) {
            throw new CHttpException('404', 'Новость не найдена');
        }

        $this->prepareSeo($event->title);
        ContentDecorator::decorate($event);
        $this->render('event', compact('event'));
    }
    public function actionArticle($id)
    {
        $this->layout = 'other';

        $event = Event::model()->findByAttributes(array('id'=>$id,'type'=>'article'));

        if (!$event) {
            throw new CHttpException('404', 'Статьи не найдена');
        }

        $this->prepareSeo($event->title);
        ContentDecorator::decorate($event);
        $this->render('article', compact('event'));
    }
    public function actionWiki($id)
    {
		$this->redirect('/');
        $this->layout = 'other';

        $event = Event::model()->findByAttributes(array('id'=>$id,'type'=>'wiki'));

        if (!$event) {
            throw new CHttpException('404', 'Статьи не найдена');
        }

        $this->prepareSeo($event->title);
        ContentDecorator::decorate($event);
        $this->render('wiki', compact('event'));
    }

    public function actionEvents()
    {
		$this->redirect('/');
        $this->layout = 'other';

        $criteria = new CDbCriteria();
        $criteria->condition = 'publish = 1';
        $criteria->compare('type', 'news');
        $criteria->order     = 'created DESC';

        $count = Event::model()->count($criteria);

        $pages = new CPagination($count);
        $pages->pageSize = Yii::app()->params['news_limit'] ? Yii::app()->params['news_limit'] : 7;
        $pages->applyLimit($criteria);

        $events = Event::model()->findAll($criteria);

        $this->prepareSeo('Новости');

        foreach($events as $e) {
            ContentDecorator::decorate($e);
        }

        $this->render('events', compact('events', 'pages'));
    }

	public function actionArticles()
    {
        $this->layout = 'other';

        $criteria = new CDbCriteria();
        $criteria->condition = 'publish = 1';
        $criteria->compare('type', 'article');
        $criteria->order     = 'created DESC';

        $count = Event::model()->count($criteria);

        $pages = new CPagination($count);
        $pages->pageSize = Yii::app()->params['news_limit'] ? Yii::app()->params['news_limit'] : 7;
        $pages->applyLimit($criteria);

        $events = Event::model()->findAll($criteria);

        $this->prepareSeo('Статьи');

        foreach($events as $e) {
            ContentDecorator::decorate($e);
        }

        $this->render('articles', compact('events', 'pages'));
    }

    public function actionWikis()
    {
		$this->redirect('/');
        $this->layout = 'other';

        $criteria = new CDbCriteria();
        $criteria->condition = 'publish = 1';
        $criteria->compare('type', 'wiki');
        $criteria->order     = 'created DESC';

        $count = Event::model()->count($criteria);

        $pages = new CPagination($count);
        $pages->pageSize = Yii::app()->params['news_limit'] ? Yii::app()->params['news_limit'] : 7;
        $pages->applyLimit($criteria);

        $events = Event::model()->findAll($criteria);

        $this->prepareSeo('Wiki');

        foreach($events as $e) {
            ContentDecorator::decorate($e);
        }

        $this->render('wikies', compact('events', 'pages'));
    }


    public function actionBlog($id)
    {
        $this->layout = 'other';

        $blog = Blog::model()->findByPk($id);

        if (!$blog) {
            throw new CHttpException('404', 'Новость не найдена');
        }

        $criteria = new CDbCriteria();
        $criteria->condition = 'blog_id = ?';
        $criteria->order     = 'created DESC';
        $criteria->params[]  = $id;

        $count = Page::model()->count($criteria);

        $pages = new CPagination($count);
        $pages->pageSize = Yii::app()->params['posts_limit'] ? Yii::app()->params['posts_limit'] : 7;
        $pages->applyLimit($criteria);

        $posts = Page::model()->findAll($criteria);

        $this->prepareSeo($blog->title);
        $this->render('blog', compact('blog', 'posts', 'pages'));
    }

    public function actionActions()
    {
        $this->layout = 'other';

        $criteria = new CDbCriteria();
        $criteria->condition = 'sale_value > 0 AND notexist != 1';
        $criteria->order     = 'sale_value DESC';

        $products = Product::model()->findAll($criteria);

        $this->prepareSeo('Акции');

        $this->render('actions', compact('products'));
    }

    /*public function actionTest()
    {
        $products = Product::model()->findAll(['order' => 'title']);

        $list = [];

        foreach ($products as $product) {
            $list[] = [
                $product->id,
                iconv('utf-8', 'windows-1251', $product->title),
                iconv('utf-8', 'windows-1251', $product->composition),
                iconv('utf-8', 'windows-1251', $product->price),
            ];
        }

        $fp = fopen('file.csv', 'w');

        foreach ($list as $fields) {
            fputcsv($fp, $fields, ';');
        }

        fclose($fp);
    }*/

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
        $this->layout = 'other';

	    if($error=Yii::app()->errorHandler->error)
	    {
	    	if(Yii::app()->request->isAjaxRequest)
	    		echo $error['message'];
	    	else{
          $this->render('error', $error);

        }

	    }
	}
	
}
