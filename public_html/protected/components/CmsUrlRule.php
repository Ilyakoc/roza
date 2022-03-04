<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Rick
 * Date: 08.09.11
 * Time: 17:58
 * To change this template use File | Settings | File Templates.
 */
 
class CmsUrlRule extends CBaseUrlRule
{
    public function createUrl($manager, $route, $params, $ampersand)
    {
        $router = CmsMenuRouter::getInstance();

        if ($path = $router->createUrl($route, $params)) {
            if ($params) {
                $path .= '?'. $manager->createPathInfo($params, '=', $ampersand);
            }
            return $path;
        }
        return false;
    }

    public function parseUrl($manager, $request, $pathInfo, $rawPathInfo)
    {
        $router = CmsMenuRouter::getInstance();

        if ($data = $router->parseUrl($pathInfo)) {
            if (isset($data['attributes'])) {
                $this->setGetParams($data['attributes']);
            }
            return $data['route'];
        }
        return false;
    }


    private function setGetParams($params = array())
    {
        if (!count($params)) return;

        foreach($params as $name=>$value) {
            $_GET[$name] = $value;
        }
    }
}
