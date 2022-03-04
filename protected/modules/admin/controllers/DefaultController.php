<?php

class DefaultController extends AdminController
{
    // *** AJAX actions *** ///

    /**
     * Apply new order of menu items
     * @return void
     */
    public function actionMenuOrder()
    {
        $items = Yii::app()->request->getParam('item');
        MenuHelper::getInstance()->reorder($items);

        Yii::app()->end();
    }

    /**
     * Apply new order for images
     */
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

    function toint(&$val, $key) {
        $val = preg_replace("/[item_]/", "", $val);
    }

    public function actionShopOrder()
    {

        $category_id = Yii::app()->request->getParam('cat_id');
        $orders = Yii::app()->request->getParam('products');

        $c = new CDbCriteria;
        $c->condition = "category_id = :category_id";
        $c->params = array(":category_id" => $category_id);

        $products = Product::model()->findAll($c);
        
        array_walk($orders, array($this, "toint"));

        foreach($products as $i) {

            $i->ordering = array_search((int)$i->id, $orders) + 1;
            $i->save();
            
        }
        
        echo 'ok';
        Yii::app()->end();
    }

    public function actionSaveImageDesc()
    {
        if (Yii::app()->request->isAjaxRequest && count($_POST)) {
            $id   = (int) $_POST['id'];
            $desc = $_POST['desc'];

            $model = CImage::model()->findByPk($id);

            if ($model === null)
                throw new CHttpException(404, 'Изображение не найдено');

            $model->description = $desc;
            if ($model->save()) {
                echo 'ok';
            } else {
                echo 'error';
            }

            Yii::app()->end();
        }

        $this->redirect('index');
    }

    /*
     * Action for removing images
     */
    public function actionRemoveImage($id)
    {
        $model = CImage::model()->findByPk($id);

        if ($model === null)
            throw new CHttpException(404, 'Изображение не найдено');

        $status = $model->delete() ? 'ok' : 'error';

        if (Yii::app()->request->isAjaxRequest) {
            echo $status;
            Yii::app()->end();
        } else
            $this->redirect(array('/admin/'.$model->model.'/update', 'id'=>$model->item_id));
    }

    // *** ------------ *** ///
    
    public function actionLogin()
    {
        if (!Yii::app()->user->isGuest)
            $this->redirect(array('index'));

        $this->layout = 'login';

        $model = new LoginForm;

        if (isset($_POST['LoginForm'])) {
            $model->attributes = $_POST['LoginForm'];

            if ($model->validate() && $model->login())
                $this->redirect(Yii::app()->user->getReturnUrl(array('/admin/default/index')));
        }

        $this->pageTitle = 'Авторизация - '. $this->appName;
        $this->render('login',array('model'=>$model));
    }
    
    public function actionLogout()
    {
        Yii::app()->user->logout();
        $this->redirect($this->createUrl('index'));
    }

    public function actionIndex()
    {
        $this->render('index');
    }

    public function actionSettings()
    {
        $model = new SettingsForm();

        if (isset($_POST['SettingsForm'])) {
            $model->attributes = $_POST['SettingsForm'];

            if ($model->validate()) {
                $model->saveSettings();
                $this->refresh();
            }
        }
        $model->loadSettings();
        
        $this->render('settings', compact('model'));
    }

    public function actionClearImageCache()
    {
        $images   = CImage::model()->findAll();
        $products = array();

        foreach($images as $id=>$img) {
            $img->removeTmb();

            if ($img->model == 'product') {
                $products[] = $img;
                unset($images[$id]);
            }
        }

        $resizer = new UploadHelper();
        $resizer->createThumbnails($images);

        $params = array('max'=>100, 'master_side'=>4);
        if ($cropTop = Yii::app()->settings->get('shop_settings', 'cropTop')) {
            $params['crop']=true;
            $params['cropt_top']=$cropTop;
        }
        $resizer = new UploadHelper();
        $resizer->createThumbnails($products, $params);

        if (Yii::app()->request->isAjaxRequest) {
            echo 'ok';
            Yii::app()->end();
        } else
            $this->redirect(array('default/settings'));
    }

    public function actionError()
    {
        if($error=Yii::app()->errorHandler->error)
        {
            if(Yii::app()->request->isAjaxRequest)
                echo $error['message'];
            else
                $this->render('error', $error);
        }

    }

    public function actionExtUpdate()
    {
        /*$data = array(
            'username'=>'admin',
            'password'=>'dish_1234',
            'version'=>'1.2.6'
        );*/

        $data = Yii::app()->request->getPost('authData', array());

        $model = new LoginForm;
        $model->attributes = $data;

        $result = array('error'=>false, 'text'=>'', 'version'=>'');

        if (!$model->validate()) {
            $result['error'] = true;
            $result['text']  = 'Неверный логин или пароль';
            echo json_encode($result);
            Yii::app()->end();
        }

        $updater = new CmsUpdate($data['version']);
        $updater->update();

        $result['text']    = $updater->cmdResult ? $updater->cmdResult : '';
        $result['version'] = CmsUpdate::version();
        echo json_encode($result);
        Yii::app()->end();
    }

    public function actionGisMapDialog()
    {
        $request = Yii::app()->request;

        if ($request->isAjaxRequest) {
            $desc       = $request->getPost('description');
            $coors      = $request->getPost('coors');
            $marker_id  = $request->getPost('marker_id');
            $balloon_id = $request->getPost('balloon_id');

            $data = CJSON::encode(array(
                'coors'=>$coors,
                'desc'=>$desc
            ));

            Yii::app()->settings->set('markers', $coors, $data);

            echo json_encode(array(
                'result'=>'ok',
                'text'=>$desc,
                'marker_id'=>$marker_id,
                'balloon_id'=>$balloon_id
            ));
            Yii::app()->end();
        }

        $markers = Yii::app()->settings->get('markers');
        $params  = Yii::app()->settings->get('mapParams') or array();

        if ($markers) {
            $markers = array_values($markers);
            foreach($markers as $id=>$m) {
                $markers[$id] = json_decode($m);
            }
        } else {
            $markers = array();
        }

        $this->layout = 'clear';
        $this->pageTitle = 'Карта 2Гис';
        $assets = Yii::app()->assetManager->getPublishedUrl(Yii::getPathOfAlias('admin.widget.CmsEditor.assets'));

        $this->render('gismapdialog', compact('assets', 'markers', 'params'));
    }

    public function actionSaveMapParams()
    {
        $params = Yii::app()->request->getPost('mapParams');

        Yii::app()->settings->set('mapParams', $params);
        echo 'ok';
        Yii::app()->end();
    }

    public function actionGisMapRemoveMarker()
    {
        $coors = Yii::app()->request->getPost('coors');

        Yii::app()->settings->delete('markers', $coors);
        echo 'ok';
        Yii::app()->end();
    }

    public function actionCheckDb()
    {
        $dbUpdater = new CmsDbUpdate();

        if (Yii::app()->request->isAjaxRequest) {
            $dbUpdater->update(true);
            Yii::app()->end();
        }

        $query_list = $dbUpdater->update()->queryList();
        $this->render('check_db', compact('query_list'));
    }

    public function actionDeleteSettingFile()
    {
        if($attribute = Yii::app()->request->getPost('attribute')) {
            $filename = ModuleHelper::getParam($attribute, true);

            $delete = Yii::getPathOfAlias('webroot') . Yii::app()->params['uploadSettingsPath'] . $filename;
            
            if (file_exists($delete)) unlink($delete);
        }
    }
}
