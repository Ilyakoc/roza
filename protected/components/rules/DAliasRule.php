<?php
/**
 * Правило маршрутизации для моделей с поведением DAliasBehavior
 * 
 */
use AttributeHelper as A;

class DAliasRule extends CBaseUrlRule
{
	/**
	 * @var array массив конфигурации. 
	 * array(
	 * 	className=>array(
	 * 		'url'=>основная ссылка, 
	 * 		'replaceUrl'=>ссылка на которую замениться основная ссылка при формировании,
	 * 		если не указана, будет использована основная ссылка. 
	 * 		'attributeId'=>attributeId,
	 * 		'attributeAlias'=>attributeAlias,
	 * 		'module'=>имя модуля, если задан, будет проверяться подключен модуль или нет.
	 * )) 
	 * attributeId по умолчанию "id",
	 * attributeAlias по умолчанию "alias",
	 */
	public $config=array(
		'Event'=>array('url'=>'site/event', 'replaceUrl'=>'news'),
		'Blog'=>array('url'=>'site/blog'),
		'Page'=>array('url'=>'site/page'),
		'Product'=>array('url'=>'shop/product'),
		'Category'=>array('url'=>'shop/category'),
	);
	
	/**
	 * (non-PHPdoc)
	 * @see CBaseUrlRule::createUrl()
	 */
	public function createUrl($manager, $route, $params, $ampersand)
	{
		foreach($this->config as $className=>$cfg) {
	  		if ($route == $cfg['url']) {
	   			$id=$params['id'];
	   			$alias=$this->_getAliasById($className, $id);
	   			
	   			unset($params['id']);
				$url=empty($alias) ? sprintf(A::get($cfg, 'replaceUrl', $cfg['url']).'/%d', $id) : sprintf('%s', $alias);
	
	    		if(!empty($params)) 
	    			$url.='?' . $manager->createPathInfo($params, '=', $ampersand);
	    		
	    		return $url;
	  		}
		}

		return false;
	}

	/**
	 * (non-PHPdoc)
	 * @see CBaseUrlRule::parseUrl()
	 */
	public function parseUrl($manager, $request, $pathInfo, $rawPathInfo)
	{
		if(!$pathInfo) return false;

		if($pathInfo != 'index') {

		   $pathInfo=preg_replace('/^.*?([^\/]+)$/', '$1', $pathInfo);
//            		    var_dump($rawPathInfo);
//		    die();
		   if($pathInfo == 'index') return false;
		}
		

		foreach($this->config as $className=>$cfg) {
			if($rawPathInfo == ($cfg['url'].'/'.$pathInfo)) {
				if($alias=$this->_getAliasById($className, $pathInfo)) {
				    if($alias != '') {
                        $request->redirect('/'.$alias, true, 301);
                    }
				}
			}
			
			if ($id=$this->_getIdByAlias($className, $pathInfo)) {
				$_GET['id']=$id;
				return $cfg['url'];
			}
			elseif($replaceUrl=A::get($cfg, 'replaceUrl')) {
				if(preg_match("/^{$replaceUrl}\/(\d+)$/i", $pathInfo, $matches)) {
					$_GET['id']=$matches[1];
					return $cfg['url'];
				}
			}
		}
		
		return false;
	}
	
	/**
	 * Получение имени атрибута id модели
	 * @param string $className имя класса модели
	 * @return string
	 */
	private function _getAttributeId($className)
	{
		return A::get(A::get($this->config, $className, array()), 'attributeId', 'id');
	}
	
	/**
	 * Получение имени атрибута алиаса модели
	 * @param string $className имя класса модели
	 * @return string
	 */
	private function _getAttributeAlias($className)
	{
		return A::get(A::get($this->config, $className, array()), 'attributeAlias', 'alias');
	}
	
	/**
	 * Получение id модели по алиасу 
	 * @param string $className имя класса модели
	 * @param string $alias алиас модели.
	 * @return integer
	 */
	private function _getIdByAlias($className, $alias)
	{
		return (int)\Yii::app()->db->createCommand()
			->select($this->_getAttributeId($className))
			->from($className::model()->tableName())
			->where($this->_getAttributeAlias($className).'=:alias', array(':alias'=>$alias))
			->queryScalar();
	}

	/**
	 * Получение алиаса модели по id
	 * @param string $className имя класса модели
	 * @param integer $id id модели.
	 */
	private function _getAliasById($className, $id)
	{
		$fQuery=function($className, $id, $select, $method='queryScalar') {
			return \Yii::app()->db->createCommand()
				->select($select)
				->from($className::model()->tableName())
				->where($this->_getAttributeId($className).'=:id', array(':id'=>$id))
				->$method();
		};
		if($className == 'Page') {
			if($data=$fQuery($className, $id, 'alias, blog_id', 'queryRow')) {
				if(!empty($data['blog_id'])) {
					if($blogAlias=$fQuery('Blog', $data['blog_id'], 'alias')) {
						return $blogAlias.'/'.$data['alias'];
					}
				}
				return $data['alias'];
			}
		}
		else {
			return $fQuery($className, $id, $this->_getAttributeAlias($className));
		}
		return null;
	}
}
