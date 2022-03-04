<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Rick
 * Date: 18.05.11
 * Time: 10:54
 * To change this template use File | Settings | File Templates.
 */

class MenuHelper
{
    private $_items = null;

    /**
     * @var MenuHelper;
     */
    static $instance = null;

    /**
     * Menu static instance
     * @static
     * @return MenuHelper
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * @return object
     */
    private function getItems()
    {
        if ($this->_items == null) {
            $criteria = new CDbCriteria();
            $criteria->order = 'ordering';
            $this->_items = Menu::model()->findAll($criteria);
        }

        return $this->_items; 
    }

    private function getNextOrder()
    {
        $items = $this->getItems();
        $max   = $items[count($items)-1]->ordering;

        return $max < 0 ? 1 : $max + 1;
    }

    public function getItem($model, $item_id = null)
    {
        $items = $this->getItems();

        if (is_object($model)) {
            $item_id = $model->id;
            $model   = get_class($model);
        }

        foreach($items as $item) {
            if ($item->model   == strtolower($model) &&
                $item->item_id == $item_id)
                return $item;
        }

        return false;
    }

    public function find($params = array())
    {
        if (!$params)
            return false;

        $items = $this->getItems();

        foreach($items as $item) {
            $search = true;

            foreach($params as $name=>$value) {
                if ($item->$name != $value)
                    $search = false;
            }
            
            if ($search)
                return $item;
        }
        return false;
    }

    /**
     * @return array
     */
    public function getSiteMenu()
    {
        $items = $this->getItems();

        $result = array();

        /*$limit = Yii::app()->params['menu_limit'];

        foreach($items as $item) {
            if ($item->hidden)
                continue;

            if ($limit && count($result) == $limit)
                break;

            $params = (array) json_decode($item->options);

            if (!$item->item_id || $params['action'] == 'index') {
                $action = $params['action'];
            } else {
                $action = $item->model;
            }

            $action = 'site/'. $action;
            unset($params['action']);

            $route = array_merge(array($action), $params);

            $result[] = array(
                'label'=>$item->title,
                'url'=>$route
            );
        }*/

        return $result;
    }

    public function getAdminMenu()
    {
        $items = $this->getItems();

        $result = array();

        foreach($items as $item) {
            $action = '/admin/'. $item->model;

            if ($item->item_id) {
                $action .=  '/update';
                $params = array('id'=>$item->item_id);
            } else {
                $action .= '/index';
                $params = array();
            }

            $route = array_merge(array($action), $params);

            $options = array('id'=>'item-'.$item->id);
            if ($item->ordering < 0)
                $options['class'] = 'ui-state-disabled';

            $result[] = array(
                'label'=>$item->title,
                'url'=>$route,
                'itemOptions'=>$options,
            );
        }

        return $result;
    }

    public function addItem(& $model)
    {
        $model_name = strtolower(get_class($model));

        $item = new Menu();
        $item->title   = $model->title;
        $item->model   = $model_name;
        $item->item_id = $model->id;

        $jdata = array('action'=>$model_name);
        if (isset($model->alias))
            $jdata['alias'] = $model->alias;

        $item->options = json_encode($jdata);
        $item->ordering = $this->getNextOrder();

        if (!$item->save()) {
            throw new CHttpException('500', 'Ошибка создания пункта меню');
        }

        return $this;
    }

    public function updateItem(& $model)
    {
        $model_name = strtolower(get_class($model));

        $item = $this->getItem($model_name, $model->id);

        if ($item) {
            $params = new stdClass;

            if ($model->mainpage) {
                $params->action = 'index';
            } else {
                $params->action = $model_name;
                $params->alias  = $model->alias;
            }

            $item->title   = $model->mainpage ? 'Главная страница' : $model->title;
            $item->options = json_encode($params);
            $item->save();
        }

        return;
    }

    public function removeItem(& $model, $modelName)
    {
        $item = $this->getItem($modelName, $model->id);

        if ($item)
            $item->delete();

        return $this;
    }

    public function reorder($orders = array())
    {
        $items = $this->getItems();
        $reset = !$orders || !is_array($orders) ? true : false;
        $i = 1;

        foreach($items as $item) {
            if ($item->ordering > 0) {
                $inx = !$reset ? array_search($item->id, $orders) : $i++ ;
                $item->ordering = $inx + 1;
                $item->save();
            }
        }
    }
}
