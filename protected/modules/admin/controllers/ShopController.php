<?php

class ShopController extends AdminController
{
	/**
	 * (non-PHPdoc)
	 * @see AdminController::filters()
	 */
	public function filters()
	{
		return CMap::mergeArray(parent::filters(), array(
			'ajaxOnly +removeRelatedCategory'
		));
	}

    public function actionProductCategoryChange()
    {
        $from = (int) Yii::app()->request->getQuery('from');
        $to = (int) Yii::app()->request->getPost('to');
        $productIDs = Yii::app()->request->getPost('product');


        if ($to && $productIDs) {
            $productCriteria = new CDbCriteria();
            $productCriteria->addInCondition('id', $productIDs);

            Product::model()->updateAll(['category_id' => $to], $productCriteria);

            Yii::app()->user->setFlash('message', 'Товары успешно перенесены.');
            $this->refresh();
        }

        $criteria = new CDbCriteria();
        $criteria->addColumnCondition(['category_id' => $from]);
        $criteria->select = 'id, title, category_id';
        $criteria->order = 'title';

        $products = Product::model()->findAll($criteria);

        $this->render('product_category_change', compact('to', 'from', 'products'));
    }

    public function actionCategorySort()
    {
        if(Yii::app()->request->isAjaxRequest) {
            $data=json_decode(Yii::app()->request->getPost('data', json_encode(array())));
            if(is_array($data)) {
                $cases=array('ordering'=>'', 'root'=>'','lft'=>'','rgt'=>'','level'=>'');
                foreach($data as $item) {
                    array_walk($cases, function(&$expression,$attribute) use ($item) { 
                        $expression.=' WHEN '.(int)$item->id.' THEN '.(int)$item->$attribute;
                    });
                }
                array_walk($cases, function(&$expression,$attribute) {
                    $expression="`t`.`{$attribute}`=CASE `t`.`id` {$expression} ELSE `t`.`{$attribute}` END";
                });
                
                $query='UPDATE `'.Category::model()->tableName().'` as `t` SET '.implode(',',$cases);
                Category::model()->getDbConnection()
                    ->createCommand($query)
                    ->execute();
                echo CJSON::encode(array('success'=>true));
            }
            else {
                echo CJSON::encode(array('success'=>false));
            } 
            Yii::app()->end();
            die();
        }
        $this->pageTitle = 'Каталог / Сортировка категорий';

        $this->render('category_sort');
    }
	
	public function actionRemoveRelatedCategory()
	{
		$data = Yii::app()->request->getPost('data');
		
		if($data){
			RelatedCategory::model()->deleteAll(array('condition'=>'category_id = ' . $data['related'] . ' AND product_id = ' . $data['product']));
		}
		
		Yii::app()->end();
	}
	
    public function actionIndex()
    {
        $categories = $this->getCategories();

        $products   = Product::model()->findAll(array('order'=>'id DESC', 'limit'=>16));
        $orders     = Order::model()->findAll(array('order'=>'id DESC'));

        $this->render('index', compact('categories', 'products', 'orders'));
    }

    public function actionCategory($id)
    {

        $categories = $this->getCategories($id);
        $bredcrumbs = $this->getBreadcrumbs($id);

        $model = $this->loadCategory($id);
        
        if(!$model)
            throw new CHttpException(404, "Not found");


        $c = new CDbCriteria;
        $c->order = "new DESC, ordering ASC, id DESC";
        $c->condition = "category_id = :model_id";
        $c->params = array(':model_id' => $model->id);
        $products   = Product::model()->findAll($c);
        
        $this->render('category', compact('model', 'categories', 'bredcrumbs', 'id', 'products'));
    }

    /* --- Product CRUD --- */
    public function actionProductCreate($category_id = null)
    {
        $model = new Product();

        if (isset($_POST['Product'])) {
            $model->attributes = $_POST['Product'];

            if ($model->save()) {
                if(isset($_POST['EavValue'])){
                    foreach ($_POST['EavValue'] as $key => $value) {
                        if (!$value) {
                            continue;
                        }
                        $attributesProduct = new EavValue;
                        $attributesProduct->id_attrs = $key;
                        $attributesProduct->id_product = $model->id;
                        $attributesProduct->value = $value;
                        $attributesProduct->save();
                    }
                }

                $this->redirect(array('index'));
            }
        }

        if ($category_id)
            $model->category_id = $category_id;

        $fixAttributes = array();

        if(Yii::app()->params['attributes']){
            $criteria = new CDbCriteria;
            $criteria->condition = "fixed = 1";

            $fixAttributes = EavAttribute::model()->findAll($criteria);
        }
        
        $this->render('productcreate', compact('model', 'fixAttributes'));
    }
    public function actionProductUpdate($id)
    {
        $model = $this->loadProduct($id);

        if (isset($_POST['Product'])) {
            $model->attributes = $_POST['Product'];

            if ($model->save()) {
                if(isset($_POST['EavValue']) && $_POST['EavValue']){
                    EavValue::model()->deleteAll('id_product = ' . $model->id);

                    foreach ($_POST['EavValue'] as $key => $value) {
                        if (!$value) {
                            continue;
                        }

                        $attributesProduct = new EavValue;
                        $attributesProduct->id_attrs = $key;
                        $attributesProduct->id_product = $model->id;
                        $attributesProduct->value = $value;
                        $attributesProduct->save();

                    }
                }

            	$additionalCategories = Yii::app()->request->getPost('relatedCategories');
            	
            	if($additionalCategories){
            		foreach ($additionalCategories as $id => $catID) {
            			$relatedCategory = new RelatedCategory;
            			$relatedCategory->product_id = $model->id;
            			$relatedCategory->category_id = $catID;
            			$relatedCategory->save();
            		}
            	}
            	
                $this->refresh();
            }
        }
        
        // Дополнительные категории
        if($model->isNewRecord) {
        	$categoryList=$relatedCategories=[];
        }
        else {
        	$relatedCategoriesIDs = array();
        	
        	foreach ($model->relatedCategories as $key => $value) {
        		$relatedCategoriesIDs[] = $value['category_id'];
        	}
        	
        	$relatedCategoriesIDs[] = $model->category_id;
        	
        	$categoryCriteria = new CDbCriteria;
        	$categoryCriteria->select = 'id, title';
        	$categoryCriteria->addNotInCondition('id', $relatedCategoriesIDs);
        	
        	$categoryList = Category::model()->findAll($categoryCriteria);
        	
        	$categoryCriteriaProduct = new CDbCriteria;
        	$categoryCriteriaProduct->select = 'id, title';
        	$categoryCriteriaProduct->addInCondition('id', $relatedCategoriesIDs);
        	$relatedCategories = Category::model()->findAll($categoryCriteriaProduct);
        }

        $fixAttributes = array();

        if(Yii::app()->params['attributes']){
            $criteria = new CDbCriteria;
            $criteria->condition = "fixed = 1";

            $fixAttributes = EavAttribute::model()->findAll($criteria);
        }

        $this->render('productupdate', compact('model', 'categoryList', 'relatedCategories', 'fixAttributes'));
    }

    public function actionThumbsUpdate($id)
    {
        $model = $this->loadProduct($id);

        if (isset($_POST['Product'])) {
            $model->attributes = $_POST['Product'];

            if ($model->save()) {
                $this->refresh();
            }
        }

        $this->render('thumbsupdate', compact('model'));
    }

    public function actionProductDelete($id)
    {
        $model = $this->loadProduct($id);
        
        $categoryId=$model->category_id;
        
        $model->delete();

        $this->redirect(array('shop/category', 'id'=>$categoryId));
    }

    

    /* --- Category CRUD --- */
    public function actionCategoryCreate($parent_id = null)
    {
        $model = new Category();

        if (isset($_POST['Category'])) {
            $model->attributes = $_POST['Category'];

            if ($parent_id) {
                $parent = Category::model()->findByPk($parent_id);
                $model->appendTo($parent);
                $this->redirect(array('shop/category', 'id'=>$parent_id));
            } else {
                $model->saveNode();
                $this->redirect(array('index'));
            }
        }

        $this->render('categorycreate', compact('model'));
    }
    public function actionCategoryUpdate($id)
    {
        $model = $this->loadCategory($id);

        if (isset($_POST['Category'])) {
            $model->attributes = $_POST['Category'];
            
            if ($model->saveNode()) {
                $this->refresh();
            }
        }

        $this->render('categoryupdate', compact('model'));
    }
    public function actionCategoryDelete($id)
    {
        $model = $this->loadCategory($id);
        $model->deleteNode();

        $this->redirect(array('shop/index'));
    }


    private function loadProduct($id)
    {
        $model = Product::model()->findByPk((int) $id);
        if ($model === null)
            throw new CHttpException(404, 'Продукт не найден');
        return $model;
    }
    private function loadCategory($id)
    {
        $model = Category::model()->findByPk((int) $id);
        if ($model === null)
            throw new CHttpException(404, 'Категория не найдена');
        return $model;
    }


    /** ---  */
    public function actionRemoveMainImg()
    {
        $status = 0;

        if (isset($_POST['product_id'])) {
            Product::model()->removeMainImage($_POST['product_id']);
            $status = 1;
        }
        echo $status;
        Yii::app()->end();
    }

    public function actionClearImageCache()
    {
        Product::model()->clearImageCache();

        if (Yii::app()->request->isAjaxRequest) {
            echo 'ok';
            Yii::app()->end();
        }
        $this->redirect(array('shop/index'));
    }

    public function actionCategoryOrder()
    {
        $orders = Yii::app()->request->getParam('shop-category');

        $categories = Category::model()->findAllByPk($orders);

        foreach($categories as $c) {
            $c->ordering = array_search($c->id, $orders) + 1;
            $c->saveNode();
        }

        echo 'ok';
        Yii::app()->end();
    }

    /**
     * @param null $parent
     * @return mixed
     */
    private function getCategories($parent = null)
    {
        if ($parent) {
            $category = Category::model()->findByPk($parent);
            return $category->children()->findAll();
        }

        return Category::model()->roots()->findAll(array('order'=>'ordering'));
    }

    private function getBreadcrumbs($id)
    {
        $category = Category::model()->findByPk($id);
        $parents = $category->ancestors()->findAll();

        $result = array();
        foreach($parents as $p) {
            $result[] = CHtml::link($p->title, array('shop/category', 'id'=>$p->id));
        }
        return $result;
    }

    public function actionResize() {
        Yii::import('ext.EJCropper');
        $jcropper = new EJCropper();
        $jcropper->thumbPath = Yii::getPathOfAlias('webroot.images.product');
         
        $jcropper->jpeg_quality = 95;
        $jcropper->png_compression = 8;
         
        // get the image cropping coordinates (or implement your own method)
        $coords = $jcropper->getCoordsFromPost();
         
        // returns the path of the cropped image, source must be an absolute path.
        $src = mb_strpos($_POST['src'], '?') ? Yii::getPathOfAlias('webroot').mb_strcut($_POST['src'], 0, mb_strpos($_POST['src'], '?')) : Yii::getPathOfAlias('webroot').$_POST['src'];
        $dst = mb_strpos($_POST['dst'], '?') ? Yii::getPathOfAlias('webroot').mb_strcut($_POST['dst'], 0, mb_strpos($_POST['dst'], '?')) : Yii::getPathOfAlias('webroot').$_POST['dst'];
        $thumbnail = $jcropper->crop($src, $dst, $coords);
    }

    //Клонирование продукта
    public function actionProductClone($id){
        $model = $this->loadProduct($id);
        $cloned_product = new Product;
        $cloned_product->attributes = $model->attributes;
        $cloned_product->title = $model->title."_копия";
        $cloned_product->alias = $model->alias."-".mktime();
        // $cloned_product->created=new \CDbExpression('NOW()');
        // $cloned_product->ordering=0;
        //Если продукт сохранен, то начинаем работу с картинками.
        //Объявляем хелпер.
        $fhelp = new CFileHelper;
        //Получаем изображения.
        $id = $model->id;
        $files_to_copy = glob('images/product/{'.$id.','.$id.'_*}.*', GLOB_BRACE); 
        //Если продукт склонировался выполняем нужные действия
        if($cloned_product->save()){
            if(!empty($files_to_copy)) {
                foreach ($files_to_copy as $key => $file) {
                    $ext = $fhelp->getExtension($file);
                    $tmp = explode('/', $file);
                    $tmp = explode('.', $tmp[2]);
                    $tmp = explode('_', $tmp[0]);
                    if(isset($tmp[1])){
                        copy( $file, 'images/product/'.$cloned_product->id.'_'.$tmp[1].'.'.$ext); 
                    }
                    else{

                        copy( $file, 'images/product/'.$cloned_product->id.'.'.$ext);

                    }
                }
            }
            //Обработка дополнительных фотографий.
            $imgages = CImage::model()->findAll(array('condition'=>"item_id = $model->id"));
            if($imgages) {
                foreach ($imgages as $key => $img) {
                    $new_image = new CImage;
                    $new_image->attributes = $img->attributes;
                    $uid = uniqid();
                    $ext = $fhelp->getExtension('/images/product/'.$img->filename);
                    $fname = $uid.'.'.$ext;
                    $new_image->filename = $fname;
                    $new_image->item_id = $cloned_product->id;
                    if(copy('images/product/'.$img->filename, 'images/product/'.$fname)){
                        $new_image->save();
                    }
                }
            }
            $url = $this->createUrl('shop/productupdate', array('id'=>$cloned_product->id));
            $this->redirect($url);
        }
    }

}
