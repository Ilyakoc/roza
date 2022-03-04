<?php

class ShopController extends Controller
{
    public function actions()
    {
        return array(
            'robokassa_result'=>array('class'=>'ext.payment.Robokassa.result')
        );
    }

    public function actionIndex()
    {
//		$this->actionCategory(0, Yii::app()->request->getQuery('min',0), Yii::app()->request->getQuery('min',0));
//		Yii::app()->end();

		$c=new CDbCriteria;
		$c->order='new DESC, ordering ASC, id DESC';
		$c->limit=20;
		$min=Yii::app()->request->getQuery('min',0);
		$max=Yii::app()->request->getQuery('max',0);
		//$id_category=Yii::app()->request->getQuery('id',0);

        if ($min || $max) {
            $c->limit = -1;
        }

		if($min && $max) $c->addBetweenCondition('price', $min, $max);
		elseif($min) {
			$c->addCondition('price>=:min'); 
			$c->params[':min']=$min; 
		}
		elseif($max) {
            $c->addCondition('price<=:max');
            $c->params[':max']=$max;
        }
		// if($id_category != 0){
			// $c->addCondition('category_id=:id_category'); 
			// $c->params[':id_category']=$id_category; 
		// }
        $products = Product::model()->findAll($c);

        $this->prepareSeo('Магазин');

        if (Yii::app()->request->isAjaxRequest) {
            echo json_encode(array(
                'title'=>$this->pageTitle,
                'contentTitle'=>'Магазин',
                'content'=>$this->renderPartial('_products', compact('products'), true)
            ));
            Yii::app()->end();
        } else {
//            $categories = Category::model()->findAll(array('order'=>'ordering'));
			$categories=[];
            $this->render('shop', compact('categories', 'products'));
        }
    }

    public function actionCategory($id = 0,$min = 0, $max = 1000000)
    {
    	if(isset($_POST['listpage'])) {
    		$_GET['p']=$_POST['listpage'];
    	}
    	
	if($id !== 0){
	    $category = Category::model()->findByPk($id);

        if (!$category) {
            throw new CHttpException(404, 'Страница не найдена');
        }
        
        \ContentDecorator::decorate($category, 'description');
        \ContentDecorator::decorate($category, 'under_description');
        
		$allcat = $category->descendants()->findAll();
		$ids[] = $category->id;
		foreach($allcat as $cat)
			$ids[] = $cat->id;
        $criteria = new CDbCriteria();
        $criteria->addInCondition('`t`.`category_id`', $ids, 'OR');
        
        $criteria->order = 'price ASC';

        $product=new Product;
        $criteria->mergeWith($product->getRelatedCriteria($ids), 'OR');
        
        if(array_key_exists('min', $_REQUEST)) $min=(int)$_REQUEST['min'];
        if(array_key_exists('max', $_REQUEST)) $max=(int)$_REQUEST['max'];
        $criteria->addBetweenCondition('price', $min, $max);
        
		$pagesCriteria=clone $criteria;
		$pagesCriteria->select='`t`.`id`';
        $all = $product->findAll($pagesCriteria);
        $count = is_array($all) ? count($all) : 0;
        //$count = $product->count($criteria);

        $pages = new CPagination($count);
        $pages->pageVar='p';
        // $pages->pageSize = Yii::app()->params['products_on_page'] ? Yii::app()->params['products_on_page'] : 52;
        $pages->pageSize = 999;
        $pages->applyLimit($criteria);

//        $product=new Product;
//        $criteria->mergeWith($product->getRelatedCriteria($ids), 'OR');
        $products = $product->findAll($criteria);
        
        $this->prepareSeo($category->title);
        
        if($category->meta->meta_h1) {
            $h1=$category->meta->meta_h1;
        }
        else {
            $h1=$category->title;
        }
        $h1=mb_strtolower(mb_substr(trim($h1), 0, 1)) . mb_substr($h1, 1);
        
        $minPrice=0;
        foreach($products as $product) {
            if(!$minPrice || ($product->price && ($product->price<$minPrice))) $minPrice=$product->price;
        }
        
        if(!$category->meta || !$category->meta->meta_title) {
            $category->meta->meta_title='Товары категории ' . $h1 . ' по цене от ' . $minPrice . ' руб';
        }
        if(!$category->meta || !$category->meta->meta_desc) {
            $category->meta->meta_desc='Купить товары категории: ' . $h1 
            . ' вы можете в интернет-магазине: bazaroza.ru, по цене от ' . $minPrice . ' руб. Подробности по тел.: 8 (383) 239-66-75';
        }
        
		$this->seoTags($category->meta);	
        
	}else if($id == 0){//
		$category = new Category();
		$category->title = 'Все товары';
		$category->description = false;
		$category->id = 0;
		$this->prepareSeo($category->title);
                
	        $criteria = new CDbCriteria();
            $criteria->addBetweenCondition('price', $min, $max);
	        $criteria->order = 'price ASC';
		
	        $count = Product::model()->count($criteria);

	        $pages = new CPagination($count);
            // $pages->pageSize = Yii::app()->params['products_on_page'] ? Yii::app()->params['products_on_page'] : 48;
        	$pages->pageSize = 999;
	        $pages->applyLimit($criteria);

        	$products = Product::model()->findAll($criteria);	
	}
        if (Yii::app()->request->isAjaxRequest) {
        	if(isset($_POST['listpage'])) {
        		if(!(($_POST['listpage'] < 2) || ((int)$_POST['listpage'] > $pages->pageCount))) {
        			$isProductsOnly=true;
        			$this->renderPartial('_products', compact('products', 'isProductsOnly'));
        		}
        		Yii::app()->end();
        	}
        	else {
	            echo json_encode(array(
	                'title'=>$this->pageTitle,
	                'contentTitle'=>$category->title,
	                'description'=>$category->description,
	                'content'=>$this->renderPartial('_products', compact('products', 'pages'), true)
	            ));
	            Yii::app()->end();
        	}
        } else {
            $categories = Category::model()->findAll(array('order'=>'ordering'));
            if($category->view_template) $view=$category->view_template;
            else $view='category';
            $this->render($view, compact('products', 'category', 'categories', 'pages'));
        }
    }

    /**
     * Action show a product page
     *
     * @param $id
     */
    public function actionProduct($id)
    {
        $product = Product::model()->findByPk($id);

        if (!$product)
            throw new CHttpException(404, 'Товар не найден в каталоге');

        $categories = Category::model()->findAll(array('order'=>'ordering'));

        $this->prepareSeo($product->title);
        
        if($product->meta->meta_h1) {
            $h1=$product->meta->meta_h1;
        }
        else {
            $h1=$product->title;
        }
        $h1=trim($h1, ' !?.');
        
        if(!$product->meta || !$product->meta->meta_title) {
            $product->meta->meta_title=$h1 . ' по цене ' . $product->price . ' руб';
        }
        if(!$product->meta || !$product->meta->meta_desc) {
            $h1=mb_strtolower(mb_substr(trim($h1), 0, 1)) . mb_substr($h1, 1);
            $product->meta->meta_desc='Купить товар: ' . $h1 
            . ' по цене ' . $product->price . ' руб, вы можете в интернет-магазине: bazaroza.ru. Подробности по тел.: 8 (383) 239-66-75';
        }
        
        $this->seoTags($product->meta);

		$deliveryProducts=Product::model()->findAll(['condition'=>'category_id='.(int)HYii::param('delivery_cat_id')]);

        $this->render('product', compact('product', 'categories', 'deliveryProducts'));
    }

    public function actionOrder()
    {
        $cart = CmsCart::getInstance();

        /*if ($cart->countAll() == 0) {
            $this->refresh();
        }*/

        $model = new Order('payment');
        //$model = new Order();

        $model->checkPayment();

        if (isset($_POST['Order'])) {
            $model->attributes = $_POST['Order'];

            if ($model->delivery != 2) {
                $model->area = '';
                $model->address = '';
            }

            if ($model->area) {
                $area = Area::model()->findByPk($model->area);
                $model->area = $area->title;
				
				if(((int)(CmsCart::getInstance()->priceAll())>= 3000) && ((int)($area->id) != 8) && ((int)($area->id) != 9)){
					$model->delivery_price = 0;
				}else{
					$model->delivery_price = $area->price;
				}
            }

            if ($model->notice) {
                $model->notice = implode('; ', $model->notice);
            }

            if (!empty($_POST['check_time']) && $_POST['check_time'] == 1) {
                $model->time = "Уточнить у отправителя";
            }
			if($model->comment){
				$model->comment = str_replace("amp;","",$model->comment);
				$model->comment = str_replace("&quot;","",$model->comment);
				$model->comment = str_replace("&amp;","",$model->comment);
			}
           if ($model->validate()) {
                $model->save(false);

                $messageAdmin = $this->renderPartial('_admin_email', compact('model'), true);
                $messageClient = $this->renderPartial('_client_email', compact('model'), true);

                if (CmsCore::sendMail($messageAdmin)) {
                    CmsCore::sendMail($messageClient, 'Заказ #'. $model->id .' на сайте '.Yii::app()->name, $model->email);

                    CmsCart::getInstance()->clear();
                    Yii::app()->user->setFlash('order', 'Спасибо, Ваш заказ отправлен!');

                    if ($action = $model->getPaymentAction()) {
                        if (isset($action['url'])) {
                            Yii::app()->user->setState('order_id', $model->id);
                            $this->redirect(array($action['url']));
                        }
                    }
                 } else
                     Yii::app()->user->setFlash('order', 'Ошибка отправки заказа');

                $this->redirect(array('orderSuccess'));
            }
        }

        $products = $cart->getResult(true);

        $this->prepareSeo('Оформление заказа');

        if (count($products)) {
            $this->render('order', compact('model', 'products'));
        } else
            $this->render('order_empty');
    }

    public function actionSetDelivery()
    {
        $cart = CmsCart::getInstance();

        $price = $cart->priceAll();
		
        if ($id = Yii::app()->request->getPost('id')) {
            $area = Area::model()->findByPk($id);

			if(((int)$price >= 3000) && ((int)($area->id) != 8) && ((int)($area->id) != 9)){
				$price = $price;
			}else{
				$price = $price + $area->price;
			}
        }

        echo $price;
    }
	
	public function actionSetDeliveryPrice()
    {
		$cart = CmsCart::getInstance();

        $price = $cart->priceAll();
		
		$areaPrice = 0; 
		
		if ($id = Yii::app()->request->getPost('id')) {
			$area = Area::model()->findByPk($id);
			if(((int)$price >= 3000) && ((int)($area->id) != 8) && ((int)($area->id) != 9)){
				$areaPrice = 0;
			}else{
				$areaPrice = $area->price;
			}
			
		}

        echo $areaPrice;
    }

    public function actionOrderSuccess()
    {
        $this->prepareSeo('Статус заказа');
        $this->render('order_success');
    }

    /**
     * Ajax adding products to cart
     * @param $id
     * @return void
     */
    public function actionAddToCart($id, $count=1)
    {
        $data = (array) Yii::app()->request->getPost('data');

        //$count = (int)Yii::app()->request->getPost('count', 1);

        $cart = CmsCart::getInstance();
        $cart->add($id, $count, $data);

        if (Yii::app()->request->isAjaxRequest) {
            echo $this->getJsonData($id);
            Yii::app()->end();
        }
        $this->redirect('/');
    }

    /**
     * Ajax update number products of cart
     * @return void
     */
    public function actionUpdateCart()
    {
        $counts = Yii::app()->request->getParam('count');

        if ($counts) {
            $cart = CmsCart::getInstance();

            $ids = array();

            foreach($counts as $id => $count) {
                $cart->update($id, intval($count));
                $ids[] = $id;
            }

            if (count($ids) == 1)
                echo $this->getJsonData($ids[0]);
            else
                echo json_encode(array());
        } else {
            //echo json_encode(array());
        }

        Yii::app()->end();
    }

    /**
     * Prepare Json update data
     * @param $id mixed
     * @return mixed
     */
    private function getJsonData($id, $count=1)
    {
        $cart = CmsCart::getInstance();

        $data = array();
        $data['id'] = $id;
        $data['count'] = $cart->count($id);
        $data['summary_count'] = $cart->countAll();
        $data['summary_price'] = $cart->priceAll();

        if ($cart->isFirstProduct) {
            $data['summary']  = $cart->getHtmlSummary();
            $data['products'] = $cart->getHtmlProducts();
        }

        if ($cart->isFirstItem) {
            $data['products'] = $cart->getHtmlProducts();
        }

        return json_encode($data);
    }

    public function actionClearCart()
    {
        CmsCart::getInstance()->clear();

        if (Yii::app()->request->isAjaxRequest)
            Yii::app()->end();
        else
            $this->redirect(array('index'));
    }

    public function actionPayment()
    {
        $order_id = Yii::app()->user->getState('order_id');

        if (!$order_id)
            $this->redirect(array('order'));

        $order = Order::model()->findByPk((int)$order_id);

        $this->prepareSeo('Оплата');
        $this->render('payment', compact('order'));
    }

    public function actionPayment_success()
    {
        $this->render('payment_success');
    }

    public function actionPayment_fail()
    {
        $this->render('payment_fail');
    }

}
