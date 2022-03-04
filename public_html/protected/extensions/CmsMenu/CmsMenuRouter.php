<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Alexok
 * Date: 08.09.11
 * Time: 16:19
 */

class CmsMenuRouter
{
    private $url_cache = null;

    /**
     * @var CmsMenuRouter
     */
    public static $instance = null;

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /* CUrlManager methods */
    public function createUrl($route, & $params)
    {
        /*preg_match('%^(\w+)/(\w+)$%', $route, $matches);
        $controller = $matches[1];
        $action     = $matches[2];*/

        if ($model_name = $this->findRoute($route)) {
            if (isset($params['id'])) {
                $id = (int) $params['id'];
                unset($params['id']);
                return $this->findModel($model_name, $id);
            }
        }

        return false;
    }

    public function parseUrl($path)
    {
        if ($model = $this->findModelByAlias($path)) {
            $model_name = strtolower(get_class($model));

            $routes = CmsMenuHelper::getRoutes('site');
            $data   = array();

            if (isset($routes[$model_name])) {
                $url = $routes[$model_name]['one'];

                if (isset($url)) {
                    $data['route']      = $url;
                    $data['attributes'] = array('id'=>$model->id);
                }
            }

            return $data;
        }
        return false;
    }

    /**
     * @param $search
     * @return Menu|bool
     */
    private function findModelByAlias($search)
    {
        foreach($this->getUrlCache() as $model) {
            if (key_exists($search, $model)) {
                return $model[$search];
            }
        }

        return false;
    }

    private function findRoute($route)
    {
        $siteRoutes = CmsMenuHelper::getRoutes('site');

        foreach($siteRoutes as $model=>$routes) {
            if (in_array($route, $routes)) {
                return $model;
            }
        }

        return false;
    }

    private function findModel($model_name, $id) {
        $models = $this->getUrlCache();

        if (!isset($models[$model_name])) {
            return false;
        }

        foreach($models[$model_name] as $alias=>$model) {
            if ($model->id == $id) {
                return $alias;
            }
        }

        return false;
    }

    private function loadAliases($model_name)
    {
        $model = ucfirst($model_name);

        /* php 5.2 fix */
        if (version_compare(phpversion(), '5.3') === 1) {
            $items = $model::model()->findAll();
        } else {
            $model = new $model(null);
            $items = $model->model()->findAll();
        }

        $classname = $model.'AliasLoader';

        if (class_exists($classname, false)) {
            $loader = new $classname;
            $result = $loader->load($items);
        } else {
            $result = array();

            foreach($items as $item) {
                $result[$item->alias] = $item;
            }
        }

        $this->url_cache[$model_name] = $result;
    }

    private function getUrlCache()
    {
        if ($this->url_cache == null) {
            $this->loadAliases('page');
            $this->loadAliases('blog');
        }
        return $this->url_cache;
    }
}

class PageAliasLoader
{
    public function __construct()
    {
        return $this;
    }

    public function load($items)
    {
        $result = array();

        foreach($items as $item) {
            $alias = $item->blog_id ? $item->blog->alias. '/' .$item->alias : $item->alias;
            $result[$alias] = $item;
        }

        return $result;
    }
}
